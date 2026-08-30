@extends('layouts.app')

@section('title')
    Pengajuan Kasbon Karyawan
@endsection

@push('css')
<style>
    .emp-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        background: linear-gradient(135deg, rgba(26, 86, 219, 0.12) 0%, rgba(26, 86, 219, 0.22) 100%);
        color: #1a56db;
        border: 1px solid rgba(26, 86, 219, 0.2);
    }
    .btn-action-custom {
        padding: 0.35rem 0.6rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s ease;
    }
    .btn-action-custom:hover {
        transform: translateY(-1px);
    }
</style>
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
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Tampilkan:</span>
                        <select name="sort" id="sort" class="form-select form-select-sm" style="width: 80px;">
                            @php
                                $opts = [10, 25, 50, 100];
                            @endphp 
                            @foreach ($opts as $opt)
                                <option value="{{ $opt }}" {{ request('sort') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        <span class="text-muted small">entri</span>
                    </div>
                    <div class="ms-auto">
                        <form method="GET" action="{{ route('cash.advance.approval') }}">
                            <input type="hidden" name="sort" value="{{ request('sort', 10) }}">
                            <div class="input-group input-group-sm">
                                <input type="date" class="form-control" name="start" value="{{ request('start') }}" title="Dari Tanggal">
                                <span class="input-group-text">s/d</span>
                                <input type="date" class="form-control" name="end" value="{{ request('end') }}" title="Sampai Tanggal">
                                <button class="btn btn-primary" type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                    Filter
                                </button>
                                @if(request('start') || request('end'))
                                    <a href="{{ route('cash.advance.approval') }}" class="btn btn-outline-secondary">Reset</a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th class="w-1 text-center">No</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Karyawan</th>
                            <th>Keterangan</th>
                            <th>Jumlah Kasbon</th>
                            <th>Bank / Rekening</th>
                            <th>Status</th>
                            <th>Tanggal Diproses</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = $cashAdvance->firstItem() ?? 1; @endphp
                        @forelse ($cashAdvance as $item)
                            @php
                                $empUser = $item->user;
                                $empName = $empUser->name ?? 'User Tidak Ditemukan';
                                $empPhone = $empUser ? ($empUser->phone ?? ($empUser->email ?? '-')) : '-';
                                $empInitials = strtoupper(substr($empName, 0, 2));
                            @endphp
                            <tr>
                                <td class="text-center text-muted fw-medium">{{ $no++ }}</td>
                                <td>
                                    <div class="fw-medium text-dark">{{ \Carbon\Carbon::parse($item->request_date)->translatedFormat('d F Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($item->request_date)->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="emp-avatar">
                                            {{ $empInitials }}
                                        </span>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $empName }}</div>
                                            <div class="text-muted small">{{ $empPhone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->title }}</div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    @if($item->bank_name)
                                        <div class="fw-bold text-dark">{{ $item->bank_name }}</div>
                                        <div class="text-muted small font-monospace">{{ $item->account_number }}</div>
                                        <div class="text-muted small">a/n {{ $item->account_holder_name }}</div>
                                        @if($item->xendit_disbursement_id)
                                            <div class="text-success mt-1" style="font-size: 0.72rem;">ID: {{ $item->xendit_disbursement_id }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->statusBadgeColor() }} text-white">
                                        {{ $item->statusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->transfer_at)
                                        <div class="fw-medium text-dark">{{ \Carbon\Carbon::parse($item->transfer_at)->translatedFormat('d F Y H:i') }}</div>
                                    @elseif($item->approved_date && $item->status !== 'pending')
                                        <div class="fw-medium text-dark">{{ \Carbon\Carbon::parse($item->approved_date)->translatedFormat('d F Y') }}</div>
                                    @else
                                        <i class="text-muted small">Belum diproses</i>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap justify-content-center">
                                        @if(in_array($item->status, ['pending', 'failed']))
                                            @can('approve kasbon')
                                                <a href="javascript:void(0)" onclick="return approve('{{ $item->id }}')"
                                                   class="btn btn-action-custom btn-success shadow-sm"
                                                   title="{{ $item->status === 'failed' ? 'Coba Transfer Ulang' : 'Approve & Transfer' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                    {{ $item->status === 'failed' ? 'Coba Lagi' : 'Approve' }}
                                                </a>
                                            @endcan
                                            @can('tolak kasbon')
                                                <a href="javascript:void(0)" onclick="return rejected('{{ $item->id }}')"
                                                   class="btn btn-action-custom btn-outline-danger"
                                                   title="Tolak Pengajuan Kasbon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                                                    Tolak
                                                </a>
                                            @endcan
                                        @elseif(in_array($item->status, ['approved', 'transferred']))
                                            <a href="{{ route('cash.advance.invoice', $item->id) }}" target="_blank" class="btn btn-action-custom btn-outline-primary" title="Lihat Bukti Transfer">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 17h6" /><path d="M9 13h6" /></svg>
                                                Bukti Transfer
                                            </a>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2 text-muted"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /></svg>
                                        <div class="fw-semibold">Tidak Ada Pengajuan Kasbon</div>
                                        <div class="small">Saat ini belum ada data pengajuan kasbon yang tersedia.</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between py-2">
                <p class="m-0 text-muted small">
                    Menampilkan <strong>{{ $cashAdvance->firstItem() ?? 0 }}</strong> sampai <strong>{{ $cashAdvance->lastItem() ?? 0 }}</strong> dari total <strong>{{ $cashAdvance->total() }}</strong> entri
                </p>
                <ul class="pagination m-0 ms-auto">
                    {{ $cashAdvance->withQueryString()->links('pagination::bootstrap-5') }}
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
                    error: function(xhr) {
                        let msg = "Terjadi kesalahan";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Toast.fire({
                            icon: "error",
                            title: msg
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 2500);
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