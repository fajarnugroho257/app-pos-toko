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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><span class="text-danger">Stok Minimal</span></label>
                                    <input type="text"
                                        value="{{ old('barang_stok_minimal', $detail->barang_stok_minimal) }}"
                                        name="barang_stok_minimal" class="form-control" placeholder="Stok Minimal" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><span class="text-primary">Harga Beli</span></label>
                                    <input type="text"
                                        value="{{ old('barang_harga_beli', $detail->barang_harga_beli) }}"
                                        name="barang_harga_beli" id="barang_harga_beli" class="form-control" placeholder="Harga Barang" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="background-color: rgba(126, 124, 124, 0.427)">
                            <div class="col-md-2" style="display: flex; align-items: center;">
                                <h4>Harga Satuan</h4>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><span class="text-dark">%</span></label>
                                    <input type="text" required
                                        value="{{ old('barang_persentase', $detail->barang_persentase) }}"
                                        name="barang_persentase" id="barang_persentase" class="form-control" placeholder="Persentase Barang" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><span class="text-primary">Keuntungan</span></label>
                                    <input type="number" readonly required
                                        value="{{ old('barang_keuntungan', $detail->barang_keuntungan) }}"
                                        name="barang_keuntungan" id="barang_keuntungan" class="form-control" placeholder="Keuntungan Barang">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><span class="text-success">Harga Jual</span></label>
                                    <input type="text" value="{{ old('barang_harga_jual', $detail->barang_harga_jual) }}" required name="barang_harga_jual" id="barang_harga_jual" class="form-control" placeholder="Harga Barang" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="background-color: rgba(227, 214, 214, 0.427)">
                            <div class="col-md-2" style="display: flex; align-items: center;">
                                <h4>Harga Grosir</h4>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><span class="text-warning">Minimal Pembelian</span></label>
                                    <input type="text" required
                                        value="{{ old('barang_grosir_pembelian', $detail->barang_grosir_pembelian) }}"
                                        name="barang_grosir_pembelian" id="barang_grosir_pembelian" class="form-control" placeholder="Minimal Pembelian" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><span class="text-dark">%</span></label>
                                    <input type="text" required
                                        value="{{ old('barang_grosir_persentase', $detail->barang_grosir_persentase) }}"
                                        name="barang_grosir_persentase" id="barang_grosir_persentase" class="form-control" placeholder="Persentase Barang" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><span class="text-primary">Keuntungan</span></label>
                                    <input type="number" readonly required
                                        value="{{ old('barang_grosir_keuntungan', $detail->barang_grosir_keuntungan) }}"
                                        name="barang_grosir_keuntungan" id="barang_grosir_keuntungan" class="form-control" placeholder="Keuntungan Barang">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label><span class="text-success">Harga Jual</span></label>
                                    <input type="text" value="{{ old('barang_grosir_harga_jual', $detail->barang_grosir_harga_jual) }}" name="barang_grosir_harga_jual" id="barang_grosir_harga_jual" class="form-control" placeholder="Harga Barang" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="align-items: center">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Stok Barang Pusat Saat Ini</label>
                                    <input readonly type="number" id="barang_master_stok"
                                        value="{{ old('barang_master_stok', $detail->barang_master_stok) }}" name="barang_master_stok"
                                        class="form-control" placeholder="Stok Barang Pusat">
                                    <small class="text-primary">Terisi secara otomatis</small>
                                </div>
                            </div>
                            <div>
                                <p style="font-size: 40px" class="text-danger">+</p>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Perubahan Stok</label>
                                    <input type="text" id="barang_stok_perubahan"
                                        value="{{ old('barang_stok_perubahan') }}" name="barang_stok_perubahan"
                                        class="form-control" placeholder="Penambahan Stok Barang" oninput="this.value = this.value.replace(/[^0-9-]/g, '').replace(/(?!^)-/g, '')">
                                    <small class="text-danger">*isikan 0, jika tidak berubah</small>
                                </div>
                            </div>
                            <div>
                                <p style="font-size: 40px" class="text-danger">=</p>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Hasil Stok Tersedia</label>
                                    <input readonly type="number" id="barang_master_stok_hasil"
                                        value="{{ old('barang_master_stok_hasil') }}" name="barang_master_stok_hasil"
                                        class="form-control" placeholder="Hasil Stok Tersedia">
                                    <small class="text-primary">Terisi secara otomatis</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Barcode Barang</label>
                                    <input type="text" value="{{ old('barang_barcode', $detail->barang_barcode) }}"
                                        name="barang_barcode" class="form-control" placeholder="Barcode Barang" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <input type="hidden" value="{{ $detail->barang_barcode }}"
                                    name="old_barang_barcode" class="form-control" placeholder="Barcode Barang">
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
@section('javascript')
    <script>
        $(document).ready(function() {
            // Ketika nilai di input barang_stok_perubahan berubah
            $('#barang_stok_perubahan').on('input', function() {
                // Ambil nilai dari barang_stok
                var stokSaatIni = parseFloat($('#barang_master_stok').val()) || 0;

                // Ambil nilai dari barang_stok_perubahan
                var penambahanStok = parseFloat($(this).val()) || 0;
                // Cek apakah penambahanStok kurang dari 0
                // if (penambahanStok < 0) {
                //     penambahanStok = 0;
                //     $(this).val(penambahanStok); // Reset nilai input ke 0
                // }

                // Hitung hasil stok tersedia
                var hasilStokTersedia = stokSaatIni + penambahanStok;

                // Tampilkan hasil di barang_master_stok_hasil
                $('#barang_master_stok_hasil').val(hasilStokTersedia);
            });
            // HARGA SATUAN
            $('#barang_persentase').on('input', function() {
                // harga Beli
                const harga_beli = parseFloat($('#barang_harga_beli').val()) || 0;
                const persentase = parseFloat($(this).val()) || 0;
                const keuntungan = persentase / 100 * harga_beli;
                // keuntungan
                $('#barang_keuntungan').val(Math.round(keuntungan));
                // harga jual
                const harga_jual = harga_beli + keuntungan;
                $('#barang_harga_jual').val(harga_jual);
            });
            $('#barang_harga_beli').on('input', function() {
                console.log('atas');
                // harga Beli
                const harga_beli = parseFloat($(this).val()) || 0;
                //
                const persentase = parseFloat($('#barang_persentase').val()) || 0;
                const keuntungan = persentase / 100 * harga_beli;
                // keuntungan
                $('#barang_keuntungan').val(Math.round(keuntungan));
                // harga jual
                const harga_jual = harga_beli + keuntungan;
                $('#barang_harga_jual').val(harga_jual);
            });
            $('#barang_harga_jual').on('input', function() {
                // harga jual
                const harga_jual = parseFloat($(this).val()) || 0;
                const harga_beli = parseFloat($('#barang_harga_beli').val()) || 0;
                //keuntungan
                const keuntungan = harga_jual - harga_beli;
                $('#barang_keuntungan').val(keuntungan);
                // persen
                const persentase = keuntungan / harga_beli * 100;
                $('#barang_persentase').val(Math.round(persentase));
            });
            // HARGA GROSIR
            $('#barang_grosir_persentase').on('input', function() {
                // harga Beli
                const harga_beli = parseFloat($('#barang_harga_beli').val()) || 0;
                const persentase = parseFloat($(this).val()) || 0;
                const keuntungan = persentase / 100 * harga_beli;
                // keuntungan
                $('#barang_grosir_keuntungan').val(Math.round(keuntungan));
                // harga jual
                const harga_jual = harga_beli + keuntungan;
                $('#barang_grosir_harga_jual').val(harga_jual);
            });
            $('#barang_harga_beli').on('input', function() {
                console.log('bawah');
                // harga Beli
                const harga_beli = parseFloat($(this).val()) || 0;
                //
                const persentase = parseFloat($('#barang_grosir_persentase').val()) || 0;
                const keuntungan = persentase / 100 * harga_beli;
                // keuntungan
                $('#barang_grosir_keuntungan').val(Math.round(keuntungan));
                // harga jual
                const harga_jual = harga_beli + keuntungan;
                $('#barang_grosir_harga_jual').val(harga_jual);
            });
            $('#barang_grosir_harga_jual').on('input', function() {
                // harga jual
                const harga_jual = parseFloat($(this).val()) || 0;
                const harga_beli = parseFloat($('#barang_harga_beli').val()) || 0;
                //keuntungan
                const keuntungan = harga_jual - harga_beli;
                $('#barang_grosir_keuntungan').val(keuntungan);
                // persen
                const persentase = keuntungan / harga_beli * 100;
                $('#barang_grosir_persentase').val(Math.round(persentase));
            });
        });
    </script>
@endsection
