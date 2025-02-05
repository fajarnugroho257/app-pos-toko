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
                        <a href="{{ route('showLogBarangCabang', ['slug' => $cabang->slug]) }}"
                            class="btn btn-block btn-success"><i class="fa fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="mb-4 text-center">Detail Log Barang <span
                            class="text-primary"><b>{{ $barang->barang_master->barang_nama }}</b></span> <br />Cabang <b
                            class="text-danger">{{ $cabang->cabang_nama }}</b></h4>
                    {{-- <p>Harga Jual Dicabang adalah harga jual </p> --}}
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 10px">No</th>
                                    <th>Nama Barang</th>
                                    <th>Status</th>
                                    <th>Stok Awal</th>
                                    <th>Perubahan</th>
                                    <th>Stok Akhir</th>
                                    <th>Date Time</th>
                                    <th>Nama Petugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rs_barang_log as $key => $barang_log)
                                    <tr>
                                        <td class="text-center">{{ $rs_barang_log->firstItem() + $key }}</td>
                                        <td>{{ $barang_log->barang_cabang->barang_master->barang_nama }}</td>
                                        <td class="text-center">
                                            @if ($barang_log->barang_st == 'transaksi')
                                                <span class="btn btn-danger">Terjual</span>
                                            @else
                                                <span class="btn btn-success">Stok Masuk</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $barang_log->barang_awal }}</td>
                                        <td class="text-center">
                                            @if ($barang_log->barang_st == 'transaksi')
                                                {{ $barang_log->barang_transaksi }} <i class="text-danger fa fa-arrow-up"></i>
                                            @else
                                                {{ $barang_log->barang_perubahan }}
                                                <i class="text-success fa fa-arrow-down"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $barang_log->barang_akhir }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($barang_log->created_at)->translatedFormat('d F Y H:i') }}
                                        </td>
                                        <td class="text-center">{{ $barang_log->users->name }}</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                        {{ $rs_barang_log->links() }}
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
