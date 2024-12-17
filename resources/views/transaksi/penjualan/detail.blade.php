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
                        <a href="{{ route('transaksi') }}" class="btn btn-block btn-success"><i
                                class="fa fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="text-center mb-4"><b>Transaksi <br /><span
                                class="text-danger">{{ $cabang->cabang_nama }}</span></b></h4>
                    <form action="{{ route('cariTransaksi') }}" method="POST" class="mb-4">
                        @method('POST')
                        @csrf
                        <input type="hidden" name="id" value="{{ $cabang->id }}">
                        <div class="row mb-2">
                            <div class="col-md-6 ml-0 d-flex item-center" style="gap: 10px;">
                                <input type="date" name="date_start" class="form-control" value="{{ $date_start }}">
                                <div class="m-0"><b>S/D</b></div>
                                <input type="date" name="date_end" class="form-control" value="{{ $date_end }}">
                            </div>
                            <div class="d-flex item-center">
                                <button type="submit" name="aksi" value="cari" class="btn btn-primary"><i
                                        class="fa fa-search"></i>
                                    Cari</button>
                                <button type="submit" name="aksi" value="reset" class="btn btn-dark ml-2"><i
                                        class="fa fa-times"></i>
                                    Reset</button>
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
                                    <th>Nama Pelanggan</th>
                                    <th>Grand Total</th>
                                    <th>Cash</th>
                                    <th>Kembalian</th>
                                    <th>Kasir</th>
                                    <th style="width: 10%">Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($rs_transaksi as $key => $transaksi)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td class="text-center">{{ $transaksi->cart_id }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($transaksi->trans_date)->translatedFormat('d F Y H:i') }}
                                        </td>
                                        <td>{{ $transaksi->trans_pelanggan }}</td>
                                        <td class="text-right text-danger text-bold">
                                            {{ 'Rp. ' . number_format($transaksi->trans_total, 0, ',', '.') }}</td>
                                        <td class="text-right text-info text-bold">
                                            {{ 'Rp. ' . number_format($transaksi->trans_bayar, 0, ',', '.') }}</td>
                                        <td class="text-right text-success text-bold">
                                            {{ 'Rp. ' . number_format($transaksi->trans_kembalian, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $transaksi->users->name }}</td>
                                        <td class="text-center">
                                            <a href="javascript:;" title="Lihat Nota" class="btn btn-sm btn-info show-nota"
                                                data-cart_id="{{ $transaksi->cart_id }}"><i
                                                    class="fa fa-sticky-note"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">

                    </div>
                    <!-- /.card-footer-->
                </div>
                <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
    <div class="modal fade" id="modal-produk">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Nota Penjualan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
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
                    url: "{{ route('show_nota') }}",
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
