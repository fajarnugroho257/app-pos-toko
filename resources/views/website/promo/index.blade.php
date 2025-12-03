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
                        <a href="{{ route('addPromo') }}" class="btn btn-block btn-success"><i class="fa fa-plus"></i> Tambah</a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('cariPromo') }}" method="POST">
                        @method('POST')
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4 ml-0">
                                <input type="text" name="promo_barang_nama" class="form-control" autofocus placeholder="Nama Barang / Barcode" value="{{ $promo_barang_nama }}">
                            </div>
                            {{-- <div class="col-md-3 ml-0">
                                <select name="detail_st" id="" class="form-control" placeholder="Status ditampilkan di website">
                                    <option value="">Pilih Status </option>
                                    <option value="yes" @selected($detail_st == 'yes')>YA</option>
                                    <option value="no" @selected($detail_st == 'no')>TIDAK</option>
                                </select>
                            </div> --}}
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
                                    <th style="width: 8%">Gambar</th>
                                    <th style="width: 25%">Nama Barang</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th>Status</th>
                                    <th style="width: 8%" class="table-success">Harga Satuan</th>
                                    <th style="width: 8%" class="table-info">Gros Min Pembelian</th>
                                    <th style="width: 8%" class="table-info">Gros Harga Jual</th>
                                    <th style="width: 8%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rs_data as $key => $data)
                                    <tr>
                                        <td class="text-center">{{ $rs_data->firstItem() + $key }}</td>
                                        <td class="text-center">
                                            @if ($data->barang_master->detail_barang->detail_image_name != null)
                                                <img src="{{ asset('image/barang/' . $data->barang_master->detail_barang->detail_image_name) }}" height="150" width="150" class="img-fluid img-thumbnail" alt="">
                                            @else
                                                <img src="{{ asset('image/barang/default.jpg') }}" height="150" width="150" class="img-fluid img-thumbnail" alt="">
                                            @endif
                                        </td>
                                        <td>{{ $data->barang_master->barang_nama }} <br><u>{{ $data->barang_master->barang_barcode }}</u></td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($data->promo_start)->format('d M Y') }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($data->promo_end)->format('d M Y') }}</td>
                                        <td class="text-center">
                                            @if ($data->promo_st == 'yes')
                                                <span class="badge badge-success">Show</span>
                                            @elseif($data->promo_st == 'no')
                                            <span class="badge badge-danger">Hide</span>
                                            @else
                                            <b>-</b>
                                            @endif
                                        </td>
                                        <td class="text-right table-success">Rp.{{ number_format($data->promo_harga, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $data->promo_grosir_pembelian }}</td>
                                        <td class="text-right table-info">Rp.{{ number_format($data->promo_grosir_harga, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('editPromo', [$data->id]) }}" title="Ubah Data" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a>
                                            <a href="{{ route('deletePromo', [$data->id]) }}" title="Hapus Data" class="btn btn-sm btn-danger" onclick="return confirm('Apakah anda yakin akan menghapus data ini ?')"><i class="fa fa-trash"></i></a>
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
                        {{ $rs_data->links() }}
                    </ul>
                </div>
                <!-- /.card-footer-->
            </div>
            <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
@endsection
