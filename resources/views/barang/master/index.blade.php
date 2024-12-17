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
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 10px">No</th>
                                <th>Nama Barang</th>
                                <th>Barcode Barang</th>
                                <th>Stok Minimal</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rs_barang as $key => $barang)
                                <tr>
                                    <td class="text-center">{{ $rs_barang->firstItem() + $key }}</td>
                                    <td>{{ $barang->barang_nama }}</td>
                                    <td class="text-center">{{ $barang->barang_barcode }}</td>
                                    <td class="text-center">{{ $barang->barang_stok_minimal }}</td>
                                    <td class="text-right">Rp.{{ number_format($barang->barang_harga_beli, 0, ',', '.') }}
                                    <td class="text-right">Rp.{{ number_format($barang->barang_harga_jual, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('updateMasterBarang', [$barang->slug]) }}"
                                            class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a>
                                        <a href="{{ route('processDeleteMasterBarang', [$barang->slug]) }}"
                                            onclick="return confirm('Apakah anda yakin akan menghapus data ini ?')"
                                            class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
