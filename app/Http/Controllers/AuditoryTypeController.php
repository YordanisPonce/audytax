<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuditoryTypeRequest;
use App\Models\AuditoryType;
use App\Models\Company;
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
        // $this->middleware('is_admin')
        $this->middleware('is_admin')->except(['show']);
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
                },
                'documents as total_documents' => function($query) {
                    $query->whereHas('fase', function($q) {
                        $q->whereDoesntHave('qualityControl');
                    });
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

        $clients = User::role('client')->get();

        return view('auditoryTypes.create', [
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => __("Auditory Types"),
            'clients' => $clients
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
        // Validar y obtener el ID del cliente de la solicitud
        $clientId = $request->validated('client_id');
    
        // Crear el tipo de auditoría y asociarlo con el cliente
        $auditoryType = AuditoryType::create([
            'name' => $request->validated('name'),
            'client_id' => $clientId, // Suponiendo que el modelo AuditoryType tiene un campo client_id
        ]);
    
        // Crear una fase inicial para este tipo de auditoría
        $fase = $auditoryType->fases()->create([
            'name' => 'Fase 1',
            'description' => 'Fase inicial',
            'status_id' => \App\Models\Status::where('key', 'waiting')->first()->id
        ]);

        // Convertir el string de documentos en un array
        $documentNames = explode(',', $request->input('documents'));

        // Crear documentos asociados a la fase
        foreach ($documentNames as $documentName) {
            if (trim($documentName) !== '') {
                $auditoryType->documents()->create([
                    'name' => trim($documentName),
                    'status_id' => \App\Models\Status::where('key', 'waiting')->first()->id,
                    'fase_id' => $fase->id  // Aquí asociamos el documento a la fase
                ]);
            }
        }

        return redirect()->route('auditoryTypes.index')
            ->with('message', 'Tipo de auditoría creada satisfactoriamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, AuditoryType $auditoryType)
    {
        // Obtener la primera fase del tipo de auditoría (ya que cada auditoría solo tiene una fase)
        $fase = $auditoryType->fases()->whereDoesntHave('qualityControl')->first();
        
        if (!$fase) {
            return redirect()->route('auditoryTypes.index')
                ->with('message', 'No se encontró ninguna fase para este tipo de auditoría')
                ->with('status', 'warning');
        }
        
        $breadcrumbsItems = [
            [
                'name' => __("Auditory Type"),
                'url' => route('auditoryTypes.index'),
                'active' => false
            ],
            [
                'name' => 'Documentos',
                'url' => '#',
                'active' => true
            ],
        ];

        $q = $request->get('q');
        $perPage = $request->get('per_page', 10);
        $sort = $request->get('sort');
        
        // Obtener los documentos de la fase
        $documents = QueryBuilder::for(\App\Models\Document::class)
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
            'pageTitle' => 'Documentos de la auditoría ' . $auditoryType->name,
            'faseId' => $fase->id
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
                'name' => 'Edit',
                'url' => '#',
                'active' => true
            ],
        ];

        $clients = User::role('client')->get();

        return view('auditoryTypes.edit', [
            'auditoryType' => $auditoryType,
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => 'Edit Auditory Type',
            'clients' => $clients
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
        // Update the audit type name
        $auditoryType->update(['name' => $request->validated('name')]);

        // Delete existing documents
        $auditoryType->documents()->delete();

        // Convert the string of documents into an array
        $documentNames = explode(',', $request->input('documents'));

        // Create new documents
        foreach ($documentNames as $documentName) {
            if (trim($documentName) !== '') {
                $auditoryType->documents()->create([
                    'name' => trim($documentName),
                    'status_id' => \App\Models\Status::where('key', 'waiting')->first()->id,
                ]);
            }
        }

        return redirect()->route('auditoryTypes.index')->with('message', 'Tipo de auditoría actualizada satisfactoriamente');
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
