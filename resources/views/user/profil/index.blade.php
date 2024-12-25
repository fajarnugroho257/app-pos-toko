@extends('template.base.base')
@section('content')
    <div class="content-wrapper" style="min-height: 2646.8px;">
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
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">

                        <!-- Profile Image -->
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img class="profile-user-img img-fluid img-circle"
                                        src="{{ asset('image/profil/' . $detail->toko_pusat->user_image) }}"
                                        alt="User profile picture">
                                </div>
                                <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>
                                <p class="text-muted text-center">{{ Auth::user()->app_role->role_name }}</p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9">
                        <div class="card card-primary card-outline">
                            <div class="card-header ">
                                <h3 class="card-title">{{ $title }}</h3>
                            </div>
                            <form action="{{ route('processUpdateProfil') }}" method="POST" enctype="multipart/form-data">
                                <input type="hidden" value="{{ $detail->user_id }}" name="user_id">
                                <input type="hidden" value="{{ $detail->toko_pusat->id }}" name="id">
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
                                    <div class="form-group">
                                        <label>Nama Toko Pusat</label>
                                        <input type="text"
                                            value="{{ old('pusat_nama', $detail->toko_pusat->pusat_nama) }}"
                                            name="pusat_nama" class="form-control" placeholder="Nama Toko Pusat">
                                    </div>
                                    <div class="form-group">
                                        <label>Pemilik</label>
                                        <input type="text"
                                            value="{{ old('pusat_pemilik', $detail->toko_pusat->pusat_pemilik) }}"
                                            name="pusat_pemilik" class="form-control" placeholder="Pemilik">
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <input type="text"
                                            value="{{ old('pusat_alamat', $detail->toko_pusat->pusat_alamat) }}"
                                            name="pusat_alamat" class="form-control" placeholder="Nama Toko Pusat">
                                    </div>
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" value="{{ old('username', $detail->username) }}"
                                            name="username" class="form-control" placeholder="Username">
                                    </div>
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" value="" name="password" class="form-control"
                                            autocomplete="false" placeholder="Password">
                                        <small class="text-danger"><i>Kosongi jika tidak ingin merubah password</i></small>
                                    </div>
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
                                <!-- /.card-body -->
                                <div class="card-footer clearfix">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection
