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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
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
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="mb-4 text-center">Daftar Log Barang Pusat</h4>
                    <form action="{{ route('cariLogBarangCabang') }}" method="POST">
                        @method('POST')
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4 ml-0">
                                <input type="text" name="barang_cabang_nama" class="form-control" autofocus
                                    value="{{ $barang_cabang_nama ?? '' }}" placeholder="Nama Barang / Barcode">
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
                    {{-- <p>Harga Jual Dicabang adalah harga jual </p> --}}
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 10px">No</th>
                                    <th>Nama Barang</th>
                                    <th>Barcode Barang</th>
                                    <th>Stok Minimal</th>
                                    <th>Stok Tersedia</th>
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
                                        <td class="text-center">{{ $barang->barang_master_stok }}</td>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('updateMasterBarang', [$barang->slug]) }}"
                                                class="btn btn-sm btn-info"><i class="fa fa-history"></i></a>
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
                        {{-- {{ $rs_brg_cabang->links() }} --}}
                    </ul>
                </div>
                <!-- /.card-footer-->
            </div>
            <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
@endsection
