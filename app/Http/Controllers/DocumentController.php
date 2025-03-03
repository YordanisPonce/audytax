<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\Fase;
use App\Models\Status;
use App\Traits\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class DocumentController extends Controller
{
    use Upload;
    public function __construct()
    {
        $this->authorizeResource(Document::class, 'document');
        //$this->middleware('is_admin', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $faseId = $request->get('fase');
        $fase = Fase::find($faseId);
        $auditoryType = $fase->auditoryType;
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
                'name' => 'Create',
                'url' => '#',
                'active' => true
            ],
        ];
        
        // Ocultar el enlace 'Auditory Type' para clientes
        if (auth()->user()->hasRole('client')) {
            array_shift($breadcrumbsItems);
        }
        $qualityControl = $fase->qualityControl;
        return view('documents.create', [
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => __('Documents'),
            'fases' => Fase::all(),
            'statuses' => Status::all(),
            'qualityControl' => $qualityControl ?: null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentRequest $request)
    {
        $request['url'] = $this->upload($request->doc, 'documents');
        $fase = Fase::find($request->fase_id);
        $request['quality_control_id'] = $fase->qualityControl->id ?? null;
        $document = Document::create($request->only('name', 'url', 'fase_id', 'description', 'quality_control_id', 'status_id'));
        
        // Redireccionar al tipo de auditoría en lugar de la fase para ocultar las fases en la URL
        $auditoryType = $fase->auditoryType;
        return redirect()->route('auditoryTypes.show', ['auditoryType' => $auditoryType])->with('message', 'Documento agregado satisfactoriamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Document $document)
    {
        $faseId = $request->get('fase');
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
                'name' => 'Edit',
                'url' => '#',
                'active' => true
            ],
        ];
        
        // Ocultar el enlace 'Auditory Type' para clientes
        if (auth()->user()->hasRole('client')) {
            array_shift($breadcrumbsItems);
        }
        $qualityControl = null;
        $fase = Fase::find($faseId);
        if ($fase) {
            $qualityControl = Fase::find($faseId)->qualityControl;
        }

        return view('documents.edit', [
            'document' => $document,
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => 'Editar',
            "fases" => Fase::all(),
            "statuses" => Status::all(),
            'qualityControl' => $qualityControl,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentRequest $request, Document $document)
    {
        $request['url'] = $this->updateFile($request->doc, 'documents', $document->url);
        $document->update($request->only($document->getFillable()));
        
        // Redireccionar al tipo de auditoría en lugar de la fase para ocultar las fases en la URL
        $auditoryType = $document->fase->auditoryType;
        return redirect()->route('auditoryTypes.show', ['auditoryType' => $auditoryType])->with('message', 'Documento actualizado satisfactoriamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        $this->removeFile($document->url);
        $fase = $document->fase;
        $auditoryType = $fase->auditoryType;
        $document->delete();
        
        // Si la solicitud viene de una URL que contiene 'fase', redirigir al tipo de auditoría
        if (request()->is('*fase*')) {
            return redirect()->route('auditoryTypes.show', ['auditoryType' => $auditoryType])->with('message', 'Documento eliminado satisfactoriamente');
        }
        
        return redirect()->back()->with('message', 'Documento eliminado satisfactoriamente');
    }

    public function download(Document $document)
    {
        Gate::authorize('download', $document);
        $file = public_path() . '/storage/' . $document->url;
        $extension = pathinfo($document->url, PATHINFO_EXTENSION);
        try {
            return response()->download($file, $document->name . '.' . $extension);
        } catch (FileNotFoundException $th) {
            return redirect()->back()->with('message', 'No se encuentra documento');
        }
    }

    public function getDocumentsByFaseId($faseId)
    {/* 
        $fase = Fase::find($faseId);
        $breadcrumbsItems = [
            [
                'name' => 'Detalles',
                'url' => route('qualityControls.index'),
                'active' => true
            ]
        ];
        //  Gate::authorize('getDocumentsByFaseId', $fase);
        return view('documents.details', [
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => 'Documentos ' . $fase->name,
            'documents' => $fase->documents()->with('status')->get(),
            'fase' => $fase,
        ]); */
    }

    public function saveFiles(Request $request, $faseId)
    {
        if ($request->hasFile('files')) {
            $fase = Fase::find($faseId);
            $status = Status::where('key', 'processing')->first();
            foreach ($request->file('files') as $key => $value) {
                $orignalName = $value->getClientOriginalName();
                $path = $this->upload($value, 'documents');
                $document = Document::find($key);
                $document->update(['url' => $path, 'status_id' => $status->id, 'original_name' => $orignalName]);
            }
            
            // Redireccionar al tipo de auditoría si existe
            if ($fase && $fase->auditoryType) {
                return redirect()->route('auditoryTypes.show', ['auditoryType' => $fase->auditoryType])->with('message', 'Archivos subidos satisfactoriamente');
            }
        }

        return redirect()->back();
    }

    public function markAsComplete(Document $document)
    {
        $status = Status::where('key', 'complete')->first();
        if (!$status)
            return redirect()->back();

        $document->update(['status_id' => $status->id]);
        
        // Si la solicitud viene de una URL que contiene 'fase', redirigir al tipo de auditoría
        if (request()->is('*fase*') && $document->fase && $document->fase->auditoryType) {
            return redirect()->route('auditoryTypes.show', ['auditoryType' => $document->fase->auditoryType])->with('message', 'Documento marcado como completo');
        }
        
        return redirect()->back();
    }

    public function cancelDocument(Document $document)
    {
        $status = Status::where('key', 'waiting')->first();
        if (!$status)
            return redirect()->back();

        $this->removeFile($document->url);
        $document->update(['status_id' => $status->id, 'url' => null]);
        
        // Si la solicitud viene de una URL que contiene 'fase', redirigir al tipo de auditoría
        if (request()->is('*fase*') && $document->fase && $document->fase->auditoryType) {
            return redirect()->route('auditoryTypes.show', ['auditoryType' => $document->fase->auditoryType])->with('message', 'Documento cancelado');
        }
        
        return redirect()->back();
    }
}
