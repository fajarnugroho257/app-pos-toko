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
                        <a href="{{ route('tambahMasterBarang') }}" class="btn btn-block btn-success"><i
                                class="fa fa-plus"></i>
                            Tambah</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('cariMasterBarang') }}" method="POST">
                        @method('POST')
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4 ml-0">
                                <input type="text" name="barang_nama" class="form-control" autofocus
                                    placeholder="Nama Barang / Barcode" value="{{ $barang_nama }}">
                            </div>
                            <div class="col-md-4 ml-0">
                                <div class="d-flex item-center">
                                    <button type="submit" name="aksi" value="cari" class="btn btn-primary"><i
                                            class="fa fa-search"></i>
                                        Cari</button>
                                    <button type="submit" name="aksi" value="reset" class="btn btn-dark ml-2"><i
                                            class="fa fa-times"></i>
                                        Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 10px">No</th>
                                    <th>Nama Barang</th>
                                    <th>Barcode Barang</th>
                                    <th class="table-warning">Satuan Harga Beli</th>
                                    <th>Stok Minimal</th>
                                    <th>Stok Tersedia</th>
                                    <th class="table-success">Satuan Harga Jual</th>
                                    <th>Minimal Pembelian Grosir</th>
                                    <th class="table-info">Grosir Harga Jual</th>
                                    <th style="width: 12%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rs_barang as $key => $barang)
                                @php
                                $stStok = '';
                                    if ($barang->barang_master_stok < $barang->barang_stok_minimal) {
                                        $stStok = 'table-danger';
                                    }
                                @endphp
                                    <tr class="{{$stStok}}">
                                        <td class="text-center">{{ $rs_barang->firstItem() + $key }}</td>
                                        <td>{{ $barang->barang_nama }}</td>
                                        <td class="text-center">{{ $barang->barang_barcode }}</td>
                                        <td class="text-right table-warning">Rp.{{ number_format($barang->barang_harga_beli, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $barang->barang_stok_minimal }}</td>
                                        <td class="text-center">{{ $barang->barang_master_stok }}</td>
                                        <td class="text-right table-success">Rp.{{ number_format($barang->barang_harga_jual, 0, ',', '.') }}</td>
                                        <td class="text-right">{{$barang->barang_grosir_pembelian}}</td>
                                        <td class="text-right table-info">Rp.{{ number_format($barang->barang_grosir_harga_jual, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('updateMasterBarang', [$barang->slug]) }}" title="Edit / Menambah Stok"
                                                class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a>
                                            <a href="{{ route('historyMasterBarang', [$barang->id]) }}" title="History Barang Pusat"
                                                class="btn btn-sm btn-info"><i class="fa fa-history"></i></a>
                                            {{-- <a href="{{ route('processDeleteMasterBarang', [$barang->slug]) }}" title="Hapus"
                                                onclick="return confirm('Apakah anda yakin akan menghapus data ini ?')"
                                                class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                        {{ $rs_barang->links() }}
                    </ul>
                </div>
                <!-- /.card-footer-->
            </div>
            <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
@endsection
