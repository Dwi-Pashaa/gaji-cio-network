@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@push('css')
    
@endpush

@section('content')
    @can('slip gaji karyawan')
        @if ($isAbsenOn == true)
            @include('pages.dashboard.partials.dashboard-on-absen')
        @else
            @include('pages.dashboard.partials.dashboard-off-absen')
        @endif
    @endcan
    @role("Admin")
        <div class="row row-cards">
            @php
                $colors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-info', 'bg-secondary'];
            @endphp

            @foreach ($type as $index => $item)
                @php
                    $color = $colors[$index % count($colors)];
                @endphp

                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="{{ $color }} text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                            width="24" height="24" viewBox="0 0 24 24" 
                                            fill="none" stroke="currentColor" stroke-width="2" 
                                            stroke-linecap="round" stroke-linejoin="round" 
                                            class="icon icon-1">
                                            <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2"></path>
                                            <path d="M12 3v3m0 12v3"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">{{ $item->name }}</div>
                                    <div class="text-secondary">Rp{{ number_format($item->amount, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    @endrole
@endsection

@push('js')
    
@endpush