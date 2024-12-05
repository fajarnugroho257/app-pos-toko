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
                        <a href="{{ route('showBarangCabang', ['slug' => $detail->toko_cabang->slug]) }}"
                            class="btn btn-block btn-success"><i class="fa fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                </div>
                <form action="{{ route('processUpdateBarangCabang') }}" method="POST">
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
                        <div class="row">
                            <div class="col-md-6">
                                <h5><b>Harga Barang <span class="text-danger">Pusat</span></b></h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nama Barang</th>
                                            <th class="text-center">Stok Minimal</th>
                                            <th class="text-center">Harga Beli Barang Pusat</th>
                                            <th class="text-center">Harga Jual Barang Pusat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td>{{ $detail->barang_master->barang_nama }}</td>
                                            <td>{{ $detail->barang_master->barang_stok_minimal }}</td>
                                            <td>Rp.
                                                {{ number_format($detail->barang_master->barang_harga_beli, 0, ',', '.') }}
                                            <td>Rp.
                                                {{ number_format($detail->barang_master->barang_harga_jual, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr />
                        <h5 class="mt-4 mb-4"><b>Harga Barang Cabang <span
                                    class="text-danger">{{ $detail->toko_cabang->cabang_nama }}</span></b></h5>
                        <span class="btn-sm btn-success">Tambah Stok Barang</span>
                        <div class="row" style="align-items: center">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Stok Saat Ini</label>
                                    <input readonly type="number" id="barang_stok"
                                        value="{{ old('barang_stok', $detail->barang_stok) }}" name="barang_stok"
                                        class="form-control" placeholder="Stok Barang">
                                </div>
                            </div>
                            <div>
                                <p style="font-size: 40px" class="text-danger">+</p>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Penambahan Stok</label>
                                    <input type="number" id="barang_stok_penambahan"
                                        value="{{ old('barang_stok_penambahan') }}" name="barang_stok_penambahan"
                                        class="form-control" placeholder="Penambahan Stok Barang">
                                </div>
                            </div>
                            <div>
                                <p style="font-size: 40px" class="text-danger">=</p>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Hasil Stok Tersedia</label>
                                    <input readonly type="number" id="barang_stok_hasil"
                                        value="{{ old('barang_stok_hasil') }}" name="barang_stok_hasil"
                                        class="form-control" placeholder="Hasil Stok Tersedia">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Harga Jual Barang Cabang</label>
                                    <input type="number"
                                        value="{{ old('cabang_barang_harga', $detail->cabang_barang_harga) }}"
                                        name="cabang_barang_harga" class="form-control" placeholder="Harga Barang">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Harga Barang</label>
                                    <select name="barang_st" id="form-control"
                                        placeholder="Status Barang"class="form-control">
                                        <option value=""></option>
                                        <option value="no" @selected(old('barang_st', $detail->barang_st) == 'no')>Tidak digunakan</option>
                                        <option value="yes" @selected(old('barang_st', $detail->barang_st) == 'yes')>Digunakan</option>
                                    </select>
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
@section('javascript')
    <script>
        $(document).ready(function() {
            // Ketika nilai di input barang_stok_penambahan berubah
            $('#barang_stok_penambahan').on('input', function() {
                // Ambil nilai dari barang_stok
                var stokSaatIni = parseFloat($('#barang_stok').val()) || 0;

                // Ambil nilai dari barang_stok_penambahan
                var penambahanStok = parseFloat($(this).val()) || 0;

                // Cek apakah penambahanStok kurang dari 0
                if (penambahanStok < 0) {
                    penambahanStok = 0;
                    $(this).val(penambahanStok); // Reset nilai input ke 0
                }

                // Hitung hasil stok tersedia
                var hasilStokTersedia = stokSaatIni + penambahanStok;

                // Tampilkan hasil di barang_stok_hasil
                $('#barang_stok_hasil').val(hasilStokTersedia);
            });
        });
    </script>
@endsection
@endsection
