@extends('layouts.admin', ['pageTitle' => 'शाखा तथा राजस्व एनालिटिक्स'])

@section('content')
<div class="reports-container pb-5">
    <!-- Header Banner -->
    <div class="card text-white mb-4 border-0 overflow-hidden shadow-sm barhadashi-dashboard-banner">
        <div class="barhadashi-banner-bg" style="background-image: url('{{ asset('images/barhadashi_building.jpg') }}');"></div>
        <div class="barhadashi-banner-overlay"></div>
        <div class="card-body p-4 barhadashi-banner-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="barhadashi-badge-pill">
                            <i data-lucide="bar-chart-3" style="width: 14px; height: 14px;"></i>
                            सुशासन तथा राजस्व प्रतिवेदन • बाह्रदशी गाउँपालिका
                        </span>
                    </div>
                    <h2 class="fw-extrabold text-white mb-2" style="font-size: 1.65rem;">शाखागत कार्यसम्पादन तथा राजस्व विश्लेषण</h2>
                    <p class="text-white-50 mb-0" style="color: rgba(255, 255, 255, 0.92) !important; max-width: 650px; font-size: 0.92rem; line-height: 1.5;">
                        विभिन्न शाखाहरूबाट सम्पादित नागरिक सेवाहरू, संकलित सरकारी राजस्व, बाँकी बक्यौता तथा कार्यसम्पादन प्रगतिको एकीकृत डिजिटल विवरण।
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div class="d-flex flex-wrap gap-2 justify-content-lg-end no-print">
                        <div class="dropdown">
                            <button class="btn btn-light fw-bold px-3 py-2 text-primary shadow-sm dropdown-toggle d-inline-flex align-items-center gap-2" data-bs-toggle="dropdown">
                                <i data-lucide="download" style="width: 16px; height: 16px;"></i>
                                <span>Excel/CSV निर्यात</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.reports.export-csv', array_merge(request()->query(), ['type' => 'applications'])) }}">
                                        <i data-lucide="file-spreadsheet" class="text-success" style="width: 16px; height: 16px;"></i>
                                        <span>विस्तृत निवेदन सूची (.csv)</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ route('admin.reports.export-csv', array_merge(request()->query(), ['type' => 'departments'])) }}">
                                        <i data-lucide="table" class="text-primary" style="width: 16px; height: 16px;"></i>
                                        <span>शाखागत सारांश (.csv)</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <button onclick="window.print()" class="btn btn-outline-light fw-semibold px-3 py-2 d-inline-flex align-items-center gap-2">
                            <i data-lucide="printer" style="width: 16px; height: 16px;"></i>
                            <span>प्रिन्ट गर्नुहोस्</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Control Card -->
    <div class="card border-0 shadow-sm mb-4 rounded-3 no-print">
        <div class="card-body p-3 p-lg-4">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">सुरु मिति (From Date)</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">अन्तिम मिति (To Date)</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">शाखा छनोट (Department)</label>
                    <select name="department_id" class="form-select form-select-sm">
                        <option value="">-- सम्पूर्ण शाखाहरू --</option>
                        @foreach($allDepartments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">निवेदन स्थिति (Status)</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- सबै स्थिति --</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>पेश गरिएको (Pending)</option>
                        <option value="under_review" {{ $status == 'under_review' ? 'selected' : '' }}>छानबिनमा (Under Review)</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>स्वीकृत (Approved)</option>
                        <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>अस्वीकृत (Rejected)</option>
                        <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>सम्पन्न (Completed)</option>
                    </select>
                </div>
                <div class="col-12 col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold d-flex align-items-center justify-content-center gap-1" title="फिल्टर लागू गर्नुहोस्">
                        <i data-lucide="filter" style="width: 14px; height: 14px;"></i>
                        <span>फिल्टर</span>
                    </button>
                    @if(request()->hasAny(['date_from', 'date_to', 'department_id', 'status']))
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm" title="फिल्टर हटाउनुहोस्">
                            <i data-lucide="rotate-ccw" style="width: 14px; height: 14px;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Core KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Collected Revenue -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 border-start border-4 border-success">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-success-subtle text-success fw-bold px-2 py-0.5 rounded-pill extra-small mb-1 d-inline-block">राजस्व संकलन</span>
                            <div class="fs-4 fw-extrabold text-dark">रु. {{ number_format($totalRevenue, 2) }}</div>
                            <div class="text-muted small fw-semibold">कुल प्राप्त सरकारी राजस्व</div>
                        </div>
                        <div class="p-2 rounded-3 bg-success-subtle text-success">
                            <i data-lucide="wallet" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                    <div class="extra-small text-muted border-top pt-2 mt-2">
                        <span>छानिएको अवधि भित्रको रकम</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Revenue -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 border-start border-4 border-warning">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-warning-subtle text-warning-emphasis fw-bold px-2 py-0.5 rounded-pill extra-small mb-1 d-inline-block">प्रक्रियामा / बक्यौता</span>
                            <div class="fs-4 fw-extrabold text-dark">रु. {{ number_format($pendingRevenue, 2) }}</div>
                            <div class="text-muted small fw-semibold">प्रमाणीकरण बाँकी राजस्व</div>
                        </div>
                        <div class="p-2 rounded-3 bg-warning-subtle text-warning-emphasis">
                            <i data-lucide="clock" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                    <div class="extra-small text-muted border-top pt-2 mt-2">
                        <span>पेन्डिङ तथा प्रमाणीकरणमा रहेको</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Applications -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 border-start border-4 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-0.5 rounded-pill extra-small mb-1 d-inline-block">कुल कार्यभार</span>
                            <div class="fs-4 fw-extrabold text-dark">{{ number_format($totalApps) }}</div>
                            <div class="text-muted small fw-semibold">कुल प्राप्त निवेदनहरू</div>
                        </div>
                        <div class="p-2 rounded-3 bg-primary-subtle text-primary">
                            <i data-lucide="file-text" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                    <div class="extra-small text-muted border-top pt-2 mt-2 d-flex justify-content-between">
                        <span>स्वीकृत: <strong class="text-success">{{ $approvedApps }}</strong></span>
                        <span>प्रक्रियामा: <strong class="text-warning">{{ $pendingApps }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 rounded-3 border-start border-4 border-info">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-info-subtle text-info fw-bold px-2 py-0.5 rounded-pill extra-small mb-1 d-inline-block">फर्छ्यौट दर</span>
                            <div class="fs-4 fw-extrabold text-dark">{{ $completionRate }}%</div>
                            <div class="text-muted small fw-semibold">सम्पन्नता / स्वीकृति अनुपात</div>
                        </div>
                        <div class="p-2 rounded-3 bg-info-subtle text-info">
                            <i data-lucide="check-check" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                    <div class="extra-small text-muted border-top pt-2 mt-2">
                        <span>अस्वीकृत: <strong class="text-danger">{{ $rejectedApps }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Department Bar Chart -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="bar-chart-2" class="text-primary" style="width: 18px; height: 18px;"></i>
                        <h6 class="mb-0 fw-bold text-dark">शाखागत निवेदन तथा राजस्व संकलन तुलना</h6>
                    </div>
                    <span class="badge bg-light text-muted border extra-small">शाखागत तुलना</span>
                </div>
                <div class="card-body p-3">
                    <div style="height: 320px; position: relative;">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Distribution Donut Chart -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="pie-chart" class="text-info" style="width: 18px; height: 18px;"></i>
                        <h6 class="mb-0 fw-bold text-dark">निवेदन स्थिति अनुपात</h6>
                    </div>
                    <span class="badge bg-light text-muted border extra-small">स्थिति</span>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-center">
                    <div style="height: 240px; position: relative;">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="row g-2 mt-3 text-center extra-small">
                        <div class="col-4">
                            <span class="d-inline-block rounded-circle bg-success me-1" style="width: 8px; height: 8px;"></span>
                            <span class="text-muted">स्वीकृत: <strong>{{ $statusDistribution['approved'] + $statusDistribution['completed'] }}</strong></span>
                        </div>
                        <div class="col-4">
                            <span class="d-inline-block rounded-circle bg-warning me-1" style="width: 8px; height: 8px;"></span>
                            <span class="text-muted">प्रक्रियामा: <strong>{{ $statusDistribution['pending'] + $statusDistribution['under_review'] }}</strong></span>
                        </div>
                        <div class="col-4">
                            <span class="d-inline-block rounded-circle bg-danger me-1" style="width: 8px; height: 8px;"></span>
                            <span class="text-muted">अस्वीकृत: <strong>{{ $statusDistribution['rejected'] }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Trend Line Chart -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="trending-up" class="text-success" style="width: 18px; height: 18px;"></i>
                        <h6 class="mb-0 fw-bold text-dark">दैनिक कार्यसम्पादन तथा राजस्व प्रवाह (Timeline Trend)</h6>
                    </div>
                    <span class="badge bg-light text-muted border extra-small">{{ $dateFrom }} देखि {{ $dateTo }} सम्म</span>
                </div>
                <div class="card-body p-3">
                    <div style="height: 280px; position: relative;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="row g-4">
        <!-- Department Performance Table -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="building-2" class="text-primary" style="width: 18px; height: 18px;"></i>
                        <h6 class="mb-0 fw-bold text-dark">शाखागत कार्यसम्पादन र राजस्व विवरण तालिका</h6>
                    </div>
                    <a href="{{ route('admin.reports.export-csv', array_merge(request()->query(), ['type' => 'departments'])) }}" class="btn btn-outline-primary btn-sm extra-small fw-semibold d-inline-flex align-items-center gap-1 no-print">
                        <i data-lucide="download" style="width: 14px; height: 14px;"></i>
                        <span>शाखा डेटा डाउनलोड</span>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light extra-small text-uppercase text-muted">
                            <tr>
                                <th class="ps-3" style="width: 60px;">क्र.सं.</th>
                                <th>शाखाको नाम</th>
                                <th>कोड</th>
                                <th class="text-center">कुल निवेदन</th>
                                <th class="text-center">स्वीकृत</th>
                                <th class="text-center">प्रक्रियामा</th>
                                <th class="text-center">अस्वीकृत</th>
                                <th class="text-end pe-3">संकलित राजस्व</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departmentStats as $idx => $d)
                                <tr>
                                    <td class="ps-3 text-muted fw-semibold">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $d['name'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill">{{ $d['code'] }}</span>
                                    </td>
                                    <td class="text-center fw-bold">{{ $d['total_applications'] }}</td>
                                    <td class="text-center text-success fw-semibold">{{ $d['approved'] }}</td>
                                    <td class="text-center text-warning-emphasis fw-semibold">{{ $d['pending'] }}</td>
                                    <td class="text-center text-danger fw-semibold">{{ $d['rejected'] }}</td>
                                    <td class="text-end pe-3 fw-bold text-dark">
                                        रु. {{ number_format($d['revenue'], 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">कुनै शाखागत विवरण फेला परेन।</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="ps-3 text-dark">जम्मा योग (Total):</td>
                                <td class="text-center text-dark">{{ $totalApps }}</td>
                                <td class="text-center text-success">{{ $approvedApps }}</td>
                                <td class="text-center text-warning-emphasis">{{ $pendingApps }}</td>
                                <td class="text-center text-danger">{{ $rejectedApps }}</td>
                                <td class="text-end pe-3 text-success">रु. {{ number_format($totalRevenue, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Requested Services Table -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i data-lucide="layers" class="text-info" style="width: 18px; height: 18px;"></i>
                        <h6 class="mb-0 fw-bold text-dark">सेवागत माग तथा राजस्व संकलन (Top Services)</h6>
                    </div>
                    <span class="badge bg-light text-muted border extra-small">कुल {{ count($serviceStats) }} सेवाहरू</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light extra-small text-uppercase text-muted">
                            <tr>
                                <th class="ps-3" style="width: 60px;">क्र.सं.</th>
                                <th>सेवाको नाम</th>
                                <th>सम्बन्धित शाखा</th>
                                <th class="text-end">सरकारी दस्तुर</th>
                                <th class="text-center">निवेदन संख्या</th>
                                <th class="text-end pe-3">संकलित रकम</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($serviceStats as $idx => $s)
                                <tr>
                                    <td class="ps-3 text-muted fw-semibold">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $s['name'] }}</div>
                                    </td>
                                    <td class="text-muted">{{ $s['department'] }}</td>
                                    <td class="text-end text-muted">रु. {{ number_format($s['fee'], 2) }}</td>
                                    <td class="text-center fw-bold">{{ $s['count'] }}</td>
                                    <td class="text-end pe-3 fw-bold text-success">रु. {{ number_format($s['revenue'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">कुनै सेवागत रेकर्ड फेला परेन।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js 4.x CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Department Comparison Chart (Bar)
    const deptCtx = document.getElementById('deptChart');
    if (deptCtx) {
        const deptNames = @json(array_column($departmentStats, 'name'));
        const deptApps = @json(array_column($departmentStats, 'total_applications'));
        const deptRevs = @json(array_column($departmentStats, 'revenue'));

        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: deptNames,
                datasets: [
                    {
                        label: 'निवेदन संख्या',
                        data: deptApps,
                        backgroundColor: 'rgba(5, 38, 78, 0.85)',
                        borderColor: '#05264E',
                        borderWidth: 1,
                        yAxisID: 'y',
                    },
                    {
                        label: 'राजस्व (रु.)',
                        data: deptRevs,
                        backgroundColor: 'rgba(16, 185, 129, 0.75)',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'निवेदन संख्या' },
                        beginAtZero: true
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'राजस्व रु.' },
                        beginAtZero: true,
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });
    }

    // 2. Status Donut Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const sDist = @json($statusDistribution);
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['स्वीकृत / सम्पन्न', 'छानबिनमा', 'पेश गरिएको', 'अस्वीकृत'],
                datasets: [{
                    data: [
                        sDist.approved + sDist.completed,
                        sDist.under_review,
                        sDist.pending,
                        sDist.rejected
                    ],
                    backgroundColor: [
                        '#10b981',
                        '#0ea5e9',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // 3. Daily Timeline Trend Chart (Line)
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        const tDates = @json($trendDates);
        const tApps = @json($trendAppCounts);
        const tRevs = @json($trendRevenues);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: tDates,
                datasets: [
                    {
                        label: 'दैनिक प्राप्त निवेदनहरू',
                        data: tApps,
                        borderColor: '#05264E',
                        backgroundColor: 'rgba(5, 38, 78, 0.1)',
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y'
                    },
                    {
                        label: 'दैनिक संकलित राजस्व (रु.)',
                        data: tRevs,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'निवेदन संख्या' },
                        beginAtZero: true
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'राजस्व रु.' },
                        beginAtZero: true,
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush
