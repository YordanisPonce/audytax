<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
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
    $fase = Fase::findOrFail($faseId);
    $qualityControl = $fase->qualityControl;
        if ($qualityControl) {
        $breadcrumbsItems = [
            [
                'name' => 'Auditoría',
                'url' => route('qualityControls.index'),
                'active' => false,
            ],
            [
                'name' => 'Fases',
                'url' => route('qualityControls.show', $qualityControl),
                'active' => false,
            ],
            [
                'name' => 'Crear',
                'url' => '#',
                'active' => true,
            ],
        ];
    } else {
        $breadcrumbsItems = [
            [
                'name' => 'Plantilla de Auditoría',
                'url' => route('auditoryTypes.index'),
                'active' => false,
            ],
            [
                'name' => 'Fases',
                'url' => route('auditoryTypes.show', $fase->auditoryType),
                'active' => false,
            ],
            [
                'name' => 'Crear',
                'url' => '#',
                'active' => true,
            ],
        ];
    }
        return view('documents.create', [
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => __("Documents"),
            "fases" => Fase::all(),
            "statuses" => Status::all(),
            "qualityControl" => $qualityControl ?: null
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
        
        $qcId = $fase->qualityControl->id ?? null;

         // Si lo crea admin/consultor, estado OPEN; si lo sube cliente, esperando revisión
        $initialKey = auth()->user()->hasRole('client')
                    ? StatusEnum::WaitingReview->value
                    : StatusEnum::Open->value;

        $statusId = Status::where('key',$initialKey)->first()->id;

        $document = Document::create([
            'name'               => $request->name,
            'url'                => $request['url'],
            'description'        => $request->description,
            'fase_id'            => $fase->id,
            'quality_control_id' => $qcId,
            'status_id'          => $statusId,
        ]);

        // Si viene desde plantilla
    if ($request->has('auditorytype')) {
        return redirect()
            ->route('auditoryTypes.show', ['auditoryType' => $request->get('auditorytype')])
            ->with('message', 'Documento agregado satisfactoriamente');
    }

    // Si viene desde control de calidad
     // Redirección si viene de Control de Calidad
    if ($request->filled('qualityControl')) {
        return redirect()
            ->route('qualityControls.show', $qcId)
            ->with('message', 'Documento agregado satisfactoriamente');
    }

    // Redirección por defecto
    return redirect()
        ->route('fases.show', ['fase' => $document->fase->id])
        ->with('message', 'Documento agregado satisfactoriamente');
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
         $fase = Fase::findOrFail($faseId);
        $qc = $fase->qualityControl;

         // igual lógica breadcrumbs que en create()
        if ($qc) {
            $breadcrumbsItems = [
                ['name'=>'Auditoría','url'=>route('qualityControls.index'),'active'=>false],
                ['name'=>'Fases','url'=>route('qualityControls.show',$qc),'active'=>false],
                ['name'=>'Editar','url'=>'#','active'=>true],
            ];
        } else {
            $breadcrumbsItems = [
                ['name'=>'Plantilla de Auditoría','url'=>route('auditoryTypes.index'),'active'=>false],
                ['name'=>'Fases','url'=>route('auditoryTypes.show',$fase->auditoryType),'active'=>false],
                ['name'=>'Editar','url'=>'#','active'=>true],
            ];
        }

        $qualityControl = null;
        $fase = Fase::find($faseId);
        if ($fase) {
            $qualityControl = Fase::find($faseId)->qualityControl;
        }

       return view('documents.edit', [
            'document'       => $document,
            'breadcrumbItems'=> $breadcrumbsItems,
            'pageTitle'      => __('Editar Documento'),
            'fases'          => Fase::all(),
            'statuses'       => Status::all(),
            'qualityControl' => $qc,
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

    // Verifica si viene desde plantilla
    if ($request->has('auditorytype')) {
        return redirect()
            ->route('auditoryTypes.show', ['auditoryType' => $request->get('auditorytype')])
            ->with('message', 'Documento actualizado satisfactoriamente');
    }

 // Redirección si viene de Control de Calidad
    if ($request->filled('qualityControl')) {
        // Tomamos el mismo qcId que guardamos en store()
        $qcId = $document->fase->qualityControl->id ?? null;

        return redirect()
            ->route('qualityControls.show', $qcId)
            ->with('message', 'Documento actualizado satisfactoriamente');
    }


    // Si viene desde quality control
    return redirect()
        ->route('fases.show', ['fase' => $document->fase])
        ->with('message', 'Documento actualizado satisfactoriamente');
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
        $document->delete();
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
            $status = Status::where('key', 'waiting_review')->first();
            foreach ($request->file('files') as $key => $value) {
                $orignalName = $value->getClientOriginalName();
                $path = $this->upload($value, 'documents');
                $document = Document::find($key);
                $document->update(['url' => $path, 'status_id' => $status->id, 'original_name' => $orignalName]);
            }
        }

        return redirect()->back();
    }

     // --- Métodos específicos para cambiar estado ---
    public function markAsWaitingReview(Document $document)
    {
        $id = Status::where('key',StatusEnum::WaitingReview->value)->first()->id;
        $document->update(['status_id'=>$id]);
        return back();
    }

    public function markAsAccepted(Document $document)
    {
        $id = Status::where('key',StatusEnum::Accepted->value)->first()->id;
        $document->update(['status_id'=>$id]);
        return back();
    }

    public function markAsRejected(Document $document)
    {
        $id = Status::where('key',StatusEnum::Rejected->value)->first()->id;
        $document->update(['status_id'=>$id]);
        return back();
    }

    public function cancelDocument(Document $document)
    {
        $status = Status::where('key', 'waiting')->first();
        if (!$status)
            return redirect()->back();

        $this->removeFile($document->url);
        $document->update(['status_id' => $status->id, 'url' => null]);
        return redirect()->back();
    }
}
