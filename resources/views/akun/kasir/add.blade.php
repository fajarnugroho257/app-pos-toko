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
                        <a href="{{ route('akunKasir') }}" class="btn btn-block btn-success"><i
                                class="fa fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                </div>
                <form action="{{ route('aksiTambahAkunKasir') }}" method="POST" enctype="multipart/form-data">
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
                                <div class="form-group">
                                    <label>Nama lengkap</label>
                                    <input type="text" value="{{ old('name') }}" name="name" class="form-control"
                                        placeholder="Nama lengkap">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cabang</label>
                                    <select class="form-control select2" name="cabang_id" style="width: 100%;">
                                        <option value=""></option>
                                        @foreach ($rs_cabang as $cabang)
                                            <option value="{{ $cabang->id }}"
                                                {{ old('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                                {{ $cabang->cabang_nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" value="{{ old('username') }}" name="username"
                                        class="form-control" placeholder="Username">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" value="{{ old('password') }}" name="password"
                                        class="form-control" placeholder="Password">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" value="{{ old('user_alamat') }}" name="user_alamat"
                                        class="form-control" placeholder="Alamat">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select class="form-control" name="user_jk" style="width: 100%;">
                                        <option value=""></option>
                                        <option value="L" @selected(old('user_jk') == 'L')>Laki - Laki</option>
                                        <option value="P" @selected(old('user_jk') == 'P')>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" name="user_st" style="width: 100%;">
                                        <option value=""></option>
                                        <option value="yes" @selected(old('user_st') == 'yes')>Active</option>
                                        <option value="no" @selected(old('user_st') == 'no')>Non Active</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exampleInputFile">Foto Profil</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="user_image" class="custom-file-input"
                                                id="exampleInputFile">
                                            <label class="custom-file-label" for="exampleInputFile">Pilih file</label>
                                        </div>

                                    </div>
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
