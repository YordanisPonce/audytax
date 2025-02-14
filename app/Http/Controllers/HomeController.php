<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\AuditoryType;
use App\Models\Comment;
use App\Models\Document;
use App\Models\Fase;
use App\Models\QualityControl;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    /**
     * Analytic Dashboard
     */
    public function index()
    {
        $breadcrumbsItems = [
            [
                'name' => 'Inicio',
                'url' => '/',
                'active' => true
            ],
        ];
        $chartData = [];

        if (auth()->user()->hasRole('admin')) {
            $chartData = [
                'revenueReport' => [
                    'month' => ["Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"],
                    'revenue' => [
                        'title' => 'Revenue',
                        'data' => [76, 85, 101, 98, 87, 105, 91, 114, 94],
                    ],
                    'netProfit' => [
                        'title' => 'Net Profit',
                        'data' => [35, 41, 36, 26, 45, 48, 52, 53, 41],
                    ],
                    'cashFlow' => [
                        'title' => 'Cash Flow',
                        'data' => [44, 55, 57, 56, 61, 58, 63, 60, 66],
                    ],
                ],
                'revenue' => [
                    'year' => User::select(DB::raw('YEAR(created_at) as year'))->groupBy('year')->pluck('year')->toArray(),
                    'data' => User::select(DB::raw('YEAR(created_at) as year'), DB::raw('COUNT(*) as count'))->groupBy('year')->pluck('count')->toArray(),
                    'total' => User::count()
                ],
                'productSold' => [
                    'year' => AuditoryType::select(DB::raw('YEAR(created_at) as year'))->groupBy('year')->pluck('year')->toArray(),
                    'quantity' => AuditoryType::select(DB::raw('YEAR(created_at) as year'), DB::raw('COUNT(*) as count'))->groupBy('year')->pluck('count')->toArray(),
                    'total' => AuditoryType::count(),
                ],
                'growth' => [
                    'year' => QualityControl::select(DB::raw('YEAR(created_at) as year'))->groupBy('year')->pluck('year')->toArray(),
                    'perYearRate' => QualityControl::select(DB::raw('YEAR(created_at) as year'), DB::raw('COUNT(*) as count'))->groupBy('year')->pluck('count')->toArray(),
                    'total' => QualityControl::count(),
                ],
                'lastWeekOrder' => [
                    'name' => 'Documentos aprobados',
                    'data' => Document::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('COUNT(*) as count')
                    )
                        ->whereHas('status', function ($query) {
                            $query->where('key', 'complete');
                        })
                        ->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])
                        ->groupBy('date')
                        ->orderBy('date')
                        ->pluck('count')
                        ->toArray(),
                    'total' => Document::whereHas('status', function ($query) {
                        $query->where('key', 'complete');
                    })->count(),
                    'percentage' => (Document::whereHas('status', function ($query) {
                        $query->where('key', 'complete');
                    })->whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()])->count() ?? 1 / Document::count()) * 100,
                ],
                'lastWeekProfit' => [
                    'name' => 'Last Week Profit',
                    'data' => [44, 55, 57, 56, 61, 10],
                    'total' => '10k+',
                    'percentage' => 100,
                    'preSymbol' => '+',
                ],
                'lastWeekOverview' => [
                    'labels' => ["Pendientes", "Aprobados"],
                    'data' => [
                        Document::whereNull('url')->count(),
                        Document::whereNotNull('url')->count()
                    ],
                    'title' => 'Total de Fases',
                    'amount' => Fase::count(),
                    'percentage' => Fase::count(),
                ],
            ];
        } else {
            $totalQualityControl = QualityControl::whereHas('users', function ($query) {
                $query->where('users.id', auth()->id());
            })->count();

            $completeQualiltyCotrol = QualityControl::whereHas('users', function ($query) {
                $query->where('users.id', auth()->id());
            })->whereHas('status', function ($query) {
                $query->where('key', StatusEnum::Complete->value);
            })->count();

            $comments = Comment::whereHas('user', function ($query) {
                $query->where('id', auth()->id());
            })->count();

            $links = auth()->user()->qualityControls()->simplePaginate(10);
            $chartData = [
                'qualityControls' => $totalQualityControl,
                'comments' => $comments,
                'links' => $links,
                'qualityControlsCompletePercent' => number_format((max([$completeQualiltyCotrol, 1]) * 100) / $totalQualityControl, 0),
            ];
        }
        return view('Index', [
            'pageTitle' => config('app.name'),
            'data' => $chartData,
            'breadcrumbItems' => $breadcrumbsItems
        ]);
    }

    /**
     * Ecommerce Dashboard
     */
    public function ecommerceDashboard()
    {
        $chartData = [
            'revenueReport' => [
                'month' => ["Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"],
                'revenue' => [
                    'title' => 'Revenue',
                    'data' => [76, 85, 101, 98, 87, 105, 91, 114, 94],
                ],
                'netProfit' => [
                    'title' => 'Net Profit',
                    'data' => [35, 41, 36, 26, 45, 48, 52, 53, 41],
                ],
                'cashFlow' => [
                    'title' => 'Cash Flow',
                    'data' => [44, 55, 57, 56, 61, 58, 63, 60, 66],
                ],
            ],
            'revenue' => [
                'year' => [1991, 1992, 1993, 1994, 1995],
                'data' => [350, 500, 950, 700, 100],
                'total' => 4000,
                'currencySymbol' => '$',
            ],
            'productSold' => [
                'year' => [1991, 1992, 1993, 1994, 1995],
                'quantity' => [800, 600, 1000, 50, 100],
                'total' => 100,
            ],
            'growth' => [
                'year' => [1991, 1992, 1993, 1994, 1995],
                'perYearRate' => [10, 20, 30, 40, 10],
                'total' => 10,
                'preSymbol' => '+',
                'postSymbol' => '%',
            ],
            'lastWeekOrder' => [
                'name' => 'Last Week Order',
                'data' => [44, 55, 57, 56, 61, 10],
                'total' => '10k+',
                'percentage' => 100,
                'preSymbol' => '-',
            ],
            'lastWeekProfit' => [
                'name' => 'Last Week Profit',
                'data' => [44, 55, 57, 56, 61, 10],
                'total' => '10k+',
                'percentage' => 100,
                'preSymbol' => '+',
            ],
            'lastWeekOverview' => [
                'labels' => ["Success", "Return"],
                'data' => [60, 40],
                'title' => 'Profit',
                'amount' => '650k+',
                'percentage' => 0.02,
            ],
        ];
        $topCustomers = [
            [
                'serialNo' => 1,
                'name' => 'Danniel Smith',
                'totalPoint' => 50.5,
                'progressBarPoint' => 50,
                'progressBarColor' => 'green',
                'backgroundColor' => 'sky',
                'isMvpUser' => true,
                'photo' => '/images/customer.png',
            ],
            [
                'serialNo' => 2,
                'name' => 'Danniel Smith',
                'totalPoint' => 50.5,
                'progressBarPoint' => 50,
                'progressBarColor' => 'sky',
                'backgroundColor' => 'orange',
                'isMvpUser' => false,
                'photo' => '/images/customer.png',
            ],
            [
                'serialNo' => 3,
                'name' => 'Danniel Smith',
                'totalPoint' => 50.5,
                'progressBarPoint' => 50,
                'progressBarColor' => 'orange',
                'backgroundColor' => 'green',
                'isMvpUser' => false,
                'photo' => '/images/customer.png',
            ],
            [
                'serialNo' => 4,
                'name' => 'Danniel Smith',
                'totalPoint' => 50.5,
                'progressBarPoint' => 50,
                'progressBarColor' => 'green',
                'backgroundColor' => 'sky',
                'isMvpUser' => true,
                'photo' => '/images/customer.png',
            ],
            [
                'serialNo' => 5,
                'name' => 'Danniel Smith',
                'totalPoint' => 50.5,
                'progressBarPoint' => 50,
                'progressBarColor' => 'sky',
                'backgroundColor' => 'orange',
                'isMvpUser' => false,
                'photo' => '/images/customer.png',
            ],
            [
                'serialNo' => 6,
                'name' => 'Danniel Smith',
                'totalPoint' => 50.5,
                'progressBarPoint' => 50,
                'progressBarColor' => 'orange',
                'backgroundColor' => 'green',
                'isMvpUser' => false,
                'photo' => '/images/customer.png',
            ],
        ];
        $recentOrders = [
            [
                'companyName' => 'Biffco Enterprises Ltd.',
                'email' => 'Biffco@biffco.com',
                'productType' => 'Technology',
                'invoiceNo' => 'INV-0001',
                'amount' => 1000,
                'currencySymbol' => '$',
                'paymentStatus' => 'paid',
            ],
            [
                'companyName' => 'Biffco Enterprises Ltd.',
                'email' => 'Biffco@biffco.com',
                'productType' => 'Technology',
                'invoiceNo' => 'INV-0001',
                'amount' => 1000,
                'currencySymbol' => '$',
                'paymentStatus' => 'paid',
            ],
            [
                'companyName' => 'Biffco Enterprises Ltd.',
                'email' => 'Biffco@biffco.com',
                'productType' => 'Technology',
                'invoiceNo' => 'INV-0001',
                'amount' => 1000,
                'currencySymbol' => '$',
                'paymentStatus' => 'paid',
            ],
            [
                'companyName' => 'Biffco Enterprises Ltd.',
                'email' => 'Biffco@biffco.com',
                'productType' => 'Technology',
                'invoiceNo' => 'INV-0001',
                'amount' => 1000,
                'currencySymbol' => '$',
                'paymentStatus' => 'due',
            ],
            [
                'companyName' => 'Biffco Enterprises Ltd.',
                'email' => 'Biffco@biffco.com',
                'productType' => 'Technology',
                'invoiceNo' => 'INV-0001',
                'amount' => 1000,
                'currencySymbol' => '$',
                'paymentStatus' => 'paid',
            ],
            [
                'companyName' => 'Biffco Enterprises Ltd.',
                'email' => 'Biffco@biffco.com',
                'productType' => 'Technology',
                'invoiceNo' => 'INV-0001',
                'amount' => 1000,
                'currencySymbol' => '$',
                'paymentStatus' => 'due',
            ],
        ];

        return view('dashboards.ecommerce', [
            'pageTitle' => 'Ecommerce Dashboard',
            'data' => $chartData,
            'topCustomers' => $topCustomers,
            'recentOrders' => $recentOrders,
        ]);
    }

}
