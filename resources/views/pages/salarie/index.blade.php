@extends('layouts.app')

@section('title')
    Gaji Karyawan
@endsection

@push('css')
    
@endpush

@section('content')
    <div class="card">
        @can('tambah gaji karyawan')
            <div class="card-header">
                <a href="javascript:void(0)" id="addBtn" data-bs-toggle="modal" data-bs-target="#modal-simple" class="btn btn-primary">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    Buat Gaji
                </a>
            </div>
        @endcan
        <div class="card-body">
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
        <div class="table-responsive">
            <table class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                        $grandTotal = 0;
                    @endphp
                    @forelse ($salary as $item)
                        @php
                            $totalAllw = $item->user->allowance->sum('amount');
                            $totalGaji = $item->base_salary + $totalAllw;
                            $grandTotal += $totalGaji;
                        @endphp
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ optional($item->user)->name }}</td>
                            <td>{{ number_format($item->base_salary, 2) }}</td>
                            <td>
                                @forelse ($item->user->allowance as $alw)
                                    <span class="badge bg-primary text-white">
                                        {{ $alw->name }} - {{ number_format($alw->amount, 2) }}
                                    </span>
                                @empty
                                    <span class="badge bg-secondary text-white">
                                        Tidak Memiliki Tunjangan
                                    </span>
                                @endforelse
                            </td>
                            <td>
                                Rp. {{ number_format($totalGaji, 2) }}
                            </td>
                            <td>
                                @can('edit gaji karyawan')
                                    <a href="javascript:void(0)" onclick="return editModal('{{ $item->id }}')" class="btn btn-outline-warning btn-md">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-edit"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
                                        Edit
                                    </a>
                                @endcan
                                @can('hapus gaji karyawan')
                                    <a href="javascript:void(0)" onclick="return deleteItem('{{ $item->id }}')" class="btn btn-outline-danger btn-md">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-trash"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                                        Hapus
                                    </a>
                                @endcan
                            </td> 
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="6">
                                Tidak Ada Data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-center">Total</th>
                        <th colspan="3">Rp. {{ number_format($grandTotal, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">
                Showing <span>{{ $salary->firstItem() }}</span> 
                to <span>{{ $salary->lastItem() }}</span> of
                <span>{{ $salary->total() }}</span> entries
            </p>
            <ul class="pagination m-0 ms-auto">
                {{ $salary->links() }}
            </ul>
        </div>
    </div>
@endsection

@push('modal')
    <div class="modal modal-blur fade" id="modal-simple" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-1 modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Buat Gaji Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>
                <form id="form">
                    <div class="modal-body">
                        <input type="hidden" name="type" id="type">
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="" class="mb-2">Pilih Karyawan/Pegawai</label>
                                    <select name="user_id" id="user_id" class="form-control">
                                        <option value="">Pilih</option>
                                        @foreach ($user as $usr)
                                            <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error_user_id"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="" class="mb-2">Jumlah Gaji Pokok</label>
                                    <input type="text" name="base_salary" id="base_salary" class="form-control">
                                    <span class="text-danger error_base_salary"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="" class="mb-2">Tunjangan</label>
                                    <div id="tunjangan-wrapper">
                                        <div class="input-group mb-2 tunjangan-item">
                                            <select name="allowance_id[]" id="allowance_id" class="form-control">
                                                <option value="">Tidak Mempunyai Tunjangan</option>
                                                @foreach ($allowance as $alw)
                                                    <option value="{{ $alw->id }}">{{ $alw->name }} - Rp. {{ number_format($alw->amount) }}</option>
                                                @endforeach
                                            </select>
                                            <button type="button" id="add-tunjangan" class="btn btn-primary">Tambah</button>
                                        </div>
                                    </div>
                                    <span class="text-danger error_allowance_id"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="" class="mb-2">Tanggal Aktif Gaji</label>
                                    <input type="date" name="effective_date" id="effective_date" class="form-control">
                                    <span class="text-danger error_effective_date"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="form-group mb-3">
                                    <label for="" class="mb-2">Status Gaji</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="">Pilih</option>
                                        @php
                                            $status = ["active", "inactive"];
                                        @endphp
                                        @foreach ($status as $st)
                                            <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error_status"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('js')
    <script>
        const BASE = "{{ route('salary.index') }}";

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

        const allowanceOptions = `
            <option value="">Pilih</option>
            @foreach ($allowance as $alw)
                <option value="{{ $alw->id }}">
                    {{ $alw->name }} - Rp. {{ number_format($alw->amount) }}
                </option>
            @endforeach
        `;

        document.addEventListener("DOMContentLoaded", function() {
            const wrapper = document.getElementById("tunjangan-wrapper");

            wrapper.addEventListener("click", function(e) {
                if (e.target && e.target.id === "add-tunjangan") {
                    let div = document.createElement("div");
                    div.classList.add("input-group", "mb-2", "tunjangan-item");
                    div.innerHTML = `
                        <select name="allowance_id[]" class="form-control">
                            <option value="">Tidak Mempunyai Tunjangan</option>
                            ${allowanceOptions} 
                        </select>
                        <button type="button" class="btn btn-danger remove-tunjangan">Hapus</button>
                    `;

                    if (wrapper.contains(e.target)) {
                        wrapper.appendChild(div, e.target);
                    } else {
                        wrapper.appendChild(div);
                    }
                }

                if (e.target && e.target.classList.contains("remove-tunjangan")) {
                    e.target.closest(".tunjangan-item").remove();
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("base_salary");

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
            $(".modal-title").html("Buat Gaji Karyawan/Pegawai");
            $("#type").val("create");
            $("#id").val("");
            $("#user_id").val("");
            $("#base_salary").val("");
            $("#allowance_id").val("");
            $("#effective_date").val("");
            $("#status").val("");
        });

        $("#form").on("submit", function(e) {
            e.preventDefault();

            let id   = $("#id").val();
            let type = $("#type").val();
            let url, method;

            if (type === "create") {
                url = BASE + "/store";
                method = "POST";
            } else {
                url = BASE + `/${id}/update`;
                method = "POST"; 
            }

            let formData = new FormData(this);
            if (type !== "create") {
                formData.append("_method", "PUT");
            }

            $.ajax({
                url: url,
                method: method,
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(response) {
                if (response.errors) {
                    $.each(response.errors, function(index, value) {
                        let inputField = $("[name='" + index + "']");
                        inputField.addClass("is-invalid");
                        $(".error_" + index).html(value);

                        setTimeout(() => {
                            inputField.removeClass("is-invalid");
                            $(".error_" + index).html("");
                        }, 3000);
                    });
                } else {
                    $("#modal-simple").modal("hide");
                    Toast.fire({
                        icon: "success",
                        title: response.message
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log("Error:", textStatus, errorThrown);
            });
        });

        function editModal(id) {
            let url = BASE + `/${id}/show`;
            $.ajax({
                url: url,
                method: "GET",
                dataType: "json"
            }).done(function(response){
                $(".modal-title").html("Edit Gaji Pegawai");
                $("#type").val("update");
                let data = response.data;
                $("#modal-simple").modal('show');

                $("#id").val(data.id);
                $("#user_id").val(data.user.id);
                $("#effective_date").val(data.effective_date);
                $("#status").val(data.status);

                let formattedSalary = new Intl.NumberFormat('id-ID').format(data.base_salary);
                $("#base_salary").val(formattedSalary);

                let wrapper = document.getElementById("tunjangan-wrapper");
                wrapper.innerHTML = "";

                if (data.user.allowance && data.user.allowance.length > 0) {
                    data.user.allowance.forEach(alw => {
                        let div = document.createElement("div");
                        div.classList.add("input-group", "mb-2", "tunjangan-item");
                        div.innerHTML = `
                            <select name="allowance_id[]" class="form-control">
                                <option value="">Tidak Mempunyai Tunjangan</option>
                                ${allowanceOptions}
                            </select>
                            <button type="button" class="btn btn-danger remove-tunjangan">Hapus</button>
                        `;
                        div.querySelector("select").value = alw.id;
                        wrapper.appendChild(div);
                    });
                    let addBtn = document.createElement("button");
                    addBtn.type = "button";
                    addBtn.id = "add-tunjangan";
                    addBtn.classList.add("btn", "btn-primary", "mt-2");
                    addBtn.textContent = "Tambah";
                    wrapper.appendChild(addBtn);

                } else {
                    let div = document.createElement("div");
                    div.classList.add("input-group", "mb-2", "tunjangan-item");
                    div.innerHTML = `
                        <select name="allowance_id[]" class="form-control">
                            <option value="">Tidak Mempunyai Tunjangan</option>
                            ${allowanceOptions}
                        </select>
                        <button type="button" id="add-tunjangan" class="btn btn-primary mb-2">Tambah</button>
                    `;
                    wrapper.appendChild(div);
                }
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
                                icon: 'success',
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