@extends('layouts.master')

@section('title')
    Base de Datos
@endsection

@section('content')

<form onsubmit="loading();" method="POST" action="{{route('services.store')}}" enctype="multipart/form-data">
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
                        <div class="col-md-12 col-lg-4 col-xl-4 mb-8">
                            <label for="name" class="form-label required">Nombre</label>
                            <input type="text" class="form-control" placeholder="Nombre" id="name" name="name" value="{{old('name')}}"/>
                            @error('name')
                                <label for="name" class="text-danger">{{$message}}</label>
                            @enderror
                        </div>

                        <div class="col-md-12 col-lg-4 col-xl-4 mb-8">
                            <label for="every" class="form-label required">Cada</label>
                            <input type="text" class="form-control" placeholder="Cada" id="every" name="every" value="{{old('every')}}"/>
                            @error('every')
                                <label for="every" class="text-danger">{{$message}}</label>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-4 mb-8">
                            <label for="every_unit" class="form-label required">Unidad</label>
                            <input type="text" class="form-control" placeholder="Cada" id="every_unit" name="every_unit" value="{{old('every_unit')}}"/>
                            @error('every_unit')
                                <label for="every_unit" class="text-danger">{{$message}}</label>
                            @enderror
                        </div>

                        <div class="col-md-12 col-lg-12 col-xl-12 mb-8">
                            <label for="description" class="form-label required">Descripción</label>
                            <input type="text" class="form-control" placeholder="Descripción" id="description" name="description" value="{{old('description')}}"/>
                            @error('description')
                                <label for="description" class="text-danger">{{$message}}</label>
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
