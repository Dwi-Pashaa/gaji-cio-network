@extends('layouts.app')

@section('title')
    Absensi
@endsection

@push('css')
    @if ($isAbsenOn == true)
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
        <style>
            .icon-md {
                width: 2rem;
                height: 2rem;
            }
            
            .bg-primary-lt {
                background-color: rgba(32, 107, 196, 0.1) !important;
            }
            
            .bg-info-lt {
                background-color: rgba(66, 153, 225, 0.1) !important;
            }

            /* Custom marker style */
            .custom-marker {
                background: transparent;
                border: none;
            }

            /* Leaflet popup custom style */
            .leaflet-popup-content-wrapper {
                border-radius: 8px;
            }
        </style>
        <style>
            .table td {
                vertical-align: middle;
                min-height: 80px;
            }
            .table thead th {
                background-color: #f8f9fa;
                font-weight: 600;
            }
            .table td {
                vertical-align: middle;
            }
            
            .table thead th {
                position: sticky;
                top: 0;
                z-index: 10;
            }
            
            .badge {
                font-size: 0.75rem;
                padding: 0.35rem 0.5rem;
            }
            
            .card-sm {
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                transition: transform 0.2s;
            }
            
            .card-sm:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 6px rgba(0,0,0,0.15);
            }
        </style>
    @endif
@endpush

@section('content')
    @if ($isAbsenOn == true)
        @include('pages.absen.partials.absen-on')
    @else
        @include('pages.absen.partials.absen-off')
    @endif
@endsection

@push('modal')
<div class="modal modal-blur fade" id="modal-simple" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Absensi Hari {{ \Carbon\Carbon::now()->translatedFormat('l') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Loading State -->
                <div id="loadingState" style="display: none;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h3 class="mb-2">Mendeteksi Lokasi</h3>
                        <p class="text-muted">Mohon tunggu sebentar...</p>
                    </div>
                </div>

                <!-- Permission Denied State -->
                <div id="permissionDenied" style="display: none;">
                    <div class="text-center py-5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-warning mb-3" width="64" height="64" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 9v4"></path>
                            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                            <path d="M12 16h.01"></path>
                        </svg>
                        <h3 class="mb-2">Akses Lokasi Dibutuhkan</h3>
                        <p class="text-muted mb-4">Mohon aktifkan akses lokasi untuk melakukan absensi.<br>Klik tombol di bawah untuk mengaktifkan akses lokasi.</p>
                        
                        <div class="alert alert-info text-start mb-4" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                        <path d="M12 9h.01"></path>
                                        <path d="M11 12h1v4h1"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="alert-title">Cara Mengaktifkan Lokasi:</h4>
                                    <div class="text-muted">
                                        <ol class="mb-0 ps-3">
                                            <li>
                                                Buka Chrome, <b>klik menu tiga titik (⋮)</b> di pojok kanan atas, lalu pilih <b>Pengaturan</b>.
                                            </li>
                                            <li>
                                                Pilih Privasi dan keamanan, lalu Pengaturan Situs.
                                            </li>
                                            <li>
                                                Gulir ke bawah dan klik Lokasi.
                                            </li>
                                            <li>
                                                Aktifkan opsi <b>"Situs dapat meminta izin untuk menggunakan lokasi Anda"</b> (disarankan) atau <b>"Izinkan"</b> untuk semua situs.
                                            </li>
                                            <li>
                                                Anda bisa mengatur situs mana yang diizinkan (Add) atau diblokir (Block) di bagian bawah menu tersebut. 
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div id="mainContent" style="display: none;">
                    <div class="row g-3">
                        <!-- Informasi Waktu -->
                        <div class="col-md-6">
                            <div class="card bg-primary-lt">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-primary me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                            <path d="M12 7v5l3 3"></path>
                                        </svg>
                                        <div>
                                            <div class="text-muted small">Waktu Sekarang</div>
                                            <div class="fw-bold" id="currentTime">--:--:--</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Tanggal -->
                        <div class="col-md-6">
                            <div class="card bg-info-lt">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md text-info me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path>
                                            <path d="M16 3v4"></path>
                                            <path d="M8 3v4"></path>
                                            <path d="M4 11h16"></path>
                                            <path d="M11 15h1"></path>
                                            <path d="M12 15v3"></path>
                                        </svg>
                                        <div>
                                            <div class="text-muted small">Tanggal</div>
                                            <div class="fw-bold" id="currentDate">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Peta Lokasi -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Lokasi Anda</h3>
                                </div>
                                <div class="card-body p-0">
                                    <div id="map" style="height: 350px; width: 100%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Lokasi -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small mb-1">Latitude</label>
                                                <div class="fw-bold" id="latitude-show">-</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-muted small mb-1">Longitude</label>
                                                <div class="fw-bold" id="longitude-show">-</div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-0">
                                                <label class="form-label text-muted small mb-1">Alamat</label>
                                                <div class="fw-bold" id="address">Mendeteksi lokasi...</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="text" name="date" id="date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" hidden>
                <input type="text" name="check_in" id="check_in" value="{{ \Carbon\Carbon::now()->format('H:i:s') }}" hidden>
                <input type="text" name="latitude" id="latitude" hidden>
                <input type="text" name="longitude" id="longitude" hidden>
            </div>
            <div class="modal-footer" id="modalFooter" style="display: none;">
                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="storeBtn" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 12l5 5l10 -10"></path>
                    </svg>
                    Simpan Absensi
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('js')
    <script>
        const BASE = "{{ route('absen.list.index') }}";

        let params = new URLSearchParams(window.location.search);
        $("#sort").change(function() {
            params.set('sort', $(this).val());
            window.location.href = BASE + '?' + params.toString();
        });

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });
    </script>
    @if ($isAbsenOn == true)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
        <script>
            function absenToday() {
                $("#modal-simple").modal("show");
            }
        </script>
        <script>
            let map, marker;
            let currentLat, currentLng;
            let timeInterval;

            function showLoadingState() {
                document.getElementById('loadingState').style.display = 'block';
                document.getElementById('permissionDenied').style.display = 'none';
                document.getElementById('mainContent').style.display = 'none';
                document.getElementById('modalFooter').style.display = 'none';
            }

            function showPermissionDenied() {
                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('permissionDenied').style.display = 'block';
                document.getElementById('mainContent').style.display = 'none';
                document.getElementById('modalFooter').style.display = 'none';
            }

            function showMainContent() {
                document.getElementById('loadingState').style.display = 'none';
                document.getElementById('permissionDenied').style.display = 'none';
                document.getElementById('mainContent').style.display = 'block';
                document.getElementById('modalFooter').style.display = 'flex';
            }

            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                });
                document.getElementById('currentTime').textContent = timeString;
            }

            function initMap() {
                if (!navigator.geolocation) {
                    alert('Geolocation tidak didukung oleh browser Anda');
                    return;
                }

                showLoadingState();

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        currentLat = position.coords.latitude;
                        currentLng = position.coords.longitude;

                        document.getElementById('latitude-show').textContent = currentLat.toFixed(6);
                        document.getElementById('longitude-show').textContent = currentLng.toFixed(6);

                        document.getElementById('latitude').value = currentLat.toFixed(6);
                        document.getElementById('longitude').value = currentLng.toFixed(6);

                        map = L.map('map').setView([currentLat, currentLng], 16);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors',
                            maxZoom: 19
                        }).addTo(map);

                        const customIcon = L.divIcon({
                            className: 'custom-marker',
                            html: `<div style="background: #206bc4; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>`,
                            iconSize: [30, 30],
                            iconAnchor: [15, 15]
                        });

                        marker = L.marker([currentLat, currentLng], { icon: customIcon }).addTo(map);
                        
                        L.circle([currentLat, currentLng], {
                            color: '#206bc4',
                            fillColor: '#206bc4',
                            fillOpacity: 0.1,
                            radius: position.coords.accuracy
                        }).addTo(map);

                        marker.bindPopup(`<b>Lokasi Anda</b><br>Lat: ${currentLat.toFixed(6)}<br>Lng: ${currentLng.toFixed(6)}`).openPopup();

                        getAddress(currentLat, currentLng);
                        showMainContent();
                    },
                    (error) => {
                        console.error('Geolocation error:', error);
                        if (error.code === error.PERMISSION_DENIED) {
                            showPermissionDenied();
                        } else {
                            showPermissionDenied();
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            }

            function getAddress(lat, lng) {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
                    .then(response => response.json())
                    .then(data => {
                        const address = data.display_name || 'Alamat tidak ditemukan';
                        document.getElementById('address').textContent = address;
                    })
                    .catch(error => {
                        console.error('Error fetching address:', error);
                        document.getElementById('address').textContent = 'Gagal mendapatkan alamat';
                    });
            }

            // Event listener saat modal dibuka
            document.getElementById('modal-simple').addEventListener('shown.bs.modal', function () {
                initMap();
                updateTime();
                timeInterval = setInterval(updateTime, 1000);
            });

            // Event listener saat modal ditutup
            document.getElementById('modal-simple').addEventListener('hidden.bs.modal', function () {
                if (timeInterval) {
                    clearInterval(timeInterval);
                }
                if (map) {
                    map.remove();
                    map = null;
                }
            });

            // Event listener untuk tombol simpan
            document.getElementById('storeBtn').addEventListener('click', function() {
                if (!currentLat || !currentLng) {
                    alert('Lokasi belum terdeteksi. Mohon tunggu sebentar.');
                    return;
                }

                const attendanceData = {
                    date: document.getElementById('date').value,
                    check_in: document.getElementById('check_in').value,
                    latitude: document.getElementById('latitude').value,
                    longitude: document.getElementById('longitude').value,
                };
                
                $.ajax({
                    url: "{{ route('absen.list.store') }}",
                    type: "POST",
                    data: attendanceData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });

                        $("#modal-simple").modal("hide");

                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    },
                    error: function(xhr) {
                        Toast.fire({
                            icon: "error",
                            title: xhr.responseJSON.message
                        });
                        console.error('Error:', xhr);
                    }
                });
            });
        </script>
    @endif
@endpush