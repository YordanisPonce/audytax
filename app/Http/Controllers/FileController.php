<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\File;
use App\Models\User;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class FileController extends Controller
{
    use Upload;
    
    /**
     * Display a listing of all files organized by client (admin only).
     *
     * @return \Illuminate\Http\Response
     */
    public function adminIndex()
    {
        // Verificar que solo los administradores puedan acceder
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->route('dashboard.index')->with('error', 'No tienes permiso para acceder a esta página');
        }
        
        // Obtener todos los archivos agrupados por cliente (user_id)
        $files = File::with(['user', 'document.auditoryType'])->orderBy('user_id')->get();
        $filesByClient = $files->groupBy('user_id');
        
        return view('files.admin', [
            'filesByClient' => $filesByClient,
            'pageTitle' => 'Archivos por Cliente'
        ]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $documentId = $request->get('document');
        $document = Document::find($documentId);
        
        if (!$document) {
            return redirect()->back()->with('error', 'Documento no encontrado');
        }
        
        $auditoryType = $document->fase->auditoryType;
        $breadcrumbsItems = [
            [
                'name' => 'Auditory Type',
                'url' => route('auditoryTypes.index'),
                'active' => false
            ],
            [
                'name' => $auditoryType->name,
                'url' => route('auditoryTypes.show', ['auditoryType' => $auditoryType]),
                'active' => false
            ],
            [
                'name' => 'Archivos',
                'url' => '#',
                'active' => true
            ],
        ];
        
        // Ocultar el enlace 'Auditory Type' para clientes
        if (auth()->user()->hasRole('client')) {
            array_shift($breadcrumbsItems);
        }
        
        // Filtrar archivos según el rol del usuario
        $files = [];
        if (auth()->user()->hasRole('admin')) {
            // Administradores ven todos los archivos del documento agrupados por cliente
            $files = $document->files()->with('user')->orderBy('user_id')->get();
            // Agrupar archivos por usuario para la vista
            $filesByUser = $files->groupBy('user_id');
        } else {
            // Clientes solo ven sus propios archivos
            $files = $document->files()->where('user_id', auth()->id())->get();
            $filesByUser = null;
        }
        
        return view('files.index', [
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => 'Archivos del documento: ' . $document->name,
            'document' => $document,
            'files' => $files,
            'filesByUser' => $filesByUser ?? null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'document_id' => 'required|exists:documents,id'
        ]);
        
        $document = Document::find($request->document_id);
        if (!$document) {
            return redirect()->back()->with('error', 'Documento no encontrado');
        }
        
        $uploadedFile = $request->file('file');
        $originalName = $uploadedFile->getClientOriginalName();
        $path = $this->upload($uploadedFile, 'files');
        
        $file = new File([
            'name' => pathinfo($originalName, PATHINFO_FILENAME),
            'url' => $path,
            'original_name' => $originalName,
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'is_approved' => false // Por defecto, los archivos requieren aprobación
        ]);
        
        $file->save();
        
        // Cambiar el estado del documento a 'procesando' si tiene al menos un archivo
        if ($document->files()->count() > 0 && $document->isWaiting()) {
            $processingStatus = \App\Models\Status::where('key', 'processing')->first();
            if ($processingStatus) {
                $document->status_id = $processingStatus->id;
                $document->save();
            }
        }
        
        return redirect()->back()->with('message', 'El archivo se ha enviado para revisión');
    }

    /**
     * Download the specified file.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function download(File $file)
    {
        // Verificar permisos: solo el propietario o administradores pueden descargar
        if (auth()->user()->hasRole('admin') || $file->user_id == auth()->id()) {
            $filePath = public_path() . '/storage/' . $file->url;
            $extension = pathinfo($file->url, PATHINFO_EXTENSION);
            
            try {
                return response()->download($filePath, $file->name . '.' . $extension);
            } catch (FileNotFoundException $th) {
                return redirect()->back()->with('error', 'No se encuentra el archivo');
            }
        }
        
        return redirect()->back()->with('error', 'No tienes permiso para descargar este archivo');
    }

    /**
     * Approve a file
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function approveFile(File $file)
    {
        // Solo administradores pueden aprobar archivos
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'No tienes permiso para aprobar archivos');
        }
        
        $file->update(['is_approved' => true]);
        
        return redirect()->back()->with('message', 'Archivo aprobado');
    }
    
    /**
     * Reject a file approval
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function rejectFile(File $file)
    {
        // Solo administradores pueden rechazar archivos
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'No tienes permiso para rechazar archivos');
        }
        
        $file->update(['is_approved' => false]);
        
        return redirect()->back()->with('message', 'Aprobación de archivo rechazada');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\File  $file
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {
        // Verificar permisos: solo el propietario o administradores pueden eliminar
        if (auth()->user()->hasRole('admin') || $file->user_id == auth()->id()) {
            $this->removeFile($file->url);
            $file->delete();
            return redirect()->back()->with('message', 'Archivo eliminado satisfactoriamente');
        }
        
        return redirect()->back()->with('error', 'No tienes permiso para eliminar este archivo');
    }
}