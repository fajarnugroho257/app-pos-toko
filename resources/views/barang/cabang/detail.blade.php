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
                        <a href="{{ route('barangCabang') }}" class="btn btn-block btn-success"><i
                                class="fa fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="mb-4 text-center">Daftar Barang Untuk Cabang <b
                            class="text-danger">{{ $cabang->cabang_nama }}</b></h4>
                    <form action="{{ route('cariBarangCabang') }}" method="POST">
                        <input type="hidden" name="id" value="{{ $cabang->id }}">
                        @method('POST')
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4 ml-0">
                                <input type="text" name="barang_cabang_nama" class="form-control" autofocus
                                    placeholder="Nama Barang / Barcode" value="{{ $barang_cabang_nama }}">
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
                    <a href="javascript:;" id="getProduk" class="btn btn-primary mb-3"><i class="fa fa-download"></i> Produk
                        baru</a>
                    {{-- <p>Harga Jual Dicabang adalah harga jual </p> --}}
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th style="width: 10px">No</th>
                                <th>Nama Barang</th>
                                <th>Harga Jual Dicabang</th>
                                <th>Stok Minimal</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th style="width: 10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rs_barang as $key => $barang)
                                <tr @if ($barang->barang_stok < $barang->barang_master->barang_stok_minimal) class="table-danger" @endif>
                                    <td class="text-center">{{ $rs_barang->firstItem() + $key }}</td>
                                    <td>{{ $barang->barang_master->barang_nama }}</td>
                                    <td class="text-center">Rp.
                                        {{ number_format($barang->cabang_barang_harga, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $barang->barang_master->barang_stok_minimal }}</td>
                                    <td class="text-center">{{ $barang->barang_stok }}</td>
                                    <td class="text-center">
                                        @if ($barang->barang_st == 'yes')
                                            <span class="btn-sm btn-success">Digunakan</span>
                                        @else
                                            <span class="btn-sm btn-danger">Tidak Digunakan</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('updatebarangCabang', [$barang->id]) }}" title="Ubah barang"
                                            class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a>
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
    <div class="modal fade" id="modal-produk">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Data Produk terbaru</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('processAddBarangCabang') }}" method="POST">
                    <input type="hidden" name="cabang_id" value="{{ $cabang->id }}">
                    @method('POST')
                    @csrf
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nama Barang</th>
                                    <th class="text-center">Harga Beli</th>
                                    <th class="text-center">Harga Jual</th>
                                </tr>
                            </thead>
                            <tbody id="res-produk"></tbody>
                        </table>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script>
        $(document).ready(function() {
            $('#getProduk').on('click', function() {
                const slug = $(this).data('slug');
                $.ajax({
                    url: "{{ route('getDataProduk') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        cabang_id: {{ $cabang->id }},
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
