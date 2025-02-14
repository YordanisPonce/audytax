<?php

namespace App\Http\Controllers;

use App\Http\Requests\FaseRequest;
use App\Models\AuditoryType;
use App\Models\Document;
use App\Models\Fase;
use App\Models\Status;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class FaseController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Fase::class, 'fase');
        $this->middleware('is_admin', ['only' => ['create', 'store', 'edit', 'update', 'destroy', 'show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $breadcrumbsItems = [
            [
                'name' => 'Auditory Type',
                'url' => route('auditoryTypes.index'),
                'active' => false
            ],
            [
                'name' => 'Fases',
                'url' => route('fases.index'),
                'active' => false
            ],
            [
                'name' => 'Create',
                'url' => route('fases.create'),
                'active' => true
            ],
        ];

        return view('fases.create', [
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => __("Fases"),
            "auditoryTypes" => AuditoryType::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FaseRequest $request)
    {
        $status = Status::where('key', 'waiting')->first();
        $fase = Fase::create($request->only('name', 'descripcion', 'auditory_type_id', 'quality_control_id') + ['status_id' => $status->id]);
        $params = $this->getParams($request, $fase);
        return redirect()->route($params['route'], $params['param'])->with('message', 'Fase agregada satisfactoriamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Fase  $fase
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Fase $fase)
    {
        $breadcrumbsItems = [
            [
                'name' => __("Auditory Type"),
                'url' => route('auditoryTypes.index'),
                'active' => false
            ],
            [
                'name' => __("Fases"),
                'url' => '',
                'active' => false
            ],
            [
                'name' => 'Documents',
                'url' => '#',
                'active' => true
            ],
        ];

        $q = $request->get('q');
        $perPage = $request->get('per_page', 10);
        $sort = $request->get('sort');
        $documents = QueryBuilder::for(Document::class)
            ->orderBy('id')
            ->allowedSorts(['description'])
            ->with('fase', 'qualityControl', 'status')
            ->where('fase_id', $fase->id)
            ->latest()
            ->paginate($perPage)
            ->appends(['per_page' => $perPage, 'q' => $q, 'sort' => $sort]);
        return view('documents.index', [
            'documents' => $documents,
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => 'Documentos de la fase ' . $fase->name,
            'faseId' => $fase->id
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Fase  $fase
     * @return \Illuminate\Http\Response
     */
    public function edit(Fase $fase)
    {
        $breadcrumbsItems = [
            [
                'name' => 'Auditory Type',
                'url' => route('auditoryTypes.index'),
                'active' => false
            ],
            [
                'name' => __("Fases"),
                'url' => route('auditoryTypes.show', ['auditoryType' => $fase->auditoryType]),
                'active' => false
            ],
            [
                'name' => 'Edit',
                'url' => '#',
                'active' => true
            ],
        ];

        return view('fases.edit', [
            'fase' => $fase,
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => 'Editar',
            "auditoryTypes" => AuditoryType::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fase  $fase
     * @return \Illuminate\Http\Response
     */
    public function update(FaseRequest $request, Fase $fase)
    {
        $fase->update($request->only($fase->getFillable()));
        $params = $this->getParams($request, $fase);
        return redirect()->route($params['route'], $params['param'])->with('message', 'Fase actualizada satisfactoriamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Fase  $fase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Fase $fase)
    {
        $fase->delete();
        return redirect()->back()->with('message', 'Tipo de auditoría creada satisfactoriamente');
    }

    private function getParams($request, $fase)
    {
        $route = !$request->quality_control_id ? 'auditoryTypes.show' : 'qualityControls.show';
        $param = !$fase->qualityControl ? ['auditoryType' => $fase->auditoryType] : ['qualityControl' => $fase->qualityControl];
        return [
            'route' => $route,
            'param' => $param
        ];
    }
}
