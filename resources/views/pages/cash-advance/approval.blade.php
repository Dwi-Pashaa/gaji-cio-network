@extends('layouts.app')

@section('title')
    Pengajuan Kasbon Karyawan
@endsection

@push('css')
    
@endpush

@section('content')
    @role("Admin")
        @include('components.alert.success')
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <b>Setting Nomor Wa Pengajuan Kasbon</b>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('salary.updatePhone') }}" method="POST" id="form-phone">
                            @csrf
                            <div class="input-group mb-2">
                                <input type="telp" class="form-control @error('phone') is-invalid @enderror" value="{{ $phone->telp ?? '' }}" name="phone">
                                <button class="btn btn-outline-primary" type="submit">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-phone"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endrole
    <div class="mt-3">
        <div class="card">
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
                                <input type="date" class="form-control" name="start">
                                <input type="date" class="form-control" name="end">
                                <button class="btn" type="submit">
                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-search"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Karyawan</th>
                            <th>Keterangan</th>
                            <th>Jumlah Kasbon</th>
                            <th>Status</th>
                            <th>Tanggal Di Terima</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            $statusColors = [
                                'approved' => 'primary',
                                'pending'  => 'warning',
                                'rejected' => 'danger',
                            ];
                        @endphp
                        @forelse ($cashAdvance as $item)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->request_date)->translatedFormat('d F Y') }}
                                </td>
                                <td>
                                    {{ optional($item)->user->name }}
                                </td>
                                <td>
                                    {{ $item->title }}
                                </td>
                                <td>
                                    Rp. {{ number_format($item->amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$item->status] ?? 'secondary' }} text-white">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->status === "approved" || $item->status === "rejected")
                                        {{ \Carbon\Carbon::parse($item->approved_date)->translatedFormat('d F Y') }}
                                    @else
                                        <i>Belum Di Validasi</i>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status === "pending")
                                        @can('approve kasbon')
                                            <a href="javascript:void(0)" onclick="return approve('{{ $item->id }}')" class="btn btn-outline-primary btn-md">
                                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-copy-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path stroke="none" d="M0 0h24v24H0z" /><path d="M7 9.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2 2 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /><path d="M11 14l2 2l4 -4" /></svg>
                                                Terima
                                            </a>
                                        @endcan
                                        @can('tolak kasbon')
                                            <a href="javascript:void(0)" onclick="return rejected('{{ $item->id }}')" class="btn btn-outline-danger btn-md">
                                                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 21h-7a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v6.5" /><path d="M16 3v4" /><path d="M8 3v4" /><path d="M4 11h16" /><path d="M22 22l-5 -5" /><path d="M17 22l5 -5" /></svg>
                                                Tolak
                                            </a>
                                        @endcan
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak Ada Data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center">
                <p class="m-0 text-secondary">
                    Showing <span>{{ $cashAdvance->firstItem() }}</span> 
                    to <span>{{ $cashAdvance->lastItem() }}</span> of
                    <span>{{ $cashAdvance->total() }}</span> entries
                </p>
                <ul class="pagination m-0 ms-auto">
                    {{ $cashAdvance->links() }}
                </ul>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    const BASE = "{{ route('cash.advance.approval') }}";

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

    function approve(id) {
        Swal.fire({
            title: "Peringatan !",
            text: "Anda yakin ingin menerima pengajuan kasbon ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Iya",
            cancelButtonText: "Tidak"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE + '/' + id + '/approve',
                    method: "PUT",
                    dataType: "json",
                    success: function(response) {
                        console.log(response);

                        Toast.fire({
                            icon: response.status,
                            title: response.message
                        });

                        let opened = 0; 

                        if (response.wa_link_user) {
                            const popupUser = window.open(
                                response.wa_link_user,
                                'waUser',
                                'width=600,height=800,top=100,left=100,toolbar=no,menubar=no,scrollbars=yes,resizable=yes'
                            );
                            if (popupUser) opened++;
                        }

                        if (response.wa_link_default) {
                            setTimeout(() => {
                                const popupAdmin = window.open(
                                    response.wa_link_default,
                                    'waAdmin',
                                    'width=600,height=800,top=150,left=750,toolbar=no,menubar=no,scrollbars=yes,resizable=yes'
                                );
                                if (popupAdmin) opened++;

                                if (opened >= 1) {
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 2000);
                                }
                            }, 1500); 
                        } else {
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    },
                    error: function() {
                        Toast.fire({
                            icon: "error",
                            title: "Server Error"
                        });
                    }
                });
            }
        });
    }

    function rejected(id) {
        Swal.fire({
            title: "Peringatan !",
            text: "Anda yakin ingin menolak pengajuan kasbon ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Iya",
            cancelButtonText: "Tidak"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE + '/' + id + '/rejected',
                    method: "PUT",
                    dataType: "json",
                    success: function(response) {
                        Toast.fire({
                            icon: response.status,
                            title: response.message
                        });

                        let opened = 0; 

                        if (response.wa_link_user) {
                            const popupUser = window.open(
                                response.wa_link_user,
                                'waUser',
                                'width=600,height=800,top=100,left=100,toolbar=no,menubar=no,scrollbars=yes,resizable=yes'
                            );
                            if (popupUser) opened++;
                        }

                        if (response.wa_link_default) {
                            setTimeout(() => {
                                const popupAdmin = window.open(
                                    response.wa_link_default,
                                    'waAdmin',
                                    'width=600,height=800,top=150,left=750,toolbar=no,menubar=no,scrollbars=yes,resizable=yes'
                                );
                                if (popupAdmin) opened++;

                                if (opened >= 1) {
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 2000);
                                }
                            }, 1500); 
                        } else {
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        }
                    },
                    error: function() {
                        Toast.fire({
                            icon: "error",
                            title: "Server Error"
                        });
                    }
                });
            }
        });
    }

    $("#form-phone").submit(function() {
        this.submit();
    });
</script>
@endpush