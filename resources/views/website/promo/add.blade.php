@extends('template.base.base')
@section('content')
    <div class="content-wrapper">
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
            </div>
        </section>
        <section class="content">
            <div class="card">
                <div class="card-header ">
                    <h3 class="card-title">{{ $title }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('promo') }}" class="btn btn-block btn-success"><i
                                class="fa fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                </div>
                <form action="{{ route('processAddPromo') }}" method="POST" enctype="multipart/form-data">
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
                                    <label>Daftar Barang</label>
                                    <select class="form-control select2" name="barang_id" style="width: 100%;" id="barang_id">
                                        <option value=""></option>
                                        @foreach ($rs_barang as $barang)
                                            <option value="{{ $barang->barang_master->id }}" @selected(old('barang_id') == $barang->barang_master->id)> {{ $barang->barang_master->barang_barcode }} || {{ $barang->barang_master->barang_nama }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger">*hanya barang yang sudah ada gambarnya yang tersedia dan barang yang belum ditambahkan ke promo</small>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <p>Harga Satuan : <b><span id="barang_harga_jual"></span></b></p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-success">Gorsir Minimal Pembelian : <b><span id="barang_grosir_pembelian"></span></b></p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-success">Harga Grosir : <b><span id="barang_grosir_harga_jual"></span></b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn btn-success" id="sync-data"><i class="fa fa-copy"></i> Salin harga</div>
                                <br><small class="text-danger">*akan menyalin harga dari data master barang</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga Satuan</label>
                                    <input type="text" value="{{ old('promo_harga') }}" readonly name="promo_harga" class="form-control" placeholder="Harga Satuan" id="res_barang_harga_jual">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Grosir Minimal Pembelian</label>
                                    <input type="text" value="{{ old('promo_grosir_pembelian') }}" readonly name="promo_grosir_pembelian" class="form-control" placeholder="Grosir Minimal Pembelian" id="res_barang_grosir_pembelian">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Harga Grosir</label>
                                    <input type="text" value="{{ old('promo_grosir_harga') }}" readonly name="promo_grosir_harga" class="form-control" placeholder="Harga Grosir" id="res_barang_grosir_harga_jual">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Promo Mulai</label>
                                <input type="date" class="form-control" value="{{ old('promo_start') }}" name="promo_start">
                            </div>
                            <div class="col-md-3">
                                <label for="">Promo Selesai</label>
                                <input type="date" class="form-control" value="{{ old('promo_end') }}" name="promo_end">
                            </div>
                            <div class="col-md-3">
                                <label for="">Digunakan</label>
                                <select name="promo_st" class="form-control" id="">
                                    <option value="">Promo Digunakan</option>
                                    <option value="yes" @selected(old('promo_st') == 'yes')>YA</option>
                                    <option value="no" @selected(old('promo_st') == 'no')>TIDAK</option>
                                </select>
                            </div>
                            <small class="text-danger">*Perubahan harga barang ini <b>tidak akan merubah harga master barang</b>, Silahkan untuk menyesuaikan harga barang di menu master data barang</small>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
@section('javascript')
    <script>
        $('#barang_id').on('change', function() {
            let barang_id = $(this).val();
            console.log(barang_id);
            $.ajax({
                url: "{{ route('detailBarang') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    barang_id: barang_id,
                },
                success: function(response) {
                    console.log(response.detail);
                    if (response.success) {
                        const detail = response.detail;
                        $('#barang_harga_jual').text(detail.barang_harga_jual);
                        $('#barang_grosir_harga_jual').text(detail.barang_grosir_harga_jual);
                        $('#barang_grosir_pembelian').text(detail.barang_grosir_pembelian);

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
        $('#sync-data').on('click', function() {
            const res_barang_harga_jual = $('#barang_harga_jual').text();
            const res_barang_grosir_pembelian = $('#barang_grosir_pembelian').text();
            const res_barang_grosir_harga_jual = $('#barang_grosir_harga_jual').text();
            // 
            $('#res_barang_harga_jual').val(res_barang_harga_jual);
            $('#res_barang_grosir_pembelian').val(res_barang_grosir_pembelian);
            $('#res_barang_grosir_harga_jual').val(res_barang_grosir_harga_jual);
        });
    </script>
@endsection
