<?php

namespace App\Http\Controllers;

use App\Http\Requests\QualityControlRequest;
use App\Models\AuditoryType;
use App\Models\QualityControl;
use App\Models\Status;
use App\Models\User;
use App\Models\Fase;
use App\Models\History;
use App\Notifications\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;

class QualityControlController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(QualityControl::class, 'qualityControl');
        $this->middleware('is_admin', ['except' => 'getDetails']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $breadcrumbsItems = [
            [
                'name' => 'Quality Control',
                'url' => route('qualityControls.index'),
                'active' => true
            ]
        ];

        $q = $request->get('q');
        $perPage = $request->get('per_page', 10);
        $sort = $request->get('sort');

        $qualityControls = QueryBuilder::for(QualityControl::class)
            ->allowedSorts(['name', 'description'])
            ->with('auditoryType', 'status')
            ->withCount([
                'fases',
                'users as client_users_count' => function ($query) {
                    $query->whereHas('roles', function ($subquery) {
                        $subquery->where('name', 'client');
                    });
                },
                'users as consultant_users_count' => function ($query) {
                    $query->whereHas('roles', function ($subquery) {
                        $subquery->where('name', 'consultant');
                    });
                },
            ])
            ->where(function ($query) use ($user) {
                if (!$user->isAdmin()) {
                    $query->whereIn('id', $user->qualityControls()->get(['quality_controls.id'])->pluck('id')->toArray());
                }
            })
            ->where('name', 'like', "%$q%")
            ->latest()
            ->paginate($perPage)
            ->appends(['per_page' => $perPage, 'q' => $q, 'sort' => $sort]);


        /*  if ($user->isAdmin()) { */

        return view('qualityControls.index', [
            'qualityControls' => $qualityControls,
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => __("Quality Controls")
        ]);
        /*      } */
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
                'name' => 'Quality Control',
                'url' => route('qualityControls.index'),
                'active' => false
            ],
            [
                'name' => 'Create',
                'url' => route('qualityControls.create'),
                'active' => true
            ],
        ];

        $auditoryTypes = AuditoryType::all();
        $statuses = Status::all();
        $clients = User::whereHas('roles', function ($query) {
            $query->where('name', 'client');
        })->withoutAuthUser()
            ->withoutSuperAdmin()->get();

        $consultants = User::whereHas('roles', function ($query) {
            $query->where('name', 'consultant');
        })->withoutAuthUser()
            ->withoutSuperAdmin()->get();

        return view('qualityControls.create', [
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => "Crear control de calidad",
            "auditoryTypes" => $auditoryTypes,
            "statuses" => $statuses,
            "clients" => $clients,
            "consultants" => $consultants,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QualityControlRequest $request)
    {
        $status = Status::where('key', 'waiting_review')->first();
        $qualityControl = QualityControl::create($request->only('name', 'description', 'auditory_type_id') + ['status_id' => $status->id ?: 1]);
        $ids = array_merge($request->consultants ?: [], $request->clients ?: []);
        $qualityControl->users()->attach($ids);
        //
        User::whereIn('id', $ids)->get()->each(function (User $item) {
            $item->notify(new Notify('Te ha asignado un nuevo control de calidad'));
        });
        return redirect()->route('qualityControls.index')->with('message', 'Control de calidad agregado satisfactoriamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\QualityControl  $qualityControl
     * @return \Illuminate\Http\Response
     */
    public function show(QualityControl $qualityControl, Request $request)
    {
        $breadcrumbsItems = [
            [
                'name' => __("Quality Control"),
                'url' => route('qualityControls.index'),
                'active' => false
            ],
            [
                'name' => __("Fases"),
                'url' => route('qualityControls.show', ['qualityControl' => $qualityControl]),
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
        ->where('quality_control_id', $qualityControl->id)
        ->with('documents.status')
        ->latest()
        ->get();

     
    // Calculamos el total de documentos por estado en todas las fases:
    $statusCounts = \App\Models\Document::query()
        ->whereIn('fase_id', $fases->pluck('id'))
        ->selectRaw('status_id, count(*) as count')
        ->groupBy('status_id')
        ->pluck('count', 'status_id');

    // Mapear IDs de status a tu enum
    $statusKeys = \App\Models\Status::pluck('key', 'id'); // [1=>'open',2=>'accepted',...]
    $counts = [
        'open'           => 0,
        'waiting_review' => 0,
        'accepted'       => 0,
        'rejected'       => 0,
    ];
    foreach ($statusCounts as $statusId => $cnt) {
        $key = $statusKeys[$statusId] ?? null;
        if ($key && isset($counts[$key])) {
            $counts[$key] = $cnt;
        }
    }

    return view('qualityControls.show', [
        'fases'         => $fases,
        'qualityControl'=> $qualityControl,
        'breadcrumbItems'=> $breadcrumbsItems,
        'pageTitle'     => 'Fases de la Auditoria',
        'statusCounts'  => $counts,
        // si necesitas comments/activity, pásalos también…
    ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\QualityControl  $qualityControl
     * @return \Illuminate\Http\Response
     */
    public function edit(QualityControl $qualityControl)
    {
        $breadcrumbsItems = [
            [
                'name' => __("Quality Controls"),
                'url' => route('qualityControls.index'),
                'active' => false
            ],
            [
                'name' => 'Edit',
                'url' => '#',
                'active' => true
            ],
        ];
        $auditoryTypes = AuditoryType::all();
        $statuses = Status::all();
        $clients = User::whereHas('roles', function ($query) {
            $query->where('name', 'client');
        })->withoutAuthUser()
            ->withoutSuperAdmin()
            ->get();

        $consultants = User::whereHas('roles', function ($query) {
            $query->where('name', 'consultant');
        })
            ->withoutAuthUser()
            ->withoutSuperAdmin()
            ->get();
        return view('qualityControls.edit', [
            'qualityControl' => $qualityControl,
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => 'Editar control de calidad',
            "auditoryTypes" => $auditoryTypes,
            "statuses" => $statuses,
            "clients" => $clients,
            "consultants" => $consultants,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QualityControl  $qualityControl
     * @return \Illuminate\Http\Response
     */
    public function update(QualityControlRequest $request, QualityControl $qualityControl)
    {
        $qualityControl->update($request->only($qualityControl->getFillable()));

        $qualityControl->users()->get()->each(function (User $item) {
            $user = User::find($item->id);
            $user->notify(new Notify('Ha actualizado un control de calidad al cual estas asignado'));
        });

        return redirect()->route('qualityControls.index')->with('message', 'Control de calidad actualizado satisfactoriamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\QualityControl  $qualityControl
     * @return \Illuminate\Http\Response
     */
    public function destroy(QualityControl $qualityControl)
    {
        $qualityControl->delete();
        return redirect()->route('qualityControls.index')->with('message', 'Control de calidad eliminado satisfactoriamente');
    }

    public function getDetails(QualityControl $qualityControl, Request $request)
    {
        Gate::authorize('getDetails', $qualityControl);
        $breadcrumbsItems = [
            [
                'name' => 'Detalles',
                'url' => route('qualityControls.index'),
                'active' => true
            ]
        ];
        $faseId = $request->get('fase');
        $fase = $qualityControl->fases()->with('status', 'documents.status')->where(function ($query) use ($faseId) {
            if ($faseId) {
                $query->where('id', $faseId);
            } else {
                $query->whereHas('documents', function ($subquery) {
                    $status = Status::where('key', 'waiting_review')->first();
                    $subquery->where('status_id', $status->id);
                });
            }
        })->first();

        $fase = $fase ?? $qualityControl->fases()->first();
        $nextFase = $qualityControl->fases()->where('id', '>', $fase->id)->first() ?? $qualityControl->fases()->first();


        return view('qualityControls.work_flow', [
            'breadcrumbItems' => $breadcrumbsItems,
            'pageTitle' => $qualityControl->name,
            'qualityControl' => $qualityControl,
            'fase' => $fase,
            'nextFase' => $nextFase
        ]);
    }
}
