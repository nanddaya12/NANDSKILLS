<div class="space-y-8" x-data="analyticsDashboard({
    revenueLabels: @js($revenueLabels),
    revenueValues: @js($revenueValues),
    enrollmentLabels: @js($enrollmentLabels),
    enrollmentValues: @js($enrollmentValues),
    gpaLabels: @js($gpaLabels),
    gpaValues: @js($gpaValues)
})">
    <!-- Top Header -->
    <div class="flex justify-between items-center bg-slate-900/60 p-6 rounded-2xl border border-white/5 backdrop-blur-md">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Business Intelligence & Analytics</h2>
            <p class="text-slate-400 text-sm mt-1">Multi-dimensional operational telemetry, financials progression, and academic performance indices.</p>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- KPI 1: Active Enrolled Students -->
        <div class="bg-slate-900/40 p-6 rounded-2xl border border-white/5 backdrop-blur-md">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Enrolled Students</h3>
            <p class="text-3xl font-black text-white mt-2">{{ $totalStudentsCount }}</p>
            <span class="text-xs text-emerald-400 mt-2 block font-semibold">▲ +12% from last quarter</span>
        </div>

        <!-- KPI 2: Total Revenue -->
        <div class="bg-slate-900/40 p-6 rounded-2xl border border-white/5 backdrop-blur-md">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Cash Collections</h3>
            <p class="text-3xl font-black text-white mt-2">${{ number_format($totalRevenueSum, 2) }}</p>
            <span class="text-xs text-emerald-400 mt-2 block font-semibold">▲ +8.4% monthly growth</span>
        </div>

        <!-- KPI 3: Average GPA index -->
        <div class="bg-slate-900/40 p-6 rounded-2xl border border-white/5 backdrop-blur-md">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Average Cumulative GPA</h3>
            <p class="text-3xl font-black text-white mt-2">{{ $averageGpa }} / 4.0</p>
            <span class="text-xs text-blue-400 mt-2 block font-semibold">ℹ Excellent academic tier</span>
        </div>

        <!-- KPI 4: Student Retention Rate -->
        <div class="bg-slate-900/40 p-6 rounded-2xl border border-white/5 backdrop-blur-md">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Student Retention Rate</h3>
            <p class="text-3xl font-black text-white mt-2">{{ $retentionRate }}%</p>
            <span class="text-xs text-emerald-400 mt-2 block font-semibold">▲ Highest record this year</span>
        </div>
    </div>

    <!-- Charts Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Revenue Line Chart -->
        <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Revenue Progression ($)</h3>
            <div class="h-80 relative">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Student Enrollment Bar Chart -->
        <div class="p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Student Enrollments Growth</h3>
            <div class="h-80 relative">
                <canvas id="enrollmentChart"></canvas>
            </div>
        </div>

        <!-- GPA Grade Distribution Donut Chart -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md space-y-4 max-w-xl mx-auto w-full">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider text-center">GPA Grade Range Distribution</h3>
            <div class="h-80 relative">
                <canvas id="gpaChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Include Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Chart initializer scripts -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('analyticsDashboard', (config) => ({
                revenueLabels: config.revenueLabels,
                revenueValues: config.revenueValues,
                enrollmentLabels: config.enrollmentLabels,
                enrollmentValues: config.enrollmentValues,
                gpaLabels: config.gpaLabels,
                gpaValues: config.gpaValues,

                init() {
                    this.$nextTick(() => {
                        this.initCharts();
                    });
                },

                initCharts() {
                    // 1. Revenue Progression Chart
                    const ctxRev = document.getElementById('revenueChart').getContext('2d');
                    new Chart(ctxRev, {
                        type: 'line',
                        data: {
                            labels: this.revenueLabels,
                            datasets: [{
                                label: 'Collected Revenue ($)',
                                data: this.revenueValues,
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94A3B8' } },
                                x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94A3B8' } }
                            }
                        }
                    });

                    // 2. Enrollment Growth Chart
                    const ctxEnroll = document.getElementById('enrollmentChart').getContext('2d');
                    new Chart(ctxEnroll, {
                        type: 'bar',
                        data: {
                            labels: this.enrollmentLabels,
                            datasets: [{
                                label: 'New Students',
                                data: this.enrollmentValues,
                                backgroundColor: '#A855F7',
                                borderRadius: 8,
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94A3B8' } },
                                x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94A3B8' } }
                            }
                        }
                    });

                    // 3. GPA Grade Distribution Chart
                    const ctxGpa = document.getElementById('gpaChart').getContext('2d');
                    new Chart(ctxGpa, {
                        type: 'doughnut',
                        data: {
                            labels: this.gpaLabels,
                            datasets: [{
                                data: this.gpaValues,
                                backgroundColor: [
                                    '#10B981', // green
                                    '#3B82F6', // blue
                                    '#F59E0B', // amber
                                    '#EF4444', // rose
                                    '#64748B'  // slate
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: '#94A3B8', boxWidth: 15 }
                                }
                            }
                        }
                    });
                }
            }));
        });
    </script>
</div>
