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
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('cariListBarang') }}" method="POST">
                        @method('POST')
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4 ml-0">
                                <input type="text" name="daftar_barang_nama" class="form-control" autofocus placeholder="Nama Barang / Barcode" value="{{ $daftar_barang_nama }}">
                            </div>
                            <div class="col-md-3 ml-0">
                                <select name="detail_st" id="" class="form-control" placeholder="Status ditampilkan di website">
                                    <option value="">Pilih Status </option>
                                    <option value="yes" @selected($detail_st == 'yes')>YA</option>
                                    <option value="no" @selected($detail_st == 'no')>TIDAK</option>
                                </select>
                            </div>
                            <div class="col-md-4 ml-0">
                                <div class="d-flex item-center">
                                    <button type="submit" name="aksi" value="cari" class="btn btn-primary"><i class="fa fa-search"></i> Cari</button>
                                    <button type="submit" name="aksi" value="reset" class="btn btn-dark ml-2"><i class="fa fa-times"></i> Reset</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 10px">No</th>
                                    <th>Gambar</th>
                                    <th>Nama Barang</th>
                                    <th>Status</th>
                                    <th class="table-warning">Satuan Harga Beli</th>
                                    <th class="table-success">Satuan Harga Jual</th>
                                    <th class="table-info">Grosir Harga Jual</th>
                                    <th style="width: 8%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rs_barang as $key => $barang)
                                    <tr>
                                        <td class="text-center">{{ $rs_barang->firstItem() + $key }}</td>
                                        <td class="text-center">
                                            @if ($barang->detail_image_name != null)
                                                <img src="{{ asset('image/barang/' . $barang->detail_image_name) }}" height="150" width="150" class="img-fluid img-thumbnail" alt="">
                                            @else
                                                <img src="{{ asset('image/barang/default.jpg') }}" height="150" width="150" class="img-fluid img-thumbnail" alt="">
                                            @endif
                                        </td>
                                        <td>{{ $barang->barang_nama }} <br><u>{{ $barang->barang_barcode }}</u></td>
                                        <td class="text-center">
                                            @if ($barang->detail_st == 'yes')
                                                <span class="badge badge-success">Show</span>
                                            @elseif($barang->detail_st == 'no')
                                            <span class="badge badge-danger">Hide</span>
                                            @else
                                            <b>-</b>
                                            @endif
                                        </td>
                                        <td class="text-right table-warning">Rp.{{ number_format($barang->barang_harga_beli, 0, ',', '.') }}</td>
                                        <td class="text-right table-success">Rp.{{ number_format($barang->barang_harga_jual, 0, ',', '.') }}</td>
                                        <td class="text-right table-info">Rp.{{ number_format($barang->barang_grosir_harga_jual, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('updateListBarang', [$barang->slug]) }}" title="Ubah Gambar" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a>
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
