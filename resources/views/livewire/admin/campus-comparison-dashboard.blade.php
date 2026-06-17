<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🏫 Multi-Campus Intelligence</h1>
            <p class="text-sm text-gray-500 mt-1">Compare performance metrics across all campus branches.</p>
        </div>
        <div class="flex gap-2">
            @foreach(['week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $p => $label)
            <button wire:click="setPeriod('{{ $p }}')"
                class="px-3 py-1.5 text-xs font-medium rounded-lg transition {{ $period === $p ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Metric selector --}}
    <div class="flex gap-2 flex-wrap">
        @foreach([
            'students'   => '👥 Students',
            'staff'      => '👨‍🏫 Staff',
            'courses'    => '📚 Courses',
            'attendance' => '✅ Attendance',
            'revenue'    => '💰 Revenue',
        ] as $m => $label)
        <button wire:click="setMetric('{{ $m }}')"
            class="px-4 py-2 text-sm font-medium rounded-xl transition {{ $metric === $m ? 'bg-indigo-600 text-white shadow' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    @if(count($campusData) === 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-12 text-center text-gray-400">
        <div class="text-5xl mb-3">🏫</div>
        <p class="text-lg font-medium">No branches found</p>
        <p class="text-sm mt-1">Create campus branches in Branch Management to see comparison data.</p>
    </div>
    @else

    {{-- Campus Cards --}}
    @php $maxVal = max(array_column($campusData, $metric) ?: [1]); @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($campusData as $campus)
        @php
            $val = $campus[$metric] ?? 0;
            $pct = $maxVal > 0 ? round(($val / $maxVal) * 100) : 0;
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $campus['name'] }}</h3>
                    <p class="text-xs text-gray-400">{{ $campus['city'] ?? '' }}</p>
                </div>
                <span class="text-3xl font-bold text-blue-600">
                    {{ $metric === 'revenue' ? '₨' . number_format($val) : number_format($val) }}
                </span>
            </div>

            {{-- Progress bar --}}
            <div class="mb-4">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
                <div class="text-xs text-gray-400 mt-1">{{ $pct }}% of best performing campus</div>
            </div>

            {{-- All metrics mini --}}
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                    <div class="font-bold text-gray-800 dark:text-white text-sm">{{ number_format($campus['students']) }}</div>
                    <div class="text-xs text-gray-400">Students</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                    <div class="font-bold text-gray-800 dark:text-white text-sm">{{ number_format($campus['courses']) }}</div>
                    <div class="text-xs text-gray-400">Courses</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                    <div class="font-bold text-gray-800 dark:text-white text-sm">{{ number_format($campus['attendance']) }}</div>
                    <div class="text-xs text-gray-400">Attendance</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Comparison Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-white">Full Comparison Table</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Campus</th>
                        <th class="px-4 py-3 text-right">Students</th>
                        <th class="px-4 py-3 text-right">Staff</th>
                        <th class="px-4 py-3 text-right">Courses</th>
                        <th class="px-4 py-3 text-right">Attendance</th>
                        <th class="px-4 py-3 text-right">Revenue (PKR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($campusData as $campus)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $campus['name'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campus['students']) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campus['staff']) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campus['courses']) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300">{{ number_format($campus['attendance']) }}</td>
                        <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">{{ number_format($campus['revenue']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
