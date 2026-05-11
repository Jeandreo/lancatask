@extends('layouts.app')

@section('Page Title', 'Dashboard Admin')

@section('custom-head')
<style>
    .admin-dashboard-card {
        min-height: 118px;
    }

    .admin-dashboard-chart {
        min-height: 295px;
    }

    .admin-dashboard-mini-chart {
        min-height: 260px;
    }

    .admin-dashboard-list {
        min-height: 365px;
    }

    .admin-dashboard-icon {
        width: 44px;
        height: 44px;
    }
</style>
@endsection

@section('content')
<div class="row g-5 g-xl-8">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-dashboard-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-gray-600 fw-semibold d-block mb-2">Tipos de contrato</span>
                    <span class="fs-2hx fw-bold text-gray-900">{{ $cards['contracts'] }}</span>
                </div>
                <div class="admin-dashboard-icon rounded bg-light-primary d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-file-contract text-primary fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-dashboard-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-gray-600 fw-semibold d-block mb-2">Contratos ativos</span>
                    <span class="fs-2hx fw-bold text-gray-900">{{ $cards['active_contracts'] }}</span>
                </div>
                <div class="admin-dashboard-icon rounded bg-light-success d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-circle-check text-success fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-dashboard-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-gray-600 fw-semibold d-block mb-2">Projetos ativos</span>
                    <span class="fs-2hx fw-bold text-gray-900">{{ $cards['active_projects'] }}</span>
                </div>
                <div class="admin-dashboard-icon rounded bg-light-info d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-diagram-project text-info fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card admin-dashboard-card">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-gray-600 fw-semibold d-block mb-2">Receita bruta {{ $year }}</span>
                    <span class="fs-2 fw-bold text-gray-900">{{ $cards['gross_revenue'] }}</span>
                </div>
                <div class="admin-dashboard-icon rounded bg-light-warning d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-sack-dollar text-warning fs-2"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-5 g-xl-8 mt-0">
    <div class="col-12 col-xl-6">
        <div class="card h-100">
            <div class="card-header border-0 pt-5">
                <div>
                    <h3 class="card-title fw-bold text-gray-900 mb-1">Finanças do ano</h3>
                    <span class="text-gray-600 fw-semibold">{{ $year }}</span>
                </div>
            </div>
            <div class="card-body pt-0">
                <div id="adminFinanceAreaChart" class="admin-dashboard-chart"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title fw-bold text-gray-900 mb-0">Resultado</h3>
            </div>
            <div class="card-body pt-0">
                <div id="adminFinanceBarChart" class="admin-dashboard-mini-chart"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title fw-bold text-gray-900 mb-0">Receitas por categoria</h3>
            </div>
            <div class="card-body pt-0">
                <div id="adminCategoryChart" class="admin-dashboard-mini-chart"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-5 g-xl-8 mt-0">
    <div class="col-12 col-xl-6">
        <div class="card admin-dashboard-list">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title fw-bold text-gray-900 mb-0">Últimos acessos</h3>
            </div>
            <div class="card-body pt-0">
                @forelse($latestAccesses as $access)
                <div class="d-flex align-items-center py-4 border-bottom border-gray-200">
                    <div class="symbol symbol-45px me-4">
                        <span class="symbol-label bg-light-primary">
                            <i class="fa-solid fa-user text-primary fs-3"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <span class="text-gray-900 fw-bold d-block">{{ $access['name'] }}</span>
                        <span class="text-gray-600 fw-semibold fs-7">{{ $access['email'] }}</span>
                    </div>
                    <span class="badge badge-light-primary fw-semibold">{{ $access['last_activity'] }}</span>
                </div>
                @empty
                <div class="w-100 bg-light border border-dashed border-1 border-gray-200 h-125px rounded d-flex justify-content-center align-items-center">
                    <span class="text-gray-600 fw-semibold">Nenhum acesso ativo encontrado.</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card admin-dashboard-list">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title fw-bold text-gray-900 mb-0">Últimas tarefas atualizadas</h3>
            </div>
            <div class="card-body pt-0">
                @forelse($latestTasks as $task)
                <div class="d-flex align-items-center py-4 border-bottom border-gray-200">
                    <div class="symbol symbol-45px me-4">
                        <span class="symbol-label @if($task['checked']) bg-light-success @else bg-light-warning @endif">
                            <i class="fa-solid @if($task['checked']) fa-check text-success @else fa-clock text-warning @endif fs-3"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <span class="text-gray-900 fw-bold d-block text-truncate">{{ $task['name'] }}</span>
                        <span class="text-gray-600 fw-semibold fs-7 d-block text-truncate">
                            {{ $task['project_name'] ?? 'Sem projeto' }} · {{ $task['module_name'] ?? 'Sem módulo' }}
                        </span>
                    </div>
                    <div class="text-end ms-4">
                        <span class="badge @if($task['checked']) badge-light-success @else badge-light-warning @endif fw-semibold d-block mb-1">
                            @if($task['checked']) Concluída @else Pendente @endif
                        </span>
                        <span class="text-gray-600 fw-semibold fs-8">{{ $task['updated_at'] }}</span>
                    </div>
                </div>
                @empty
                <div class="w-100 bg-light border border-dashed border-1 border-gray-200 h-125px rounded d-flex justify-content-center align-items-center">
                    <span class="text-gray-600 fw-semibold">Nenhuma tarefa atualizada encontrada.</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-footer')
@parent
<script>
    const adminDashboardCharts = @json($charts);
    const adminDashboardBarTotals = @json($barTotals);
    const adminDashboardCurrency = new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });

    function adminDashboardMoney(value) {
        return adminDashboardCurrency.format(value);
    }

    function adminDashboardColor(name) {
        return KTUtil.getCssVariableValue(name);
    }

    const adminFinanceAreaChart = new ApexCharts(document.querySelector('#adminFinanceAreaChart'), {
        series: [
            {
                name: 'Receitas',
                data: adminDashboardCharts.monthlyRevenue
            },
            {
                name: 'Despesas',
                data: adminDashboardCharts.monthlyExpense
            },
            {
                name: 'Resultado',
                data: adminDashboardCharts.monthlyResult
            }
        ],
        chart: {
            fontFamily: 'inherit',
            type: 'area',
            height: 295,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.38,
                opacityTo: 0.04,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: adminDashboardCharts.months,
            labels: {
                style: {
                    colors: adminDashboardColor('--bs-gray-600'),
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: adminDashboardMoney,
                style: {
                    colors: adminDashboardColor('--bs-gray-600'),
                    fontSize: '12px'
                }
            }
        },
        colors: [
            adminDashboardColor('--bs-success'),
            adminDashboardColor('--bs-danger'),
            adminDashboardColor('--bs-primary')
        ],
        grid: {
            borderColor: adminDashboardColor('--bs-border-dashed-color'),
            strokeDashArray: 4
        },
        tooltip: {
            y: {
                formatter: adminDashboardMoney
            }
        }
    });

    const adminFinanceBarChart = new ApexCharts(document.querySelector('#adminFinanceBarChart'), {
        series: [
            {
                name: 'Total',
                data: Object.values(adminDashboardBarTotals)
            }
        ],
        chart: {
            fontFamily: 'inherit',
            type: 'bar',
            height: 260,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '48%',
                distributed: true
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: Object.keys(adminDashboardBarTotals),
            labels: {
                style: {
                    colors: adminDashboardColor('--bs-gray-700'),
                    fontSize: '12px',
                    fontWeight: 600
                }
            }
        },
        yaxis: {
            labels: {
                formatter: adminDashboardMoney
            }
        },
        colors: [
            adminDashboardColor('--bs-success'),
            adminDashboardColor('--bs-danger'),
            adminDashboardColor('--bs-primary')
        ],
        grid: {
            borderColor: adminDashboardColor('--bs-border-dashed-color'),
            strokeDashArray: 4
        },
        legend: {
            show: false
        },
        tooltip: {
            y: {
                formatter: adminDashboardMoney
            }
        }
    });

    const adminCategoryChart = new ApexCharts(document.querySelector('#adminCategoryChart'), {
        series: adminDashboardCharts.categoryRevenue,
        chart: {
            fontFamily: 'inherit',
            type: 'donut',
            height: 260
        },
        labels: adminDashboardCharts.categoryLabels,
        legend: {
            position: 'bottom',
            fontSize: '12px'
        },
        dataLabels: {
            enabled: false
        },
        colors: [
            adminDashboardColor('--bs-primary'),
            adminDashboardColor('--bs-success'),
            adminDashboardColor('--bs-info'),
            adminDashboardColor('--bs-warning'),
            adminDashboardColor('--bs-danger')
        ],
        noData: {
            text: 'Sem receitas pagas'
        },
        tooltip: {
            y: {
                formatter: adminDashboardMoney
            }
        }
    });

    adminFinanceAreaChart.render();
    adminFinanceBarChart.render();
    adminCategoryChart.render();
</script>
@endsection
