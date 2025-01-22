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
                        <a href="{{ route('labaRugi') }}" class="btn btn-block btn-success"><i class="fa fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="text-center mb-4"><b>Laba / Rugi <br /><span
                                class="text-danger">{{ $cabang->cabang_nama }}</span></b></h4>
                    <form action="{{ route('cariLaba') }}" method="POST" class="mb-4">
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
                                    <th>Pelanggan</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th>Laba</th>
                                    <th style="width: 30px">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                    $ttlBeli = 0;
                                    $ttlJual = 0;
                                    $ttlQrt = 0;
                                    $grandTtl = 0;
                                    $grandTtlCartBeli = 0;
                                    $grandTtlCartJual = 0;
                                    $grandTtlLaba = 0;
                                @endphp
                                @foreach ($rs_laba as $key => $laba)
                                    @php
                                        $ttlBeli += $laba->cart_harga_beli * $laba->cart_qty;
                                        $ttlJual += $laba->cart_harga_jual * $laba->cart_qty;
                                        $ttlQrt += $laba->cart_qty;
                                        $grandTtl += $laba->cart_subtotal;
                                        $jlhCartData = count($laba->cart_data);
                                        //
                                        $ttlCartBeli = 0;
                                        $ttlCartJual = 0;
                                    @endphp
                                    @foreach ($laba->cart_data as $item)
                                        @php
                                            $ttlCartBeli += $item->cart_harga_beli * $item->cart_qty;
                                            $ttlCartJual += $item->cart_harga_jual * $item->cart_qty;
                                            // laba
                                            $ttlLaba = $ttlCartJual - $ttlCartBeli;
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td class="text-center">{{ $laba->cart_id }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($laba->trans_date)->translatedFormat('d F Y H:i') }}
                                        </td>
                                        <td>{{ $laba->trans_pelanggan }}</td>
                                        <td class="text-right text-danger text-bold">
                                            {{ 'Rp. ' . number_format($ttlCartBeli, 0, ',', '.') }}</td>
                                        <td class="text-right text-info text-bold">
                                            {{ 'Rp. ' . number_format($ttlCartJual, 0, ',', '.') }}</td>
                                        <td class="text-right text-success text-bold">
                                            {{ 'Rp. ' . number_format($ttlLaba, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="javascript:;" title="Lihat Nota" class="btn btn-sm btn-info show-nota"
                                                data-cart_id="{{ $laba->cart_id }}"><i
                                                    class="fa fa-sticky-note"></i></a>
                                        </td>
                                        @php
                                            $grandTtlCartBeli += $ttlCartBeli;
                                            $grandTtlCartJual += $ttlCartJual;
                                            $grandTtlLaba += $ttlLaba;
                                        @endphp
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="text-right" colspan="4"></td>
                                    <td class="text-right text-danger text-bold">
                                        {{ 'Rp. ' . number_format($grandTtlCartBeli, 0, ',', '.') }}</td>
                                    <td class="text-right text-info text-bold">
                                        {{ 'Rp. ' . number_format($grandTtlCartJual, 0, ',', '.') }}</td>
                                    <td class="text-right text-success text-bold">
                                        {{ 'Rp. ' . number_format($grandTtlLaba, 0, ',', '.') }}</td>
                                </tr>
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
                                <th class="text-center">Harga Beli</th>
                                <th class="text-center">Harga Jual</th>
                                <th class="text-center">Untung</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Total Laba</th>
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
                    url: "{{ route('detailLabaRugi') }}",
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
