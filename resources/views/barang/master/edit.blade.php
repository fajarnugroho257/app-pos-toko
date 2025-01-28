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

            <!-- Default box -->
            <div class="card">

                <div class="card-header ">
                    <h3 class="card-title">{{ $title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('masterBarang') }}" class="btn btn-block btn-success"><i
                                class="fa fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                </div>
                <form action="{{ route('processUpdateMasterBarang') }}" method="POST">
                    <input type="hidden" value="{{ $detail->id }}" name="id">
                    @method('POST')
                    @csrf
                    <div class="card-body">
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Barang</label>
                                    <input type="text" value="{{ old('barang_nama', $detail->barang_nama) }}"
                                        name="barang_nama" class="form-control" placeholder="Nama Barang">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Barcode Barang</label>
                                    <input type="text" value="{{ old('barang_barcode', $detail->barang_barcode) }}"
                                        name="barang_barcode" class="form-control" placeholder="Barcode Barang">
                                    <input type="hidden" value="{{ $detail->barang_barcode }}"
                                    name="old_barang_barcode" class="form-control" placeholder="Barcode Barang">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><span class="text-primary">Harga Beli</span></label>
                                    <input type="number"
                                        value="{{ old('barang_harga_beli', $detail->barang_harga_beli) }}"
                                        name="barang_harga_beli" class="form-control" placeholder="Harga Barang">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><span class="text-success">Harga Jual</span></label>
                                    <input type="number"
                                        value="{{ old('barang_harga_jual', $detail->barang_harga_jual) }}"
                                        name="barang_harga_jual" class="form-control" placeholder="Harga Barang">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><span class="text-success">Stok Minimal</span></label>
                                    <input type="number"
                                        value="{{ old('barang_stok_minimal', $detail->barang_stok_minimal) }}"
                                        name="barang_stok_minimal" class="form-control" placeholder="Stok Minimal">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
                <!-- /.card-footer-->
            </div>
            <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
@endsection
