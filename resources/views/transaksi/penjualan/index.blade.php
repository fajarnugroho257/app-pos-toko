@extends('template.base.base')
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.min.css">
@endsection
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
                    <h4 class="text-center mb-3"><b>Transaksi</b></h4>
                    <div id="flipclock" class="clock"></div>
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

                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">

                </div>
                <!-- /.card-footer-->
            </div>
            <!-- /.card -->

        </section>
        <!-- /.content -->
    </div>
@section('javascript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Inisialisasi FlipClock
            const clock = new FlipClock(document.getElementById('flipclock'), {
                autoStart: false,
                clockFace: 'DailyCounter',
                countdown: true,
                callbacks: {
                    stop: function() {
                        alert('Countdown selesai!');
                    }
                }
            });

            // Atur timer (contoh: 10 detik)
            clock.setTime(10);
            clock.start();
        });
    </script>
@endsection
@endsection
