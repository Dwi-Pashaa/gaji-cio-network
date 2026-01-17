@extends('layouts.app')

@section('title')
    Set Absensi - {{ $user->name }}
@endsection

@push('css')
    
@endpush

@section('content')
    @include('components.alert.success')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <b>
                Pengaturan Waktu Absensi Kerja
            </b>
            <form action="{{ route('user.setting.toggleActiveAbsen') }}" id="form-switch" method="POST">
                @csrf

                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <input type="hidden" name="value" value="{{ $isAbsenOn == true ? 'inactive' : 'active' }}">

                <button
                    type="button"
                    class="btn btn-warning"
                    onclick="document.getElementById('form-switch').submit()"
                >
                    {{ $isAbsenOn == true ? 'Nonaktifkan Fitur Absen' : 'Aktifkan Fitur Absen' }}
                </button>
            </form>
        </div>
        <form action="{{ route('user.setting.store') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <div class="card-body border-bottom py-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">Pilih Lokasi Absensi</label>
                            <select name="koordinat_id" id="koordinat_id" class="form-control @error('koordinat_id') is-invalid @enderror">
                                <option value="">Pilih</option>    
                                @foreach ($coordinat as $crd)
                                    <option value="{{ $crd->id }}" {{ old('koordinat_id', $setting?->koordinat_id) == $crd->id ? 'selected' : '' }}>{{ $crd->name }} ( {{ $crd->radius }} Meter )</option>
                                @endforeach
                            </select> 
                            @error('start_work')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Jam Mulai Absensi</label>
                            <input value="{{ $setting->start_work ?? '' }}" type="time" name="start_work" id="start_work" class="form-control @error('start_work') is-invalid @enderror">  
                            @error('start_work')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">Jam Selesai Absensi</label>
                            <input value="{{ $setting->end_work ?? '' }}" type="time" name="end_work" id="end_work" class="form-control @error('end_work') is-invalid @enderror">  
                            @error('end_work')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-header">
                <b>
                    Pengaturan Potongan Gaji
                </b>
            </div>
            <div class="card-body border-bottom py-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group mb-3">
                            <label class="form-label">Potongan Tidak Masuk</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input value="{{ number_format($setting->alpha ?? 0, 0, ',', '.') }}" type="text" name="alpha" id="money-alpha" class="form-control money-input @error('alpha') is-invalid @enderror">  
                                @error('alpha')    
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span> 
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group mb-3">
                            <label class="form-label">Potongan Izin/Cuti</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input value="{{ number_format($setting->cuti ?? 0, 0, ',', '.') }}" type="text" name="cuti" id="money-cuti" class="form-control money-input @error('cuti') is-invalid @enderror">  
                                @error('cuti')    
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span> 
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group mb-3">
                            <label class="form-label">Potongan Telat Melakukan Absensi</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input value="{{ number_format($setting->telat ?? 0, 0, ',', '.') }}" type="text" name="telat" id="money-telat" class="form-control money-input @error('telat') is-invalid @enderror">  
                                @error('telat')    
                                    <span class="invalid-feedback">
                                        {{ $message }}
                                    </span> 
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary w-100">Simpan</button>
            </div>
        </form>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("money-alpha");

            input.addEventListener("input", function(e) {
                let value = this.value.replace(/\D/g, "");
                
                if (value) {
                    this.value = new Intl.NumberFormat("id-ID").format(value);
                } else {
                    this.value = "";
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("money-cuti");

            input.addEventListener("input", function(e) {
                let value = this.value.replace(/\D/g, "");
                
                if (value) {
                    this.value = new Intl.NumberFormat("id-ID").format(value);
                } else {
                    this.value = "";
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("money-telat");

            input.addEventListener("input", function(e) {
                let value = this.value.replace(/\D/g, "");
                
                if (value) {
                    this.value = new Intl.NumberFormat("id-ID").format(value);
                } else {
                    this.value = "";
                }
            });
        });
    </script>
@endpush