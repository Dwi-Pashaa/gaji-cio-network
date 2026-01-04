@extends('layouts.app')

@section('title')
    Hari Kerja User
@endsection

@push('css')
    
@endpush

@section('content')
@include('components.alert.success')
<div class="card">
    <div class="card-header">
        <a href="{{ route('user.index') }}" class="btn btn-primary">
            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevrons-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M11 7l-5 5l5 5" /><path d="M17 7l-5 5l5 5" /></svg>
            Kembali
        </a>
    </div>
    <form action="{{ route('user.saveWorkDay', ['id' => $user->id]) }}" method="POST">
        @csrf
        <div class="card-body border-bottom py-3">
            @csrf
            @method("PUT")
            @php
                $workday = [
                    ['key' => 0, 'value' => 'Minggu'],
                    ['key' => 1, 'value' => 'Senin'],
                    ['key' => 2, 'value' => 'Selasa'],
                    ['key' => 3, 'value' => 'Rabu'],
                    ['key' => 4, 'value' => 'Kamis'],
                    ['key' => 5, 'value' => 'Jumat'],
                    ['key' => 6, 'value' => 'Sabtu'],
                ];
                $selectedWorkdays = $user->workday->pluck('weekday')->toArray();
            @endphp
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <h3 class="mb-2">Hari Kerja</h3>
                    <p class="text-muted small">Pilih hari-hari kerja yang berlaku untuk {{ $user->name }}.</p>
                </div>
                @foreach($workday as $wd)
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('weekday') is-invalid @enderror" id="workday_{{ $wd['key'] }}" name="weekday[]" value="{{ $wd['key'] }}" type="checkbox" @checked(in_array($wd['key'], $selectedWorkdays))>
                            <label class="form-check-label" for="workday_{{ $wd['key'] }}">
                                {{ $wd['value'] }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary w-100">Simpan</button>
        </div>
    </form>
</div>
@endsection

@push('js')
    
@endpush