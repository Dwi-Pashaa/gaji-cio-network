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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-phone"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" /></svg>
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
                            <th>Metode Pencairan</th>
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
                                    @if($item->admin_fee > 0)
                                        <div class="text-danger small" style="font-size: 0.72rem;">Potongan Admin: -Rp {{ number_format($item->admin_fee, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($item->payment_type === 'manual')
                                        <span class="badge bg-primary text-white">💵 Uang Tunai / Kas</span>
                                    @else
                                        <span class="badge bg-azure text-white">⚡ Transfer Bank (Xendit)</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->payment_type === 'manual')
                                        <span class="text-muted small">— Kas Tunai —</span>
                                    @elseif($item->bank_name)
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
                                                <button type="button" onclick="openApprovalModal('{{ $item->id }}')"
                                                   class="btn btn-action-custom btn-success shadow-sm"
                                                   title="{{ $item->status === 'failed' ? 'Coba Transfer Ulang' : 'Approve & Validasi' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                                    {{ $item->status === 'failed' ? 'Coba Lagi' : 'Approve' }}
                                                </button>
                                            @endcan
                                            @can('tolak kasbon')
                                                <button type="button" onclick="rejected('{{ $item->id }}')"
                                                   class="btn btn-action-custom btn-outline-danger"
                                                   title="Tolak Pengajuan Kasbon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                                                    Tolak
                                                </button>
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
                                <td colspan="10" class="text-center py-5">
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

    <!-- ================================================================= -->
    <!-- MODAL APPROVAL & VALIDASI KASBON                                  -->
    <!-- ================================================================= -->
    <div class="modal fade" id="modal-approve-kasbon" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold mb-0 text-white">Validasi & Approval Kasbon</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <input type="hidden" id="appr_id">
                    <input type="hidden" id="appr_payment_type">

                    <!-- Profil Karyawan & Pengajuan -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Karyawan:</span>
                            <span class="fw-bold text-dark fs-5" id="appr_emp_name">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Keperluan / Judul:</span>
                            <span class="fw-semibold text-dark" id="appr_title">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Metode Dipilih Karyawan:</span>
                            <span id="appr_method_badge">-</span>
                        </div>
                    </div>

                    <!-- Rincian Nominal -->
                    <div class="card border-primary-subtle border mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between py-1 small">
                                <span class="text-muted">Jumlah Kasbon Diajukan:</span>
                                <span class="fw-bold text-dark" id="appr_original_amount">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 small" id="appr_admin_row">
                                <span class="text-muted">• Biaya Admin Transfer (Xendit):</span>
                                <span class="text-danger fw-semibold" id="appr_admin_fee">-Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2 mt-2 border-top">
                                <span class="fw-bold text-dark">Total Dana Bersih:</span>
                                <span class="fw-bold fs-3 text-primary" id="appr_net_amount">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Info Rekening Bank (Jika Xendit) -->
                    <div id="appr_bank_container" class="mb-3" style="display: none;">
                        <label class="form-label fw-bold text-dark mb-1 small">Rekening Bank Penerima (Disbursement Xendit):</label>
                        <div class="p-2 bg-azure-lt border border-azure-subtle rounded small">
                            <div class="fw-bold text-dark" id="appr_bank_display">-</div>
                            <div class="text-muted font-monospace" id="appr_acc_num_display">-</div>
                            <div class="text-muted" id="appr_acc_holder_display">-</div>
                        </div>
                    </div>

                    <!-- Status Saldo Finance API -->
                    <div class="p-2 rounded border mb-3" id="appr_balance_box">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small" id="appr_balance_label">Sisa Saldo:</span>
                            <span class="fw-bold" id="appr_balance_value">Rp 0</span>
                        </div>
                        <div class="mt-1" id="appr_balance_status"></div>
                    </div>

                    <!-- Alert Insufficient -->
                    <div id="appr_alert_insufficient" class="alert alert-danger mb-0 py-2 small" style="display: none;">
                        <div class="d-flex align-items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v4" /><path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" /><path d="M12 16h.01" /></svg>
                            <span id="appr_alert_msg">Saldo tidak mencukupi untuk pembayaran ini.</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="btn-submit-approve" onclick="executeApproveKasbon()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                        Setujui & Transfer Sekarang
                    </button>
                </div>
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

    function formatRupiah(number) {
        return 'Rp ' + Math.round(number || 0).toLocaleString('id-ID');
    }

    let currentApprovalData = null;

    // Buka Modal Validasi Kasbon
    function openApprovalModal(id) {
        Swal.fire({
            title: 'Memeriksa Data & Saldo...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: BASE + '/' + id + '/calculate-transfer',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                Swal.close();
                if (!res.status) {
                    Toast.fire({ icon: "error", title: res.message || "Gagal memuat rincian kasbon." });
                    return;
                }

                currentApprovalData = res.data;
                let data = currentApprovalData;

                let isXendit = (data.payment_type === 'xendit');
                let channelActive = isXendit ? (data.channel_status?.xendit ?? data.channel_xendit_enabled ?? true) : (data.channel_status?.manual ?? data.channel_manual_enabled ?? true);
                let currentBal = isXendit ? (data.balance_xendit || 0) : (data.balance_manual || 0);
                let requiredBal = isXendit ? data.amount_xendit : data.amount_manual;
                let isBalanceEnough = currentBal >= requiredBal;

                $("#appr_id").val(data.cash_advance_id);
                $("#appr_payment_type").val(data.payment_type);
                $("#appr_emp_name").text(data.user_name);
                $("#appr_title").text(data.title);
                $("#appr_original_amount").text(formatRupiah(data.amount));

                if (isXendit) {
                    $("#appr_method_badge").html('<span class="badge bg-azure text-white">⚡ Transfer Bank (Xendit)</span>');
                    $("#appr_admin_row").show();
                    $("#appr_admin_fee").text('-' + formatRupiah(data.admin_fee));
                    $("#appr_net_amount").text(formatRupiah(data.amount_xendit));

                    $("#appr_bank_display").text((data.bank_name || '-').toUpperCase());
                    $("#appr_acc_num_display").text(data.account_number || '-');
                    $("#appr_acc_holder_display").text("a/n " + (data.account_holder_name || data.user_name));
                    $("#appr_bank_container").show();

                    $("#appr_balance_label").text("Sisa Saldo Xendit:");
                    $("#appr_balance_value").attr("class", "fw-bold text-azure").text(formatRupiah(currentBal));
                } else {
                    $("#appr_method_badge").html('<span class="badge bg-primary text-white">💵 Uang Tunai / Kas</span>');
                    $("#appr_admin_row").hide();
                    $("#appr_net_amount").text(formatRupiah(data.amount_manual));
                    $("#appr_bank_container").hide();

                    $("#appr_balance_label").text("Sisa Saldo Manual:");
                    $("#appr_balance_value").attr("class", "fw-bold text-primary").text(formatRupiah(currentBal));
                }

                // Status Saldo & Channel
                if (!channelActive) {
                    $("#appr_balance_status").html(`
                        <span class="badge bg-secondary-lt text-secondary">Saluran ${isXendit ? 'Xendit' : 'Manual'} Sedang Dinonaktifkan Admin</span>
                    `);
                    $("#appr_alert_msg").text(`Saluran pembayaran ${isXendit ? 'Saldo Xendit' : 'Saldo Manual'} sedang dinonaktifkan di Finance API.`);
                    $("#appr_alert_insufficient").show();
                    $("#btn-submit-approve").prop('disabled', true);
                } else if (isBalanceEnough) {
                    $("#appr_balance_status").html(`
                        <span class="badge bg-success-lt text-success d-inline-flex align-items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                            Saldo Mencukupi
                        </span>
                    `);
                    $("#appr_alert_insufficient").hide();
                    $("#btn-submit-approve").prop('disabled', false);
                } else {
                    $("#appr_balance_status").html(`
                        <span class="badge bg-danger-lt text-danger">Kurang ${formatRupiah(requiredBal - currentBal)}</span>
                    `);
                    $("#appr_alert_msg").text(`Saldo tidak mencukupi! Sisa: ${formatRupiah(currentBal)}, Dibutuhkan: ${formatRupiah(requiredBal)}.`);
                    $("#appr_alert_insufficient").show();
                    $("#btn-submit-approve").prop('disabled', true);
                }

                $("#modal-approve-kasbon").modal("show");
            },
            error: function(xhr) {
                Swal.close();
                let msg = "Gagal memeriksa data kasbon.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Toast.fire({ icon: "error", title: msg });
            }
        });
    }

    // Eksekusi Approval
    function executeApproveKasbon() {
        if (!currentApprovalData) return;
        let id = $("#appr_id").val() || currentApprovalData.cash_advance_id;
        let paymentType = $("#appr_payment_type").val() || currentApprovalData.payment_type || 'xendit';

        $("#btn-submit-approve").prop("disabled", true).html(`
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
            Memproses...
        `);

        $.ajax({
            url: BASE + '/' + id + '/approve',
            method: 'PUT',
            data: {
                _token: "{{ csrf_token() }}",
                transfer_type: paymentType
            },
            dataType: 'json',
            success: function(response) {
                $("#modal-approve-kasbon").modal("hide");

                Toast.fire({
                    icon: response.status || "success",
                    title: response.message
                });

                if (response.wa_link_user) {
                    const popupUser = window.open(
                        response.wa_link_user,
                        'waUser',
                        'width=600,height=800,top=100,left=100,toolbar=no,menubar=no,scrollbars=yes,resizable=yes'
                    );
                }

                setTimeout(() => {
                    window.location.reload();
                }, 1800);
            },
            error: function(xhr) {
                $("#btn-submit-approve").prop("disabled", false).html(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                    Setujui & Transfer Sekarang
                `);

                let msg = "Terjadi kesalahan saat memproses kasbon.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({ icon: "error", title: "Gagal", text: msg });
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
            confirmButtonText: "Iya, Tolak",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: BASE + '/' + id + '/rejected',
                    method: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: "json",
                    success: function(response) {
                        Toast.fire({
                            icon: response.status,
                            title: response.message
                        });

                        if (response.wa_link_user) {
                            window.open(
                                response.wa_link_user,
                                'waUser',
                                'width=600,height=800,top=100,left=100,toolbar=no,menubar=no,scrollbars=yes,resizable=yes'
                            );
                        }

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
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