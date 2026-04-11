@extends('layouts.app')

@section('content')
<section class="page-shell pt-24 pb-12">
    <h2 class="text-4xl font-extrabold mb-12 text-center">Real-Time Impact Dashboard</h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100 lg:col-span-2">
            <h3 class="text-xl font-bold mb-6 text-gray-800">Food Saved Monthly (tons)</h3>
            <div class="h-64 flex items-end gap-4">
                @forelse($monthlyImpact as $item)
                    @php 
                        $maxKg = $monthlyImpact->max('kg') ?: 1;
                        $heightPercent = ($item['kg'] / $maxKg) * 100;
                    @endphp
                    <div class="flex-1 bg-emerald-50 rounded-t-xl relative group transition-all duration-300 hover:bg-emerald-100"
                         style="height: {{ max(15, $heightPercent) }}%">
                        <div class="absolute inset-x-0 bottom-0 bg-emerald-600 rounded-t-xl shadow-lg" style="height: 100%"></div>
                        <div class="opacity-0 group-hover:opacity-100 absolute -top-10 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-2 py-1 rounded text-[10px] font-bold z-10 whitespace-nowrap">
                            {{ $item['kg'] }} kg
                        </div>
                    </div>
                @empty
                    <div class="w-full flex items-center justify-center p-12 text-gray-400 italic text-sm">
                        No redistribution data available for this period yet.
                    </div>
                @endforelse
            </div>
            @if($monthlyImpact->count())
                <div class="flex justify-between mt-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    @foreach($monthlyImpact as $item)
                        <span>{{ $item['month'] }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-emerald-600 p-8 rounded-3xl shadow-xl text-white">
            <h3 class="text-xl font-bold mb-8 text-emerald-100 border-b border-emerald-500/30 pb-4">Contribution Summary</h3>
            <div class="space-y-8">
                <div>
                    <p class="text-xs font-bold opacity-80 uppercase tracking-widest mb-1">CO2 Equivalent Saved</p>
                    <p class="text-4xl font-black">{{ number_format($stats['co2_offset_kg'], 1) }} <span class="text-lg font-normal opacity-60">kg</span></p>
                </div>
                <div class="pt-6 border-t border-emerald-500/30">
                    <p class="text-xs font-bold opacity-80 uppercase tracking-widest mb-1">Lives Impacted</p>
                    <p class="text-4xl font-black">{{ number_format($stats['total_claims'] * 15) }}</p>
                </div>
                <div class="pt-6 border-t border-emerald-500/30">
                    <p class="text-xs font-bold opacity-80 uppercase tracking-widest mb-1">Estimated Meals</p>
                    <p class="text-4xl font-black">{{ number_format($stats['estimated_meals']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
            <h3 class="text-xl font-bold text-gray-800">Top Community Contributors</h3>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Leaderboard</span>
        </div>
        <div class="p-0">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-6 text-xs font-bold text-gray-400 uppercase tracking-wider">Partner</th>
                        <th class="p-6 text-xs font-bold text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="p-6 text-xs font-bold text-gray-400 uppercase tracking-wider">Donations</th>
                        <th class="p-6 text-xs font-bold text-gray-400 uppercase tracking-wider">Contribution</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($topContributors as $contributor)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-6 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                                    {{ substr($contributor['name'], 0, 1) }}
                                </div>
                                <span class="font-bold text-gray-900">{{ $contributor['name'] }}</span>
                            </td>
                            <td class="p-6">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $contributor['type'] === 'donor' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $contributor['type'] }}
                                </span>
                            </td>
                            <td class="p-6 font-bold text-gray-700">
                                {{ $contributor['donations_count'] }}
                            </td>
                            <td class="p-6">
                                <span class="text-emerald-600 font-black">{{ $contributor['total_kg'] }} kg</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
