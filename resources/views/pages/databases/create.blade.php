@extends('layouts.master')

@section('title')
    Base de Datos
@endsection

@section('content')

<form onsubmit="loading();" method="POST" action="{{route('databases.store')}}" enctype="multipart/form-data">
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
                        <div class="col-md-12 col-lg-2 col-xl-2 mb-8">
                            <label for="connection_id" class="form-label required">Conexión</label>
                            <select class="form-control" name="connection_id" id="connection_id">
                                <option value="">Selecciona una conexión</option>
                                @foreach ($connections as $c)
                                <option value="{{$c->id}}" @selected($c->id == old('connection_id'))>{{$c->name}}</option>
                                @endforeach
                            </select>
                            @error('connection_id')
                                <label for="connection_id" class="text-danger">{{$message}}</label>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-10 col-xl-10 mb-8">
                            <label for="name" class="form-label required">Nombre</label>
                            <input type="text" class="form-control" placeholder="Nombre" id="name" name="name" value="{{old('name')}}"/>
                            @error('name')
                                <label for="name" class="text-danger">{{$message}}</label>
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
<script>
    $('#connection_id').select2();
</script>
@endpush
