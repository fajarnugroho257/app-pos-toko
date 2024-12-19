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
                                    <th>Pelanggan</th>
                                    <th>Barang</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                    $ttlBeli = 0;
                                    $ttlJual = 0;
                                    $ttlQrt = 0;
                                    $grandTtl = 0;
                                @endphp
                                @foreach ($rs_laba as $key => $laba)
                                    @php
                                        $ttlBeli += $laba->cart_harga_beli * $laba->cart_qty;
                                        $ttlJual += $laba->cart_harga_jual * $laba->cart_qty;
                                        $ttlQrt += $laba->cart_qty;
                                        $grandTtl += $laba->cart_subtotal;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td class="text-center">{{ $laba->cart_id }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($laba->trans_date)->translatedFormat('d F Y H:i') }}
                                        </td>
                                        <td>{{ $laba->trans_pelanggan }}</td>
                                        <td>{{ $laba->cart_nama }}</td>
                                        <td class="text-right text-danger text-bold">
                                            {{ 'Rp. ' . number_format($laba->cart_harga_beli, 0, ',', '.') }}</td>
                                        <td class="text-right text-info text-bold">
                                            {{ 'Rp. ' . number_format($laba->cart_harga_jual, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $laba->cart_qty }}</td>
                                        <td class="text-right text-success text-bold">
                                            {{ 'Rp. ' . number_format($laba->cart_subtotal, 0, ',', '.') }}</td>
                                        {{-- <td class="text-center">{{ $laba->users->name }}</td> --}}
                                        {{-- <td class="text-center">
                                            <a href="javascript:;" title="Lihat Nota" class="btn btn-sm btn-info show-nota"
                                                data-cart_id="{{ $laba->cart_id }}"><i class="fa fa-sticky-note"></i></a>
                                        </td> --}}
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="text-right" colspan="5"></td>
                                    <td class="text-right text-danger text-bold">
                                        {{ 'Rp. ' . number_format($ttlBeli, 0, ',', '.') }}</td>
                                    <td class="text-right text-info text-bold">
                                        {{ 'Rp. ' . number_format($ttlJual, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $ttlQrt }}</td>
                                    <td class="text-right text-success text-bold">
                                        {{ 'Rp. ' . number_format($grandTtl, 0, ',', '.') }}</td>
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
@endsection
