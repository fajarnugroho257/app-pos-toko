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
                        {{-- <a href="{{ route('tambahTokoCabang') }}" class="btn btn-block btn-success"><i
                                class="fa fa-file-pdf"></i>
                            Download</a> --}}
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('cariStokBarang') }}" method="POST" class="mb-4">
                        @method('POST')
                        @csrf
                        <div class="row mb-2">
                            <div class="col-md-6 ml-0 d-flex item-center" style="gap: 10px;">
                                <select name="cabang_id" id="" class="form-control">
                                    <option value="gudang" @selected($cabang_id == 'gudang')>Semua Cabang</option>
                                    @foreach ($rs_cabang as $cabang)
                                        <option @selected($cabang_id == $cabang->id) value="{{ $cabang->id }}">
                                            {{ $cabang->cabang_nama }}</option>
                                    @endforeach
                                </select>
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
                    <table class="table table-bordered">
                        <thead>
                            @if ($cabang_id == 'gudang')
                                <tr class="text-center">
                                    <th style="width: 10px">No</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                </tr>
                            @else
                                <tr class="text-center">
                                    <th style="width: 10px">No</th>
                                    <th>Cabang</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                                $grandTotal = 0;
                            @endphp
                            @if ($cabang_id == 'gudang')
                                @foreach ($rs_terbanyak as $key => $terbanyak)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $terbanyak->barang_nama }}</td>
                                        <td class="text-center text-bold">{{ $terbanyak->penjualan }}</td>
                                    </tr>
                                    @php
                                        $grandTotal += $terbanyak->penjualan;
                                    @endphp
                                @endforeach
                            @else
                                @foreach ($rs_terbanyak as $key => $terbanyak)
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td class="text-center text-bold">{{ $terbanyak->cabang_nama }}</td>
                                        <td>{{ $terbanyak->barang_nama }}</td>
                                        <td class="text-center text-bold">{{ $terbanyak->cart_qty }}</td>
                                    </tr>
                                    @php
                                        $grandTotal += $terbanyak->cart_qty;
                                    @endphp
                                @endforeach
                            @endif
                            <tr>
                                @if ($cabang_id == 'gudang')
                                    <td colspan="2" class="text-right">Total</td>
                                @else
                                    <td colspan="3" class="text-right text-bold">Total</td>
                                @endif
                                <td class="text-center text-bold">{{ $grandTotal }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
@endsection
