@extends('layouts.app')
@section('title', $pet->name . ' Health')
@section('page-title', $pet->name . ' — Health')

@section('content')
<div class="space-y-6" x-data="{ tab: 'records' }">

{{-- Header --}}
<div class="flex items-center gap-4">
    <img src="{{ $pet->avatar_url }}" alt="{{ $pet->name }}" class="w-14 h-14 rounded-2xl object-cover shadow-md flex-shrink-0">
    <div class="flex-1">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $pet->name }}</h2>
        <p class="text-slate-500 text-sm capitalize">{{ $pet->species }}{{ $pet->breed ? ' · '.$pet->breed : '' }}</p>
    </div>
    <a href="{{ route('pets.show', $pet) }}" class="btn-secondary btn-sm">← Back to Pet</a>
</div>

{{-- Weight chart --}}
@if($weightHistory->count() > 1)
<div class="card p-5">
    <h3 class="font-bold text-slate-900 dark:text-white mb-4">Weight History</h3>
    <canvas id="weightChart" height="70"></canvas>
</div>
@endif

{{-- Tabs --}}
<div class="flex gap-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl w-fit">
    <button @click="tab='records'"
            :class="tab==='records' ? 'bg-white dark:bg-slate-700 shadow-sm text-slate-900 dark:text-white font-semibold' : 'text-slate-500'"
            class="px-5 py-2 rounded-lg text-sm transition-all">Medical Records</button>
    <button @click="tab='vaccinations'"
            :class="tab==='vaccinations' ? 'bg-white dark:bg-slate-700 shadow-sm text-slate-900 dark:text-white font-semibold' : 'text-slate-500'"
            class="px-5 py-2 rounded-lg text-sm transition-all">Vaccinations</button>
</div>

{{-- Medical Records --}}
<div x-show="tab==='records'" class="space-y-4">
    <div class="card" x-data="{ open: false }">
        <button @click="open=!open" class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
            <span class="font-semibold text-slate-900 dark:text-white">+ Add Medical Record</span>
            <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-transition class="border-t border-slate-100 dark:border-slate-800 p-5">
            <form method="POST" action="{{ route('health.medical.store', $pet) }}" enctype="multipart/form-data"
                  class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div><label class="form-label">Type *</label>
                    <select name="type" required class="form-input">
                        @foreach(['checkup'=>'Checkup','illness'=>'Illness','surgery'=>'Surgery','prescription'=>'Prescription','weight_log'=>'Weight Log','symptom'=>'Symptom','other'=>'Other'] as $v=>$l)
                            <option value="{{ $v }}">{{ $l }}</option>
                        @endforeach
                    </select></div>
                <div><label class="form-label">Title *</label>
                    <input type="text" name="title" required class="form-input" placeholder="e.g. Annual checkup"></div>
                <div><label class="form-label">Date *</label>
                    <input type="date" name="record_date" value="{{ today()->toDateString() }}" required class="form-input"></div>
                <div><label class="form-label">Weight (kg)</label>
                    <input type="number" name="weight" step="0.1" min="0" class="form-input" placeholder="{{ $pet->weight }}"></div>
                <div><label class="form-label">Vet Name</label>
                    <input type="text" name="vet_name" class="form-input" value="{{ $pet->vet_name }}"></div>
                <div><label class="form-label">Attachment (PDF/Image)</label>
                    <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" class="form-input"></div>
                <div class="sm:col-span-2"><label class="form-label">Description</label>
                    <textarea name="description" rows="2" class="form-input" placeholder="Details..."></textarea></div>
                <div><label class="form-label">Diagnosis</label>
                    <textarea name="diagnosis" rows="2" class="form-input"></textarea></div>
                <div><label class="form-label">Treatment</label>
                    <textarea name="treatment" rows="2" class="form-input"></textarea></div>
                <div class="sm:col-span-2">
                    <button type="submit" class="btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>

    @if($pet->medicalRecords->count() > 0)
    <div class="space-y-3">
        @foreach($pet->medicalRecords as $record)
        @php $icons = ['checkup'=>'🩺','illness'=>'🤒','surgery'=>'🔬','prescription'=>'💊','weight_log'=>'⚖️','symptom'=>'🌡️','other'=>'📋']; @endphp
        <div class="card p-4 flex gap-4">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl flex-shrink-0
                        {{ $record->type === 'illness' ? 'bg-red-50' : ($record->type === 'surgery' ? 'bg-orange-50' : 'bg-blue-50') }}">
                {{ $icons[$record->type] ?? '📋' }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-semibold text-slate-900 dark:text-white">{{ $record->title }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $record->record_date->format('M j, Y') }}{{ $record->vet_name ? ' · '.$record->vet_name : '' }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($record->weight)<span class="badge badge-gray">{{ $record->weight }} kg</span>@endif
                        <form method="POST" action="{{ route('health.medical.destroy', $record) }}" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @if($record->description)<p class="text-sm text-slate-600 dark:text-slate-300 mt-2">{{ $record->description }}</p>@endif
                @if($record->diagnosis)<p class="text-xs text-slate-500 mt-1"><strong>Diagnosis:</strong> {{ $record->diagnosis }}</p>@endif
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card p-10 text-center"><p class="text-slate-400">No medical records yet</p></div>
    @endif
</div>

{{-- Vaccinations --}}
<div x-show="tab==='vaccinations'" class="space-y-4">
    <div class="card" x-data="{ open: false }">
        <button @click="open=!open" class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
            <span class="font-semibold text-slate-900 dark:text-white">+ Add Vaccination Record</span>
            <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-transition class="border-t border-slate-100 dark:border-slate-800 p-5">
            <form method="POST" action="{{ route('health.vaccination.store', $pet) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div><label class="form-label">Vaccine Name *</label>
                    <input type="text" name="vaccine_name" required class="form-input" placeholder="e.g. Rabies"></div>
                <div><label class="form-label">Administered Date *</label>
                    <input type="date" name="administered_date" value="{{ today()->toDateString() }}" required class="form-input"></div>
                <div><label class="form-label">Next Due Date</label>
                    <input type="date" name="next_due_date" class="form-input"></div>
                <div><label class="form-label">Administered By</label>
                    <input type="text" name="administered_by" class="form-input" value="{{ $pet->vet_name }}"></div>
                <div><label class="form-label">Batch Number</label>
                    <input type="text" name="batch_number" class="form-input"></div>
                <div><label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-input"></textarea></div>
                <div class="sm:col-span-2">
                    <button type="submit" class="btn-primary">Save Vaccination</button>
                </div>
            </form>
        </div>
    </div>

    @if($pet->vaccinations->count() > 0)
    <div class="card divide-y divide-slate-50 dark:divide-slate-800">
        @foreach($pet->vaccinations as $vax)
        <div class="px-5 py-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl {{ $vax->is_overdue ? 'bg-red-50' : 'bg-indigo-50' }} flex items-center justify-center text-xl flex-shrink-0">💉</div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-900 dark:text-white">{{ $vax->vaccine_name }}</p>
                <p class="text-xs text-slate-400">Given: {{ $vax->administered_date->format('M j, Y') }}{{ $vax->administered_by ? ' by '.$vax->administered_by : '' }}</p>
            </div>
            @if($vax->next_due_date)
            <span class="badge {{ $vax->is_overdue ? 'badge-danger' : ($vax->days_until_due <= 30 ? 'badge-warning' : 'badge-success') }}">
                {{ $vax->is_overdue ? 'Overdue' : 'Due '.$vax->next_due_date->format('M j, Y') }}
            </span>
            @endif
            <form method="POST" action="{{ route('health.vaccination.destroy', $vax) }}" onsubmit="return confirm('Delete?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @else
    <div class="card p-10 text-center"><p class="text-slate-400">No vaccination records yet</p></div>
    @endif
</div>

</div>
@endsection

@push('scripts')
@if($weightHistory->count() > 1)
<script>
new Chart(document.getElementById('weightChart').getContext('2d'), {
    type:'line',
    data:{
        labels: @json($weightHistory->pluck('record_date')->map(fn($d)=>\Carbon\Carbon::parse($d)->format('M j'))),
        datasets:[{
            label:'Weight (kg)',
            data: @json($weightHistory->pluck('weight')),
            borderColor:'#4F46E5', backgroundColor:'rgba(79,70,229,.08)',
            fill:true, tension:.4, pointBackgroundColor:'#4F46E5', pointRadius:5, pointHoverRadius:7
        }]
    },
    options:{
        responsive:true,
        plugins:{ legend:{display:false} },
        scales:{
            x:{ grid:{display:false}, border:{display:false} },
            y:{ grid:{color:'#f1f5f9'}, border:{display:false} }
        }
    }
});
</script>
@endif
@endpush
