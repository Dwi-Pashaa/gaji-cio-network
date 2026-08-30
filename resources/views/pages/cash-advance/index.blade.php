@extends('layouts.app')

@section('title')
    Pengajuan Kasbon
@endsection

@push('css')
    
@endpush

@section('content')
    <div class="mt-3">
        <div class="card">
            @can('ajukan kasbon')
                <div class="card-header">
                    <a href="javascript:void(0)" id="addBtn" data-bs-toggle="modal" data-bs-target="#modal-simple" class="btn btn-primary">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                        Ajukan Kasbon
                    </a>
                </div>
            @endcan
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
                        <form method="GET" action="{{ route('cash.advance.index') }}">
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
                                    <a href="{{ route('cash.advance.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                            <th>Keterangan</th>
                            <th>Jumlah Kasbon</th>
                            <th>Status</th>
                            <th>Tanggal Diproses</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = $cashAdvance->firstItem() ?? 1;
                        @endphp
                        @forelse ($cashAdvance as $item)
                            <tr>
                                <td class="text-center text-muted fw-medium">{{ $no++ }}</td>
                                <td>
                                    <div class="fw-medium text-dark">{{ \Carbon\Carbon::parse($item->request_date)->translatedFormat('d F Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($item->request_date)->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->title }}</div>
                                    @if($item->bank_name)
                                        <small class="text-muted">{{ $item->bank_name }} - {{ $item->account_number }} (a/n {{ $item->account_holder_name }})</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->statusBadgeColor() }} text-white">
                                        {{ $item->statusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->transfer_at)
                                        <div class="fw-medium text-dark">{{ \Carbon\Carbon::parse($item->transfer_at)->translatedFormat('d F Y H:i') }}</div>
                                    @elseif ($item->approved_date && $item->status !== 'pending')
                                        <div class="fw-medium text-dark">{{ \Carbon\Carbon::parse($item->approved_date)->translatedFormat('d F Y') }}</div>
                                    @else
                                        <i class="text-muted small">Belum Divalidasi</i>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap justify-content-center">
                                        @if ($item->status === "pending")
                                            @can('edit kasbon')
                                                <a href="javascript:void(0)" onclick="return editModal('{{ $item->id }}')" class="btn btn-outline-warning btn-sm" title="Edit Pengajuan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                                    Edit
                                                </a>
                                            @endcan
                                            @can('hapus kasbon')
                                                <a href="javascript:void(0)" onclick="return deleteItem('{{ $item->id }}')" class="btn btn-outline-danger btn-sm" title="Hapus Pengajuan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                                    Hapus
                                                </a>
                                            @endcan
                                        @elseif (in_array($item->status, ['approved', 'transferred']))
                                            <a href="{{ route('cash.advance.invoice', $item->id) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Lihat Bukti Transfer">
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
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-2 text-muted"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /></svg>
                                        <div class="fw-semibold">Belum Ada Riwayat Kasbon</div>
                                        <div class="small">Klik tombol "Ajukan Kasbon" di atas untuk mengajukan kasbon baru.</div>
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

@push('modal')
    <div class="modal modal-blur fade" id="modal-simple" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajukan Kasbon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" id="type">
                    <input type="hidden" name="id" id="id">

                    {{-- Tanggal Pengajuan --}}
                    <div class="form-group mb-3">
                        <label class="mb-2">Tanggal Pengajuan</label>
                        <input type="date" disabled value="{{ date('Y-m-d') }}" class="form-control">
                    </div>

                    {{-- Keterangan --}}
                    <div class="form-group mb-3">
                        <label for="title" class="mb-2">Keterangan / Keperluan</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Misal: Bayar cicilan, kebutuhan mendesak...">
                        <span class="invalid-feedback error_title text-danger" style="display:none;"></span>
                    </div>

                    {{-- Jumlah Kasbon --}}
                    <div class="form-group mb-3">
                        <label for="amount" class="mb-2">Jumlah Kasbon (Rp)</label>
                        <input type="text" name="amount" id="amount" class="form-control" placeholder="Misal: 1000000">
                        <span class="invalid-feedback error_amount text-danger" style="display:none;"></span>
                    </div>

                    <hr class="my-3">
                    <p class="text-muted small mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9h.01" /><path d="M11 12h1v4h1" /><circle cx="12" cy="12" r="9" /></svg>
                        Dana kasbon akan ditransfer otomatis ke rekening di bawah saat disetujui.
                    </p>

                    {{-- Pilihan Bank --}}
                    <div class="form-group mb-3">
                        <label for="bank_name" class="mb-2">Nama Bank</label>
                        <select name="bank_name" id="bank_name" class="form-control">
                            <option value="">-- Pilih Bank --</option>
                            @foreach ($banks as $code => $label)
                                <option value="{{ $code }}" {{ ($user->bank_name === $code) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="invalid-feedback error_bank_name text-danger" style="display:none;"></span>
                    </div>

                    {{-- Nomor Rekening --}}
                    <div class="form-group mb-3">
                        <label for="account_number" class="mb-2">Nomor Rekening</label>
                        <input type="text" name="account_number" id="account_number" class="form-control"
                               placeholder="Contoh: 1234567890"
                               value="{{ $user->account_number ?? '' }}">
                        <span class="invalid-feedback error_account_number text-danger" style="display:none;"></span>
                    </div>

                    {{-- Nama Pemilik Rekening --}}
                    <div class="form-group mb-3">
                        <label for="account_holder_name" class="mb-2">Nama Pemilik Rekening</label>
                        <input type="text" name="account_holder_name" id="account_holder_name" class="form-control"
                               placeholder="Sesuai buku tabungan"
                               value="{{ $user->account_holder_name ?? $user->name ?? '' }}">
                        <span class="invalid-feedback error_account_holder_name text-danger" style="display:none;"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="storeBtn" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                        Ajukan Kasbon
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('js')
<script>
    const BASE = "{{ route('cash.advance.index') }}";

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

    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById("amount");

        input.addEventListener("input", function(e) {
            let value = this.value.replace(/\D/g, "");
            
            if (value) {
                this.value = new Intl.NumberFormat("id-ID").format(value);
            } else {
                this.value = "";
            }
        });
    });

    $("#addBtn").click(function() {
        $(".modal-title").html("Ajukan Permohonan Kasbon");
        $("#type").val("create");
        $("#id").val("");
        $("#title").val("");
        $("#amount").val("");
        // Bank fields di-pre-fill dari data profil (sudah di-set via Blade value attribute)
    });

    $("#storeBtn").click(function() {
        let id                  = $("#id").val();
        let type                = $("#type").val();
        let title               = $("#title").val();
        let amount              = $("#amount").val();
        let bank_name           = $("#bank_name").val();
        let account_number      = $("#account_number").val();
        let account_holder_name = $("#account_holder_name").val();

        let url, method;
        if (type === 'create') {
            url    = BASE + '/store';
            method = "POST";
        } else {
            url    = BASE + `/${id}/update`;
            method = "PUT";
        }

        $.ajax({
            url: url,
            method: method,
            data: {
                title,
                amount,
                bank_name,
                account_number,
                account_holder_name,
            },
        }).done(function(response) {
            if (response.errors) {
                $.each(response.errors, function(index, value) {
                    $("#" + index).addClass('is-invalid');
                    $(".error_" + index).text(value[0] || value).show();

                    setTimeout(() => {
                        $("#" + index).removeClass('is-invalid');
                        $(".error_" + index).text('').hide();
                    }, 4000);
                });
            } else {
                $("#modal-simple").modal('hide');
                Toast.fire({ icon: response.status, title: response.message });

                if (response.wa_link) {
                    setTimeout(() => { window.location.href = response.wa_link; }, 2500);
                } else {
                    setTimeout(() => { window.location.reload(); }, 2500);
                }
            }
        }).fail(function() {
            Toast.fire({ icon: 'error', title: 'Terjadi kesalahan. Silakan coba lagi.' });
        });
    });

    function editModal(id) {
        let url = BASE + `/${id}/show`;
        $.ajax({
            url: url,
            method: "GET",
            dataType: "json"
        }).done(function(response) {
            let data = response.data;
            $(".modal-title").html("Edit Pengajuan Kasbon");
            $("#type").val("update");
            $("#modal-simple").modal('show');

            $("#id").val(data.id);
            $("#title").val(data.title);
            $("#bank_name").val(data.bank_name);
            $("#account_number").val(data.account_number);
            $("#account_holder_name").val(data.account_holder_name);

            let formatedAmount = new Intl.NumberFormat('id-ID').format(data.amount);
            $("#amount").val(formatedAmount);
        }).fail(function() {
            Toast.fire({ icon: 'error', title: 'Gagal memuat data kasbon.' });
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