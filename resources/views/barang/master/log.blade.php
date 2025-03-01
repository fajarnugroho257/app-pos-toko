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
                        <a href="{{ route('masterBarang') }}" class="btn btn-block btn-success"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="mb-4 text-center">Daftar Log Barang<br /><span class="text-primary"><b>{{ $detail->barang_nama }}</b></span></h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 10px">No</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Stok Awal</th>
                                    <th>Stok Perubahan</th>
                                    <th>Stok Akhir</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rs_log as $key => $log)
                                    @php
                                    if ($log->barang_st == 'pengiriman') {
                                        $color = 'text-danger';
                                    } elseif($log->barang_st == 'penambahan') {
                                        $color = 'text-success';
                                    } else {
                                        $color = 'text-warning';
                                    }
                                    @endphp
                                    <tr class="text-center">
                                        <td>{{ $rs_log->firstItem() + $key }}</td>
                                        <td class="text-center">
                                            {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d F Y H:i') }}
                                        </td>
                                        <td>
                                            @if ($log->barang_st == 'pengiriman')
                                                <button class="btn btn-sm btn-info"><i class="fa fa-truck"></i> Kirim Ke - {{$log->toko_cabang->cabang_nama}}</button>
                                            @elseif ($log->barang_st == 'penambahan')
                                            <button class="btn btn-sm btn-success"><i class="fa fa-arrow-down"></i> Penambahan</button>
                                            @else
                                            <button class="btn btn-sm btn-danger"><i class="fa fa-minus"></i> Pengurangan</button>
                                            @endif
                                        </td>
                                        <td>{{ $log->barang_master_awal }}</td>
                                        <td>
                                            <b class="{{$color}}">{{ $log->barang_master_perubahan }}</b>
                                        </td>
                                        <td>{{ $log->barang_master_akhir }}</td>
                                        <td>{{ $log->user->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                        {{ $rs_log->links() }}
                    </ul>
                </div>
                <!-- /.card-footer-->
            </div>
            <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
@endsection
