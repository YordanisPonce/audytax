<x-app-layout>
    <div class="space-y-8">
        <div class="items-center justify-between mb-6">
            <x-breadcrumb :breadcrumb-items="$breadcrumbItems" :page-title="$pageTitle" />
        </div>
        {{-- Dashboard Top Card --}}
        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-7">
            <div class="dasboardCard bg-white dark:bg-slate-800 rounded-md px-5 py-4 flex items-center justify-between bg-center bg-cover bg-no-repeat"
                style="background-image:url('{{ asset('/images/ecommerce-wid-bg.png') }}')">
                <div class="w-56 ">
                    <h3 class="font-Inter font-normal text-white text-lg">
                        {{ __('Hola') }},
                    </h3>
                    <h3 class="font-Interfont-medium text-white text-xl pb-2">
                        {{ auth()->user()->name }}
                    </h3>
                    <p class="font-Intertext-base text-white font-normal">
                        {{ __('Bienvenido a ') . config('app.name') }}
                    </p>
                </div>
            </div>
            @if (auth()->user()->hasRole('admin'))
                <div class="bg-white dark:bg-slate-800 rounded-md px-5 py-4 ">
                    <div class="pl-14 relative">
                        <div
                            class="w-10 h-10 rounded-full bg-sky-100 text-sky-800 text-base flex items-center justify-center absolute left-0 top-2">
                            <iconify-icon icon="il:users"></iconify-icon>
                        </div>
                        <h4 class="font-Interfont-normal text-sm text-textColor dark:text-white pb-1">
                            {{ __('Usuarios') }}
                        </h4>
                        <p class="font-Intertext-xl text-black dark:text-white font-medium">
                            {{ $data['revenue']['total'] }}
                        </p>
                    </div>
                    <div class="ml-auto w-24">
                        <div id="EChart"></div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-md px-5 py-4 ">
                    <div class="pl-14 relative">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-800 text-base flex items-center justify-center absolute left-0 top-2">
                            <iconify-icon icon="fluent-mdl2:compliance-audit"></iconify-icon>
                        </div>
                        <h4 class="font-Interfont-normal text-sm text-textColor dark:text-white pb-1">
                            {!! __('Tipos de auditor&iacute;a') !!}
                        </h4>
                        <p class="font-Intertext-xl text-black dark:text-white font-medium">
                            {{ $data['productSold']['total'] }}
                        </p>
                    </div>
                    <div class="ml-auto w-24">
                        <div id="EChart2"></div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-md px-5 py-4 ">
                    <div class="pl-14 relative">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-800 text-base flex items-center justify-center absolute left-0 top-2">
                            <iconify-icon icon="icon-park-twotone:inspection"></iconify-icon>
                        </div>
                        <h4 class="font-Interfont-normal text-sm text-textColor dark:text-white pb-1">
                            Controles de Calidad
                        </h4>
                        <p class="font-Intertext-xl text-black dark:text-white font-medium">
                            {{ $data['growth']['total'] }}
                        </p>
                    </div>
                    <div class="ml-auto w-24">
                        <div id="EChart3"></div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-slate-800 rounded-md px-5 py-4 ">
                    <div class="pl-14 relative">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-800 text-base flex items-center justify-center absolute left-0 top-2">
                            <iconify-icon icon="icon-park-twotone:inspection"></iconify-icon>
                        </div>
                        <h4 class="font-Interfont-normal text-sm text-textColor dark:text-white pb-1">
                            Controles de Calidad
                        </h4>
                        <p class="font-Intertext-xl text-black dark:text-white font-medium">
                            {{ $data['qualityControls'] }}
                        </p>
                    </div>
                    <div class="ml-auto w-24">
                        <div id="EChart2"></div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-md px-5 py-4 ">
                    <div class="pl-14 relative">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-800 text-base flex items-center justify-center absolute left-0 top-2">
                            <iconify-icon icon="codicon:comment"></iconify-icon>
                        </div>
                        <h4 class="font-Interfont-normal text-sm text-textColor dark:text-white pb-1">
                            {!! __('Comentarios.') !!}
                        </h4>
                        <p class="font-Intertext-xl text-black dark:text-white font-medium">
                            {{ $data['comments'] }}
                        </p>
                    </div>
                    <div class="ml-auto w-24">
                        <div id="EChart2"></div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-md px-5 py-4 ">
                    <div class="pl-14 relative">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-800 text-base flex items-center justify-center absolute left-0 top-2">
                            <iconify-icon icon="fluent-mdl2:compliance-audit"></iconify-icon>
                        </div>
                        <h4 class="font-Interfont-normal text-sm text-textColor dark:text-white pb-1">
                            {!! __('Completados') !!}
                        </h4>
                        <p class="font-Intertext-xl text-black dark:text-white font-medium">
                            {{ $data['qualityControlsCompletePercent'] }}%
                        </p>
                    </div>
                    <div class="ml-auto w-24">
                        <div id="EChart2"></div>
                    </div>
                </div>
            @endif
        </div>
        @isset($data['links'])
            <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-7">
                @foreach ($data['links'] as $link)
                    <a href="{{ route('qualityControls.details', ['qualityControl' => $link, 'fase' => $link->getActiveFase()]) }}"
                        class="bg-white dark:bg-slate-800 rounded-md px-5 py-4 cursor-pointer hover:scale-95 transition-all">
                        <div class="relative h-10 flex items-center gap-2">
                            <div
                                class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-800 text-base flex items-center justify-center left-0 top-2">
                                <iconify-icon icon="fluent-mdl2:compliance-audit"></iconify-icon>
                            </div>
                            <h4 class="font-Interfont-normal text-sm text-textColor dark:text-white pb-1 flex items-center">
                                <span>
                                    {!! $link->name !!}
                                </span>
                            </h4>
                        </div>
                        <div class="ml-auto w-24">
                            <div id="EChart2"></div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endisset
    </div>
    @if (auth()->user()->hasRole('admin'))
        @push('scripts')
            <script type="module">
                {{-- Chart Type: Bar --}}
                let revenueReportChartConfig = {
                    series: [{
                            name: '{{ $data['revenueReport']['revenue']['title'] }}',
                            data: {{ Js::from($data['revenueReport']['revenue']['data']) }},
                        },
                        {
                            name: '{{ $data['revenueReport']['netProfit']['title'] }}',
                            data: {{ Js::from($data['revenueReport']['netProfit']['data']) }},
                        },
                        {
                            name: '{{ $data['revenueReport']['cashFlow']['title'] }}',
                            data: {{ Js::from($data['revenueReport']['cashFlow']['data']) }},
                        },
                    ],
                    chart: {
                        type: "bar",
                        height: 350,
                        width: "100%",
                        toolbar: {
                            show: false,
                        },
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: "45%",
                            endingShape: "rounded",
                        },
                    },
                    legend: {
                        show: true,
                        position: "top",
                        horizontalAlign: "right",
                        fontSize: "13px",
                        fontFamily: "Inter",
                        offsetY: -30,
                        markers: {
                            width: 12,
                            height: 12,
                            radius: 12,
                        },
                        itemMargin: {
                            horizontal: 8,
                            vertical: 0,
                        },
                        onItemClick: {
                            toggleDataSeries: true,
                        },
                        onItemHover: {
                            highlightDataSeries: true,
                        },
                    },
                    title: {
                        text: "Revenue Report",
                        align: "left",
                        offsetX: 0,
                        offsetY: 13,
                        floating: false,
                        style: {
                            fontSize: "20px",
                            fontWeight: "medium",
                            fontFamily: "Inter",
                            color: "##111112",
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ["transparent"],
                    },
                    yaxis: {
                        labels: {
                            style: {
                                fontFamily: "Inter",
                            },
                        },
                    },
                    xaxis: {
                        categories: {{ Js::from($data['revenueReport']['month']) }},
                        labels: {
                            style: {
                                fontFamily: "Inter",
                            },
                        },
                    },
                    fill: {
                        opacity: 1,
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "$ " + val + " thousands";
                            },
                        },
                    },
                    colors: ["#4669FA", "#0CE7FA", "#FA916B"],
                    grid: {
                        show: true,
                        borderColor: "#E2E8F0",
                        strokeDashArray: 10,
                        position: "back",
                    },
                    responsive: [{
                        breakpoint: 600,
                        options: {
                            legend: {
                                position: "bottom",
                                offsetY: 0,
                                horizontalAlign: "center",
                            },
                            plotOptions: {
                                bar: {
                                    columnWidth: "80%",
                                },
                            },
                        },
                    }, ],
                };
                const revenueReportSelector = document.querySelector("#barChartOne");
                const chartDelay = setTimeout(delayChart, 50);
                let revenueReportChart = new ApexCharts(
                    revenueReportSelector,
                    revenueReportChartConfig
                );

                function delayChart() {
                    revenueReportChart.render();
                }
                {{-- Total Revenue Report Chart end --}}


                {{-- Total Revenue Chart start --}}
                {{-- Chart Type: area --}}
                let revenueChartConfig = {
                    chart: {
                        type: "area",
                        height: "48",
                        toolbar: {
                            autoSelected: "pan",
                            show: false,
                        },
                        offsetX: 0,
                        offsetY: 0,
                        zoom: {
                            enabled: false,
                        },
                        sparkline: {
                            enabled: true,
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        curve: "smooth",
                        width: 2,
                    },
                    colors: ["#00EBFF"],
                    tooltip: {
                        theme: "light",
                    },
                    grid: {
                        show: false,
                        padding: {
                            left: 0,
                            right: 0,
                        },
                    },
                    yaxis: {
                        show: false,
                    },
                    fill: {
                        type: "solid",
                        opacity: [0.1],
                    },
                    legend: {
                        show: false,
                    },
                    xaxis: {
                        low: 0,
                        offsetX: 0,
                        offsetY: 0,
                        show: false,
                        labels: {
                            low: 0,
                            offsetX: 0,
                            show: false,
                        },
                        axisBorder: {
                            low: 0,
                            offsetX: 0,
                            show: false,
                        },
                        categories: {{ Js::from($data['revenue']['year']) }},
                    },
                    series: [{
                        data: {{ Js::from($data['revenue']['data']) }},
                    }, ],
                };
                const revenueChartSelector = document.querySelector("#EChart")
                const revenueChart = new ApexCharts(
                    revenueChartSelector,
                    revenueChartConfig
                ).render();
                {{-- Total Revenue Chart end --}}


                {{-- Product Sales Chart start --}}
                {{-- Chart Type: area --}}``
                let productSalesChartConfig = {
                    chart: {
                        type: "area",
                        height: "48",
                        toolbar: {
                            autoSelected: "pan",
                            show: false,
                        },
                        offsetX: 0,
                        offsetY: 0,
                        zoom: {
                            enabled: false,
                        },
                        sparkline: {
                            enabled: true,
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        curve: "smooth",
                        width: 2,
                    },
                    colors: ["#5743BE"],
                    tooltip: {
                        theme: "light",
                    },
                    grid: {
                        show: false,
                        padding: {
                            left: 0,
                            right: 0,
                        },
                    },
                    yaxis: {
                        show: false,
                    },
                    fill: {
                        type: "solid",
                        opacity: [0.1],
                    },
                    legend: {
                        show: false,
                    },
                    xaxis: {
                        low: 0,
                        offsetX: 0,
                        offsetY: 0,
                        show: false,
                        labels: {
                            low: 0,
                            offsetX: 0,
                            show: false,
                        },
                        axisBorder: {
                            low: 0,
                            offsetX: 0,
                            show: false,
                        },
                        categories: {{ Js::from($data['productSold']['year']) }},
                    },
                    series: [{
                        data: {{ Js::from($data['productSold']['quantity']) }},
                    }, ],
                };
                const productSalesChartSelector = document.querySelector("#EChart2")
                const productSalesChart = new ApexCharts(
                    productSalesChartSelector,
                    productSalesChartConfig
                ).render();
                {{-- Product Sales Chart end --}}



                {{-- Growth chart --}}
                {{-- Chart Type: area --}}
                let growthChartConfig = {
                    chart: {
                        type: "area",
                        height: "48",
                        width: "48",
                        toolbar: {
                            autoSelected: "pan",
                            show: false,
                        },
                        offsetX: 0,
                        offsetY: 0,
                        zoom: {
                            enabled: false,
                        },
                        sparkline: {
                            enabled: true,
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        curve: "smooth",
                        width: 2,
                    },
                    colors: ["#fd5693"],
                    tooltip: {
                        theme: "light",
                    },
                    grid: {
                        show: false,
                        padding: {
                            left: 0,
                            right: 0,
                        },
                    },
                    yaxis: {
                        show: false,
                    },
                    fill: {
                        type: "solid",
                        opacity: [0.1],
                    },
                    legend: {
                        show: false,
                    },
                    xaxis: {
                        low: 0,
                        offsetX: 0,
                        offsetY: 0,
                        show: false,
                        labels: {
                            low: 0,
                            offsetX: 0,
                            show: false,
                        },
                        axisBorder: {
                            low: 0,
                            offsetX: 0,
                            show: false,
                        },
                        categories: {{ Js::from($data['growth']['year']) }},
                    },
                    series: [{
                        data: {{ Js::from($data['growth']['perYearRate']) }},
                    }, ],
                };
                const growthChartSelector = document.querySelector("#EChart3");
                const growthChart = new ApexCharts(
                    growthChartSelector,
                    growthChartConfig
                ).render();
                {{-- Growth chart end --}}
            </script>
        @endpush
    @endif
</x-app-layout>
