@extends('layouts.app')

@section('title')
    Data Koordinat Absensi
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-geosearch/3.11.1/geosearch.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .info-box label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .info-box .value {
            color: #212529;
            font-size: 14px;
        }
        
        .geosearch {
            position: relative;
        }
        
        .leaflet-control-geosearch {
            border-radius: 8px;
        }

        /* Custom Range Slider */
        .range-slider {
            width: 100%;
            margin: 10px 0;
        }

        .range-slider input[type="range"] {
            width: 100%;
            height: 8px;
            border-radius: 5px;
            background: #d3d3d3;
            outline: none;
            -webkit-appearance: none;
        }

        .range-slider input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #206bc4;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .range-slider input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #206bc4;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .range-slider input[type="range"]::-webkit-slider-thumb:hover {
            background: #1a5aa8;
            transform: scale(1.1);
        }

        .range-slider input[type="range"]::-moz-range-thumb:hover {
            background: #1a5aa8;
            transform: scale(1.1);
        }

        .range-value {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        .radius-display-box {
            background: #206bc4;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="card">
        @can('buat koordinat')
            <div class="card-header">
                <a href="javascript:void(0)" id="addBtn" data-bs-toggle="modal" data-bs-target="#modal-simple" class="btn btn-primary">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    Tambah
                </a>
            </div>
        @endcan
        <div class="card-body border-bottom py-3">
            <div class="d-flex">
                <div class="text-secondary">
                    <div class="mx-2 d-inline-block">
                        <select name="sort" id="sort" class="form-control">
                            @php
                                $opts = [
                                    10,25,50,100
                                ];
                            @endphp 
                            @foreach ($opts as $opt)
                                <option value="{{ $opt }}" {{ request('sort') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="ms-auto text-secondary">
                    <form>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" name="search" placeholder="Search for…">
                            <button class="btn" type="submit">
                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-search"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="table-responsive-lg">
            <table class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th class="w-1">No</th>
                        <th>Nama Koordinat Absensi</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Radius Absen</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                   @forelse ($coordinat as $item)
                       <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->lat }}</td>
                            <td>{{ $item->lng }}</td>
                            <td>{{ $item->radius }} Meter</td>
                            @canany(['edit koordinat', 'hapus koordinat'])
                                <td>
                                    @can('edit koordinat')
                                        <a href="javascript:void(0)" onclick="return editModal('{{ $item->id }}')" class="btn btn-outline-warning btn-md">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                            Edit
                                        </a>
                                    @endcan
                                    @can('hapus koordinat')
                                        <a href="javascript:void(0)" onclick="return deleteItem('{{ $item->id }}')" class="btn btn-outline-danger btn-md">
                                            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                            Hapus
                                        </a>
                                    @endcan
                                </td>
                            @endcanany
                       </tr>
                   @empty
                       
                   @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">
                Showing <span>{{ $coordinat->firstItem() }}</span> 
                to <span>{{ $coordinat->lastItem() }}</span> of
                <span>{{ $coordinat->total() }}</span> entries
            </p>
            <ul class="pagination m-0 ms-auto">
                {{ $coordinat->links() }}
            </ul>
        </div>
    </div>
@endsection

@push('modal')
    <div class="modal modal-blur fade" id="modal-simple" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Koordinat Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" id="type">
                    <input type="hidden" name="id" id="id">
                    
                    <div class="alert alert-info" role="alert">
                        <strong>Petunjuk:</strong> Klik atau cari pada peta untuk menentukan lokasi absensi. Geser slider untuk mengatur radius area absensi.
                    </div>

                    <!-- Map Container -->
                    <div class="mb-3">
                        <label class="form-label">Cari Lokasi</label>
                        <input type="text" class="form-control" id="searchLocation" placeholder="Cari nama kota, jalan, atau tempat...">
                        <small class="text-muted">Contoh: Jakarta, Surabaya, atau nama jalan/gedung</small>
                    </div>
                    
                    <div id="map"></div>
                    
                    <!-- Form Input -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" placeholder="Contoh: Kantor Pusat">
                            <span class="invalid-feedback error_name"></span>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="radius" class="form-label">Radius Absensi (meter) <span class="text-danger">*</span></label>
                            <div class="radius-display-box">
                                <span id="radius-display">100</span> meter
                            </div>
                            <div class="range-slider">
                                <input type="range" id="radius" name="radius" min="10" max="1000" value="100" step="10">
                                <div class="range-value">
                                    <span>10m</span>
                                    <span>1000m</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Info Box Koordinat -->
                    <div class="info-box">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Latitude:</label>
                                <div class="value" id="lat-display">-</div>
                                <input type="hidden" id="latitude" name="latitude">
                            </div>
                            <div class="col-md-6">
                                <label>Longitude:</label>
                                <div class="value" id="lng-display">-</div>
                                <input type="hidden" id="longitude" name="longitude">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="storeBtn" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-geosearch/3.11.1/geosearch.umd.js"></script>
    <script>
        const BASE = "{{ route('koordinat.index') }}";

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

        let map;
        let marker;
        let circle;
        let defaultLat = -6.2088;
        let defaultLng = 106.8456;
        let searchControl;
        
        // Initialize modal dan map
        document.addEventListener('DOMContentLoaded', function() {
            const modalElement = document.getElementById('modal-simple');
            
            modalElement.addEventListener('shown.bs.modal', function () {
                if (!map) {
                    initMap();
                } else {
                    map.invalidateSize();
                }
            });
            
            // Event listener untuk radius range slider
            const radiusSlider = document.getElementById('radius');
            const radiusDisplay = document.getElementById('radius-display');
            
            radiusSlider.addEventListener('input', function() {
                const radiusValue = this.value;
                radiusDisplay.textContent = radiusValue;
                
                // Update circle radius pada map
                if (circle) {
                    circle.setRadius(parseInt(radiusValue));
                }
                
                // Update background gradient berdasarkan value
                const percentage = ((radiusValue - 10) / (1000 - 10)) * 100;
                this.style.background = `linear-gradient(to right, #206bc4 ${percentage}%, #d3d3d3 ${percentage}%)`;
            });
            
            // Set initial gradient
            const initialPercentage = ((radiusSlider.value - 10) / (1000 - 10)) * 100;
            radiusSlider.style.background = `linear-gradient(to right, #206bc4 ${initialPercentage}%, #d3d3d3 ${initialPercentage}%)`;
            
            // Event listener untuk search input
            document.getElementById('searchLocation').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchLocation(this.value);
                }
            });
            
            // Event listener untuk tombol simpan
            document.getElementById('storeBtn').addEventListener('click', saveLocation);
        });
        
        function initMap() {
            // Inisialisasi map dengan koordinat default (Jakarta)
            map = L.map('map').setView([defaultLat, defaultLng], 13);
            
            // Tambahkan tile layer dari OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Tambahkan kontrol geolokasi
            L.control.scale().addTo(map);
            
            // Coba dapatkan lokasi user saat ini
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        map.setView([userLat, userLng], 15);
                        addMarkerAndCircle(userLat, userLng);
                    },
                    function(error) {
                        console.log('Geolocation error:', error);
                        addMarkerAndCircle(defaultLat, defaultLng);
                    }
                );
            } else {
                addMarkerAndCircle(defaultLat, defaultLng);
            }
            
            // Event listener untuk klik pada map
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                addMarkerAndCircle(lat, lng);
            });
        }
        
        async function searchLocation(query) {
            if (!query) {
                alert('Masukkan nama lokasi yang ingin dicari');
                return;
            }
            
            try {
                // Menggunakan Nominatim API untuk geocoding
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`
                );
                const data = await response.json();
                
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lng = parseFloat(data[0].lon);
                    
                    // Zoom ke lokasi yang ditemukan
                    map.setView([lat, lng], 16);
                    
                    // Tambahkan marker dan circle
                    addMarkerAndCircle(lat, lng);
                    
                    // Update nama lokasi jika masih kosong
                    const namaLokasiInput = document.getElementById('nama_lokasi');
                    if (!namaLokasiInput.value) {
                        namaLokasiInput.value = data[0].display_name.split(',')[0];
                    }
                } else {
                    alert('Lokasi tidak ditemukan. Coba dengan kata kunci lain.');
                }
            } catch (error) {
                console.error('Error searching location:', error);
                alert('Terjadi kesalahan saat mencari lokasi.');
            }
        }
        
        function addMarkerAndCircle(lat, lng) {
            const radius = parseInt(document.getElementById('radius').value);
            
            // Hapus marker dan circle lama jika ada
            if (marker) {
                map.removeLayer(marker);
            }
            if (circle) {
                map.removeLayer(circle);
            }
            
            // Tambahkan marker baru
            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);
            
            // Tambahkan circle untuk radius
            circle = L.circle([lat, lng], {
                color: '#206bc4',
                fillColor: '#206bc4',
                fillOpacity: 0.2,
                radius: radius
            }).addTo(map);
            
            // Update display koordinat
            updateCoordinates(lat, lng);
            
            // Event listener untuk drag marker
            marker.on('dragend', function(e) {
                const newLat = e.target.getLatLng().lat;
                const newLng = e.target.getLatLng().lng;
                circle.setLatLng([newLat, newLng]);
                updateCoordinates(newLat, newLng);
            });
        }
        
        function updateCoordinates(lat, lng) {
            document.getElementById('lat-display').textContent = lat.toFixed(6);
            document.getElementById('lng-display').textContent = lng.toFixed(6);
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }

         $("#addBtn").click(function() {
            $(".modal-title").html("Tambah Lokasi Absensi");
            $("#type").val("create");
            $("#id").val("");
        });
        
        function saveLocation() {
            const type = document.getElementById('type').value;
            const id = document.getElementById('id').value;
            const namaLokasi = document.getElementById('name').value;
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            const radius = document.getElementById('radius').value;
            
            let url = "";
            let method = "POST";

            if (type === "create") {
                url = BASE + '/store';
                method = "POST";
            } else {
                url = BASE + '/' + id + '/update';   
                method = "PUT";                    
            }

            const data = {
                name: namaLokasi,
                lat: latitude,
                lng: longitude,
                radius: radius
            };

            $.ajax({
                url: url,
                method: method,
                data: data,
            }).done(function(response) {
                if (response.errors) {
                    $.each(response.errors, function(index, value) {
                        $("#" + index).addClass('is-invalid');
                        $(".error_" + index).html(value);

                        setTimeout(() => {
                            $("#" + index).removeClass('is-invalid');
                            $(".error_" + index).html('');
                        }, 3000);
                    });
                } else {
                    $("#modal-simple").modal('hide');
                    Toast.fire({
                        icon: response.status,
                        title: response.message
                    });
                    setTimeout(() => window.location.reload(), 3000);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log("Error:", textStatus, errorThrown);
            });
        }

        function editModal(id) {
            let url = BASE + `/${id}/show`
            $.ajax({
                url: url,
                method: "GET",
                dataType: "json"
            }).done(function(response){
                $(".modal-title").html("Edit Lokasi Absensi");
                let data = response.data;
                $("#modal-simple").modal('show')

                // ISI SEMUA FORM DENGAN DATA
                document.getElementById('name').value = data.name;
                document.getElementById('radius').value = data.radius;
                document.getElementById('radius-display').textContent = data.radius;
                document.getElementById('latitude').value = data.lat;
                document.getElementById('longitude').value = data.lng;
                
                // UPDATE DISPLAY KOORDINAT
                document.getElementById('lat-display').textContent = parseFloat(data.lat).toFixed(6);
                document.getElementById('lng-display').textContent = parseFloat(data.lng).toFixed(6);
                
                // UPDATE SLIDER DAN GRADIENT
                const radiusSlider = document.getElementById('radius');
                const percentage = ((data.radius - 10) / (1000 - 10)) * 100;
                radiusSlider.style.background = `linear-gradient(to right, #206bc4 ${percentage}%, #d3d3d3 ${percentage}%)`;
                
                // BUKA MODAL
                const modal = new bootstrap.Modal(document.getElementById('modal-simple'));
                modal.show();
                
                // TAMPILKAN LOKASI DI PETA
                setTimeout(() => {
                    if (map) {
                        map.setView([parseFloat(data.lat), parseFloat(data.lng)], 16);
                        addMarkerAndCircle(parseFloat(data.lat), parseFloat(data.lng));
                    }
                }, 500);

                $("#id").val(data.id);
                $("#type").val("update");
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log("Error:", textStatus, errorThrown);
            });
        }

        function deleteItem(id) {
            Swal.fire({
                title: "Peringatan !",
                text: "Anda yakin ingin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Hapus",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: BASE + '/' + id + '/destroy',
                        method: "DELETE",
                        dataType: "json",
                        success: function(response) {
                            Toast.fire({
                                icon: response.status,
                                title: response.message
                            });

                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        },
                        error: function(err) {
                            Toast.fire({
                                icon: "error",
                                title: "Server Error"
                            });
                        }
                    })
                }
            });
        }
    </script>
@endpush