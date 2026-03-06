@extends('template.base.base')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">{{ $title ?? '' }}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @session('success')
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endsession
            @session('error')
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endsession
            <!-- Default box -->
            <div class="card">
                <div class="card-header ">
                    <h3 class="card-title">{{ $title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('laporanHutang') }}" class="btn btn-block btn-success"><i class="fa fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="text-center mb-4"><b>Laporan Hutang <br /><span
                                class="text-danger">{{ $cabang->cabang_nama }}</span></b></h4>
                    <form action="{{ route('cariHutang') }}" method="POST" class="mb-4">
                        @method('POST')
                        @csrf
                        <input type="hidden" name="id" value="{{ $cabang->id }}">
                        <div class="row mb-2">
                            <div class="col-md-6 ml-0 d-flex item-center" style="gap: 10px;">
                                <input type="date" name="date_start" class="form-control" value="{{ $date_start }}">
                                <div class="m-0"><b>S/D</b></div>
                                <input type="date" name="date_end" class="form-control" value="{{ $date_end }}">
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex item-center">
                                        <button type="submit" name="aksi" value="cari" class="btn btn-primary"><i
                                                class="fa fa-search"></i>
                                            Cari</button>
                                        <button type="submit" name="aksi" value="reset" class="btn btn-dark ml-2"><i
                                                class="fa fa-times"></i>
                                            Reset</button>
                                    </div>
                                    {{-- <div>
                                        <a href="{{ route('downloadLabaRugi', ['slug' => $cabang->slug]) }}"
                                            class="btn btn-block btn-primary"><i class="fa fa-file-pdf"></i>
                                            Download</a>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 10px">No</th>
                                    <th>ID Keranjang</th>
                                    <th>Date Time</th>
                                    <th>Pelanggan</th>
                                    <th>Total Pembelian</th>
                                    <th>Uang Muka</th>
                                    <th>Kekurangan</th>
                                    <th>Catatan</th>
                                    <th style="width: 30px">Detail</th>
                                </tr>
                            </thead>
                            @php
                                $no = 1;
                                $ttlPembelian = 0;
                                $ttlMuka = 0;
                                $ttlKekurangan = 0;
                            @endphp
                            <tbody>
                                @foreach ($rs_laba as $key => $hutang)
                                <tr>
                                    <td class="text-center">{{ $no++ }}</td>
                                    <td>{{ $hutang->cart_id }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($hutang->trans_date)->translatedFormat('d F Y H:i') }}
                                    </td>
                                    <td>{{ $hutang->trans_pelanggan }}</td>
                                    <td class="text-right text-success text-bold">{{ 'Rp. ' . number_format($hutang->trans_total, 0, ',', '.') }}</td>
                                    <td class="text-right text-info text-bold">{{ 'Rp. ' . number_format($hutang->cart->cart_draft->draft_uang_muka, 0, ',', '.') }}</td>
                                    <td class="text-right text-danger text-bold">{{ 'Rp. ' . number_format($hutang->cart->cart_draft->draft_uang_sisa, 0, ',', '.') }}</td>
                                    <td>{{ $hutang->cart->cart_draft->draft_note}}</td>
                                    <td class="text-center">
                                        <a href="javascript:;" title="Lihat Nota" class="btn btn-sm btn-info show-nota"
                                            data-cart_id="{{ $hutang->cart_id }}"><i class="fa fa-sticky-note"></i></a>
                                    </td>
                                    @php
                                        $ttlPembelian += $hutang->trans_total;
                                        $ttlMuka += $hutang->cart->cart_draft->draft_uang_muka;
                                        $ttlKekurangan += $hutang->cart->cart_draft->draft_uang_sisa;
                                    @endphp
                                </tr>
                                @endforeach
                                <tr class="text-bold">
                                    <td colspan="4" class="text-right">Jumlah</td>
                                    <td class="text-success text-right">{{ 'Rp. ' . number_format($ttlPembelian, 0, ',', '.') }}</td>
                                    <td class="text-info text-right">{{ 'Rp. ' . number_format($ttlMuka, 0, ',', '.') }}</td>
                                    <td class="text-danger text-right">{{ 'Rp. ' . number_format($ttlKekurangan, 0, ',', '.') }}</td>
                                    <td colspan="2" class="text-center">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
    <div class="modal fade" id="modal-produk">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail Hutang</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <h5 class="text-center">Pembeli</h5>
                            <tbody id="res-pembeli"></tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <h5 class="text-center">Pembelian</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nama Barang</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">SubTotal</th>
                                </tr>
                            </thead>
                            <tbody id="res-produk"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button class="btn btn-default" data-dismiss="modal">Close</button>
                    {{-- <button type="submit" class="btn btn-primary">Simpan Produk</button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script>
        $(document).ready(function() {
            $('.show-nota').on('click', function() {
                const cart_id = $(this).data('cart_id');
                // alert(cart_id);
                $.ajax({
                    url: "{{ route('show_hutang') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        cart_id: cart_id,
                    },
                    success: function(response) {
                        if (response.success) {
                            // Tindakan jika permintaan berhasil
                            $('#modal-produk').modal('show');
                            $('#res-produk').html(response.html);
                            $('#res-pembeli').html(response.htmlPembeli);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Tindakan jika permintaan gagal
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                        console.log(error);
                    }
                });
            });
        });
    </script>
@endsection
