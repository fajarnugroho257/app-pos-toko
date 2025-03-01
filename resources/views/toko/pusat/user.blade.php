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
                        <a href="{{ route('tokoPusat') }}" class="btn btn-block btn-success"><i
                                class="fa fa-arrow-left"></i>
                            Kembali</a>
                    </div>
                </div>
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
                        <div class="col-md-12">
                            <label>User Tersedia Di Toko Ini</label>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 10px">No</th>
                                        <th style="width: 20%">Nama</th>
                                        <th style="width: 19%">Username</th>
                                        <th>Role</th>
                                        <th style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $no = 1;
                                    @endphp
                                    @foreach ($rs_users as $user)
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{ $user->users->name }}</td>
                                            <td class="text-center">{{ $user->users->username }}</td>
                                            <td class="text-center">{{ $user->users->app_role->role_name }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('processDeleteUserToko', [$user->id, $detail->id]) }}"
                                                    onclick="return confirm('Apakah anda yakin akan menghapus user ini ?')"
                                                    class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Daftar User</label>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width: 10px">No</th>
                                            <th style="width: 20%">Nama</th>
                                            <th>Role</th>
                                            <th style="width: 10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                        @endphp
                                        @foreach ($exist_users as $exist_user)
                                            <tr>
                                                <td class="text-center">{{ $no++ }} </td>
                                                <td style="width: 20%">{{ $exist_user->name }}</td>
                                                <td class="text-center">{{ $exist_user->app_role->role_name }}</td>
                                                <td class="text-center">
                                                    <a href="{{ route('processAddUserToko', ['user_id' => $exist_user->user_id, 'pusat_id' => $detail->id]) }}"
                                                        onclick="return confirm('Apakah anda yakin akan mebambah user ini ?')"
                                                        class="btn btn-sm btn-success"><i class="fa fa-plus"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
