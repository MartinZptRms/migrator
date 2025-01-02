@extends('layouts.master')

@section('title')
    Habilidades
@endsection

@section('content')

<form onsubmit="loading();" method="POST" action="{{route('connections.update', $connection->id)}}" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card h-md-100">
                <div class="card-header">
                    <h3 class="card-title">
                        Información General
                    </h3>
                    <div class="card-toolbar">
                        <div class="justify-content-center">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-5">
                    <div class="row">
                        <div class="col-md-12 col-lg-12 col-xl-12 mb-8">
                            <label for="name" class="form-label required">Nombre</label>
                            <input type="text" class="form-control" placeholder="Nombre" id="name" name="name" value="{{old('name', $connection->name)}}"/>
                            @error('name')
                                <label for="name" class="text-danger">{{$message}}</label>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-10 col-xl-10 mb-8">
                            <label for="host" class="form-label required">Host</label>
                            <input type="text" class="form-control" placeholder="Host" id="host" name="host" value="{{old('host',$connection->host)}}"/>
                            @error('host')
                                <label for="host" class="text-danger">{{$message}}</label>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-2 col-xl-2 mb-8">
                            <label for="port" class="form-label required">Puerto</label>
                            <input type="text" class="form-control" placeholder="Puerto" id="port" name="port" value="{{old('port',$connection->port)}}"/>
                            @error('port')
                                <label for="port" class="text-danger">{{$message}}</label>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-6 col-xl-6 mb-8">
                            <label for="username" class="form-label required">Usuername</label>
                            <input type="text" class="form-control" placeholder="Username" id="username" name="username" value="{{old('username',$connection->username)}}"/>
                            @error('username')
                                <label for="username" class="text-danger">{{$message}}</label>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-6 col-xl-6 mb-8">
                            <label for="password" class="form-label required">Contraseña</label>
                            <input type="password" class="form-control" placeholder="Contraseña" id="password" name="password" value="{{old('password',$connection->password)}}"/>
                            @error('password')
                                <label for="password" class="text-danger">{{$message}}</label>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-8">
        <div class="col-lg-12 col-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-success">Guardar</button>
        </div>
    </div>
</form>


@endsection
@push('custom-scripts')

@endpush
