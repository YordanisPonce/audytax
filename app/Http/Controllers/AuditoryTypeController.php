<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditoryTypeRequest;
use App\Models\AuditoryType;
use App\Models\Fase;
use App\Models\User;
use App\Policies\AuditoryTypePolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;

class AuditoryTypeController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(AuditoryType::class, 'auditoryType');
        $this->middleware('is_admin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $breadcrumbsItems = [
            [
                'name' => 'Auditory Type',
                'url' => route('auditoryTypes.index'),
                'active' => true
            ]
        ];

        $q = $request->get('q');
        $perPage = $request->get('per_page', 10);
        $sort = $request->get('sort');

        $auditoryTypes = QueryBuilder::for(AuditoryType::class)
            ->allowedSorts(['name'])
            ->withCount([
                'fases' => function ($query) {
                    $query->whereDoesntHave('qualityControl');
                }
            ])
            ->where('name', 'like', "%$q%")
            ->latest()
            ->paginate($perPage)
            ->appends(['per_page' => $perPage, 'q' => $q, 'sort' => $sort]);

        return view('auditoryTypes.index', [
            'auditoryTypes' => $auditoryTypes,
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => __("Auditory Type")
        ]);
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
                'name' => 'Create',
                'url' => route('auditoryTypes.create'),
                'active' => true
            ],
        ];

        return view('auditoryTypes.create', [
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => __("Auditory Types")
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AuditoryTypeRequest $request)
    {
        AuditoryType::create(['name' => $request->validated('name')]);
        return redirect()->route('auditoryTypes.index')->with('message', 'Tipo de auditoría creada satisfactoriamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, AuditoryType $auditoryType)
    {
        $breadcrumbsItems = [
            [
                'name' => __("Auditory Type"),
                'url' => route('auditoryTypes.index'),
                'active' => false
            ],
            [
                'name' => __("Fases"),
                'url' => route('auditoryTypes.show', ['auditoryType' => $auditoryType]),
                'active' => false
            ],
            [
                'name' => 'Show',
                'url' => '#',
                'active' => true
            ],
        ];

        $q = $request->get('q');
        $perPage = $request->get('per_page', 10);
        $sort = $request->get('sort');
        $fases = QueryBuilder::for(Fase::class)
            ->orderBy('id')
            ->allowedSorts(['description'])
            ->with('auditoryType', 'qualityControl', 'status')
            ->withCount('documents')
            ->where('auditory_type_id', $auditoryType->id)
            ->whereDoesntHave('qualityControl')
            ->latest()
            ->paginate($perPage)
            ->appends(['per_page' => $perPage, 'q' => $q, 'sort' => $sort]);
        return view('fases.index', [
            'fases' => $fases,
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => 'Fases de la auditoría ' . $auditoryType->name,
            'auditoryId' => $auditoryType->id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return \Illuminate\Http\Response
     */
    public function edit(AuditoryType $auditoryType)
    {
        $breadcrumbsItems = [
            [
                'name' => __("Auditory Types"),
                'url' => route('auditoryTypes.index'),
                'active' => false
            ],
            [
                'name' => 'Show',
                'url' => '#',
                'active' => true
            ],
        ];

        return view('auditoryTypes.edit', [
            'auditoryType' => $auditoryType,
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => 'Mostrar Tipo de auditorìa',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return \Illuminate\Http\Response
     */
    public function update(AuditoryTypeRequest $request, AuditoryType $auditoryType)
    {
        $auditoryType->update(['name' => $request->validated('name')]);
        return redirect()->route('auditoryTypes.index')->with('message', 'Tipo de auditorìa actualizada satisfactoriamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return \Illuminate\Http\Response
     */
    public function destroy(AuditoryType $auditoryType)
    {
        try {
            Gate::denyIf(fn() => $auditoryType->qualityControls()->count() > 0);
            $auditoryType->delete();
            return redirect()->route('auditoryTypes.index')->with('message', 'Tipo de auditorìa eliminada satisfactoriamente');
        } catch (AuthorizationException $th) {
            return redirect()->route('auditoryTypes.index')->with('message', 'No se puede eliminar este tipo de autidoría porque tiene controles de calidad asociados a ella')->with('status', 'warning');
        } catch (\Throwable $th) {
            return redirect()->route('auditoryTypes.index')->with('message', 'Ha ocurrido un error inesperado');
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return \Illuminate\Http\Response
     */

    public function getFases(AuditoryType $auditoryType)
    {
        return 'Hola nundo';
    }
}
