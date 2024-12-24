@extends('template.base.base')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Dashboard</h1>
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
            <div class="container-fluid">
                <form action="{{ route('cariSumary') }}" method="POST">
                    @method('POST')
                    @csrf
                    <div class="row mb-3" style="display: flex; align-items: center">
                        <div class="col-lg-2">
                            <input type="date" value="{{ $startDateDash ?? '' }}" name="startDateDash"
                                class="form-control">
                        </div>
                        <div class="col-lg-2">
                            <input type="date" value="{{ $endDateDash ?? '' }}" name="endDateDash" class="form-control">
                        </div>
                        <div class="col-lg-2">
                            <select name="cabang_id" id="" class="form-control">
                                <option value="all">Semua Cabang</option>
                                @foreach ($rs_cabang as $cabang)
                                    <option @selected($cabang_id == $cabang->id) value="{{ $cabang->id }}">
                                        {{ $cabang->cabang_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <button type="submit" name="aksi" value="cari" class="btn btn-sm btn-primary"><i
                                    class="fa fa-search"></i>
                                Cari</button>
                            <button type="submit" name="aksi" value="reset" class="btn btn-sm btn-dark ml-2"><i
                                    class="fa fa-times"></i>
                                Reset</button>
                        </div>
                    </div>
                </form>
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ 'Rp. ' . number_format($transRupiah, 0, ',', '.') }}</h3>
                                <p>Pendapatan</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ route('showTransaksi', ['cabang_id' => $cabang_id]) }}"
                                class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $transaksi }}</h3>
                                <p>Transaksi</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="{{ route('showPendapatan', ['cabang_id' => $cabang_id]) }}"
                                class="small-box-footer">Selengkapnya<i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $jlhCabang }}</h3>
                                <p>Toko Cabang</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="{{ route('tokoCabang') }}" class="small-box-footer">Selengkapnya <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                    <div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $kurangStok }}</h3>
                                <p>Stok Barang Minim</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="{{ route('showBarangMinim', ['cabang_id' => $cabang_id]) }}"
                                class="small-box-footer">Selengkapnya
                                <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <nav class="w-100">
                            <div class="nav nav-tabs" id="product-tab" role="tablist">
                                <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab"
                                    href="#product-desc" role="tab" aria-controls="product-desc"
                                    aria-selected="true">Pendapatan</a>
                                <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab"
                                    href="#product-comments" role="tab" aria-controls="product-comments"
                                    aria-selected="false">Transaksi</a>
                                <a class="nav-item nav-link" id="product-rating-tab" data-toggle="tab"
                                    href="#product-rating" role="tab" aria-controls="product-rating"
                                    aria-selected="false">Stok Minim</a>
                            </div>
                        </nav>
                        <div class="tab-content p-3" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="product-desc" role="tabpanel"
                                aria-labelledby="product-desc-tab">
                                <figure class="highcharts-figure">
                                    <div id="pendapatan"></div>
                                </figure>
                            </div>
                            <div class="tab-pane fade" id="product-comments" role="tabpanel"
                                aria-labelledby="product-comments-tab">
                                <figure class="highcharts-figure">
                                    <div id="transaksi"></div>
                                </figure>
                            </div>
                            <div class="tab-pane fade" id="product-rating" role="tabpanel"
                                aria-labelledby="product-rating-tab">
                                <div class="text-center mb-4">
                                    <h5>Stok Minimum</h5>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th style="width: 10px">No</th>
                                                <th>Nama Barang</th>
                                                <th>Harga Jual Dicabang</th>
                                                <th>Stok Minimal</th>
                                                <th>Stok</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        @php
                                            $no = 1;
                                        @endphp
                                        <tbody>
                                            @foreach ($rs_stok as $key => $barang)
                                                <tr @if ($barang->barang_stok < $barang->barang_master->barang_stok_minimal) class="table-danger" @endif>
                                                    <td class="text-center">{{ $no++ }}</td>
                                                    <td>{{ $barang->barang_master->barang_nama }}</td>
                                                    <td class="text-center">Rp.
                                                        {{ number_format($barang->cabang_barang_harga, 0, ',', '.') }}</td>
                                                    <td class="text-center">
                                                        {{ $barang->barang_master->barang_stok_minimal }}
                                                    </td>
                                                    <td class="text-center">{{ $barang->barang_stok }}</td>
                                                    <td class="text-center">
                                                        @if ($barang->barang_st == 'yes')
                                                            <span class="btn-sm btn-success">Digunakan</span>
                                                        @else
                                                            <span class="btn-sm btn-danger">Tidak Digunakan</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        Footer
                    </div>
                    <!-- /.card-footer-->
                </div>
                <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
@endsection
@section('javascript')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script>
        // Ambil data dari PHP ke JavaScript
        const datas = @json($tranMonth);
        //
        const pendapatan = datas.map(item => item.pendapatan);
        const tanggal = datas.map(item => item.tanggal);
        const transaksi = datas.map(item => item.jlh_transaksi);
        // grafik pendapatan
        Highcharts.chart('pendapatan', {

            title: {
                text: 'Grafik Pendapatan',
                align: 'left'
            },
            yAxis: {
                title: {
                    text: 'Dalam Rupiah'
                }
            },

            xAxis: {
                categories: tanggal, // Nama bulan
                title: {
                    text: 'Month'
                }
            },

            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            series: [{
                name: 'Pendapatan',
                data: pendapatan
            }],
            tooltip: {
                valuePrefix: 'Rp. ',
                valueDecimals: 0, // Tidak ada desimal
                valueSuffix: '' // Tambahkan jika perlu
            },
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

        });
        // grafik transaksi
        Highcharts.chart('transaksi', {

            title: {
                text: 'Grafik Transaksi',
                align: 'left'
            },
            yAxis: {
                title: {
                    text: 'Transaksi'
                }
            },

            xAxis: {
                categories: tanggal, // Nama bulan
                title: {
                    text: 'Month'
                }
            },

            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },

            series: [{
                name: 'Transaksi',
                data: transaksi
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

        });
    </script>
@endsection
