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
                        <a href="{{ route('clientToken') }}" class="btn btn-block btn-success"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                <form action="{{ route('aksiAddClientToken') }}" method="POST">
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
                                    <label>Cabang</label>
                                    <select class="form-control select2" name="cabang_id" style="width: 100%;" id="cabang">
                                        <option value=""></option>
                                        @foreach ($rs_cabang as $cabang)
                                            <option value="{{ $cabang->id }}" @selected(old('cabang_id') == $cabang->id)> {{ $cabang->cabang_nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>User Kasir</label>
                                    <select class="form-control select2" name="user_id" style="width: 100%;" id="user_id">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Generate Token</label>
                                    <input type="text" value="{{ $token_value }}" name="token_value" readonly class="form-control">
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
        $('#cabang').on('change', function(){
            const cabangId = $(this).val();
            $('#user_id').html('<option value="">-- Pilih User Kasir Sesuai Cabang --</option>');

            if (cabangId) {
                $.ajax({
                    url: "{{ url('/get-user-by-cabang') }}",
                    type: 'GET',
                    data: { id: cabangId },
                    success: function(response) {
                        // response berisi array JSON
                        $('#user_id').empty().append('<option value="">-- Pilih User Kasir Sesuai Cabang --</option>');
                        $.each(response, function(key, value) {
                            $('#user_id').append('<option value="'+ value.user_id +'">'+ value.user_nama_lengkap +'</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
            
        });
    </script>
@endsection