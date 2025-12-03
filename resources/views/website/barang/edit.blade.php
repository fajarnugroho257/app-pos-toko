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
                        <a href="{{ route('listBarang') }}" class="btn btn-block btn-success"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                <form action="{{ route('processUpdateListBarang', $detail->id) }}" method="POST" enctype="multipart/form-data">
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
                            <div class="col-md-2">
                                @if (!empty($detailGambar))
                                    <img src="{{ asset('image/barang/' . $detailGambar->detail_image_name) }}" height="150" width="150" class="img-fluid img-thumbnail" alt="">
                                @else
                                    <img id="preview" src="{{ asset('image/barang/default.jpg') }}" height="150" width="150" class="img-fluid img-thumbnail" alt="">
                                @endif
                            </div>
                            <div class="col-md-10">
                                <p>Nama Barang : <br><b>{{ $detail->barang_nama }}</b></p>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Image</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="detail_image_name" class="custom-file-input" id="detail_image_name" accept="image/*" onchange="previewImage(event); showFileName(event);">
                                            <label class="custom-file-label" for="detail_image_name">Pilih file</label>
                                        </div>
                                    </div>
                                    <small class="text-danger">*max 512kb, jpg|png|jpeg</small>
                                </div>
                                <span id="file-name"></span>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status ditampilkan di website {{ $detail->detail_st }}</label>
                                    <select name="detail_st" id="" class="form-control" placeholder="Status ditampilkan di website">
                                        <option value=""></option>
                                        <option value="yes" @selected(old('detail_st', $detailGambar->detail_st ?? '') == 'yes')>YA</option>
                                        <option value="no" @selected(old('detail_st', $detailGambar->detail_st ?? '') == 'no')>TIDAK</option>
                                    </select>
                                </div>
                            </div>
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
    function previewImage(event) {
        const image = document.getElementById('preview');
        const file = event.target.files[0];

        if (file) {
            image.src = URL.createObjectURL(file);
            image.style.display = 'block';
        }
    }
    // 
    function showFileName(event) {
        const file = event.target.files[0];
        const fileNameTag = document.getElementById('file-name');

        if (file) {
            fileNameTag.textContent = file.name;
        } else {
            fileNameTag.textContent = "Belum ada file";
        }
    }
    </script>    
@endsection
