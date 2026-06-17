<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🧠 Intelligent Academic Analytics</h1>
            <p class="text-sm text-gray-500 mt-1">AI-powered insights, risk detection, and performance trends.</p>
        </div>
        <div class="flex gap-2">
            @foreach(['week' => 'Week', 'month' => 'Month', 'semester' => 'Semester', 'year' => 'Year'] as $p => $label)
            <button wire:click="setPeriod('{{ $p }}')"
                class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ $period === $p ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700 flex-wrap">
        @foreach([
            'risk'            => '⚠️ At-Risk Students',
            'recommendations' => '🌟 Top Performers',
            'trends'          => '📈 Enrollment Trends',
            'cohort'          => '🎓 Course Completion',
        ] as $tab => $label)
        <button wire:click="setTab('{{ $tab }}')"
            class="px-4 py-2 text-sm font-medium border-b-2 transition {{ $activeTab === $tab ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- AT-RISK STUDENTS --}}
    @if($activeTab === 'risk')
    @if(count($atRiskStudents) === 0)
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-8 text-center">
        <div class="text-4xl mb-2">✅</div>
        <p class="text-green-700 dark:text-green-400 font-medium">No at-risk students identified in this period.</p>
        <p class="text-sm text-green-600 mt-1">All students have healthy attendance records (≥75%).</p>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
            <span class="text-red-500">⚠️</span>
            <h3 class="font-semibold text-gray-800 dark:text-white">Students at Risk (Attendance &lt;75%)</h3>
            <span class="ml-auto bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-medium">{{ count($atRiskStudents) }} students</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Student No.</th>
                        <th class="px-4 py-3 text-left">Attendance Rate</th>
                        <th class="px-4 py-3 text-left">Risk Level</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($atRiskStudents as $student)
                    <tr class="hover:bg-red-50 dark:hover:bg-red-900/10">
                        <td class="px-4 py-3 font-mono text-sm text-gray-900 dark:text-white">{{ $student['student_no'] }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 w-24">
                                    <div class="h-2 rounded-full {{ $student['attendance'] < 50 ? 'bg-red-500' : 'bg-orange-400' }}"
                                         style="width: {{ $student['attendance'] }}%"></div>
                                </div>
                                <span class="text-sm font-medium {{ $student['attendance'] < 50 ? 'text-red-600' : 'text-orange-600' }}">
                                    {{ $student['attendance'] }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $student['risk_level'] === 'HIGH' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $student['risk_level'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('counseling.cases') }}" class="text-xs px-3 py-1 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 font-medium transition">
                                Create Case
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endif

    {{-- TOP PERFORMERS --}}
    @if($activeTab === 'recommendations')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">🌟 Top Performers by Exam Score</h3>
            <div class="space-y-3">
                @forelse($topPerformers as $i => $student)
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-xl">
                    <span class="w-8 h-8 flex items-center justify-center rounded-full {{ $i < 3 ? 'bg-yellow-400 text-white' : 'bg-gray-200 text-gray-600' }} text-sm font-bold">
                        {{ $i < 3 ? ['🥇','🥈','🥉'][$i] : ($i+1) }}
                    </span>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-800 dark:text-white font-mono">{{ $student->student_id_no }}</div>
                        <div class="text-xs text-gray-400">{{ $student->exams_taken }} exams taken</div>
                    </div>
                    <span class="text-lg font-bold text-indigo-600">{{ round($student->avg_score, 1) }}%</span>
                </div>
                @empty
                <p class="text-gray-400 text-sm text-center py-6">No exam results in this period.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl shadow p-6 text-white">
            <h3 class="font-semibold mb-4">📊 Period Summary</h3>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="opacity-80">At-Risk Students</span>
                    <span class="font-bold">{{ count($atRiskStudents) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="opacity-80">Top Performers</span>
                    <span class="font-bold">{{ count($topPerformers) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="opacity-80">Course Completion Rate</span>
                    <span class="font-bold">
                        @php $avgRate = count($courseCompletion) > 0 ? round(array_sum(array_column($courseCompletion, 'rate')) / count($courseCompletion), 1) : 0; @endphp
                        {{ $avgRate }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ENROLLMENT TRENDS --}}
    @if($activeTab === 'trends')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Enrollment Chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">📈 Monthly Enrollment (12 months)</h3>
            @if(count($enrollmentTrend) > 0)
            @php $maxEnroll = max($enrollmentTrend) ?: 1; @endphp
            <div class="flex items-end gap-2 h-40">
                @foreach($enrollmentTrend as $month => $count)
                @php $pct = round(($count / $maxEnroll) * 100); @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-xs font-bold text-indigo-600">{{ $count }}</span>
                    <div class="w-full bg-indigo-500 rounded-t transition-all" style="height: {{ max($pct, 4) }}px" title="{{ $month }}: {{ $count }}"></div>
                    <span class="text-xs text-gray-400 rotate-45 origin-left" style="font-size:9px">{{ substr($month, 5) }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-400 text-sm text-center py-8">No enrollment data in this period.</p>
            @endif
        </div>

        {{-- Revenue Chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-4">💰 Monthly Revenue (12 months)</h3>
            @if(count($revenueTrend) > 0)
            @php $maxRev = max($revenueTrend) ?: 1; @endphp
            <div class="flex items-end gap-2 h-40">
                @foreach($revenueTrend as $month => $rev)
                @php $pct = round(($rev / $maxRev) * 100); @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-xs font-bold text-emerald-600" style="font-size:9px">{{ number_format($rev/1000, 0) }}k</span>
                    <div class="w-full bg-emerald-500 rounded-t transition-all" style="height: {{ max($pct, 4) }}px" title="{{ $month }}: ₨{{ number_format($rev) }}"></div>
                    <span class="text-xs text-gray-400" style="font-size:9px">{{ substr($month, 5) }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-400 text-sm text-center py-8">No revenue data in this period.</p>
            @endif
        </div>
    </div>
    @endif

    {{-- COURSE COMPLETION --}}
    @if($activeTab === 'cohort')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">🎓 Course Completion Rates</h3>
        </div>
        <div class="p-6 space-y-4">
            @forelse($courseCompletion as $course)
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-700 dark:text-gray-300 truncate max-w-xs">{{ $course['title'] }}</span>
                    <span class="text-gray-500 font-semibold ml-4">{{ $course['completed'] }}/{{ $course['total'] }} ({{ $course['rate'] }}%)</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all {{ $course['rate'] >= 75 ? 'bg-green-500' : ($course['rate'] >= 50 ? 'bg-yellow-400' : 'bg-red-400') }}"
                         style="width: {{ $course['rate'] }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-8">No course completion data available.</p>
            @endforelse
        </div>
    </div>
    @endif

</div>
