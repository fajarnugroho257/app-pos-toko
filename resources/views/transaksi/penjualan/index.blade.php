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
                </div>
                <div class="card-body">
                    {{-- <form action="{{ route('cariMasterBarang') }}" method="POST">
                        @method('POST')
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4 ml-0">
                                <input type="text" name="barang_nama" class="form-control" placeholder="Nama Barang">
                            </div>
                            <div class="col-md-4 ml-0">
                                <div class="d-flex item-center">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>
                                        Cari</button>
                                    <button type="submit" class="btn btn-dark ml-2"><i class="fa fa-times"></i>
                                        Reset</button>
                                </div>
                            </div>
                        </div>
                    </form> --}}
                    <h4 class="text-center mb-3"><b>Toko Cabang yang anda miliki</b></h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 10px">No</th>
                                <th>Nama Cabang</th>
                                <th>Alamat Cabang</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rs_cabang as $key => $cabang)
                                <tr>
                                    <td class="text-center">{{ $rs_cabang->firstItem() + $key }}</td>
                                    <td>{{ $cabang->cabang_nama }}</td>
                                    <td>{{ $cabang->cabang_alamat }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('transaksiCabang', [$cabang->slug]) }}" title="Lihat Transaksi"
                                            class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                        {{ $rs_cabang->links() }}
                    </ul>
                </div>
                <!-- /.card-footer-->
            </div>
            <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
@endsection
