@extends('layouts.master')

@section('title')
    Habilidades
@endsection

@section('content')

<form onsubmit="loading();" method="POST" action="{{route('skills.update', $skill->id)}}" enctype="multipart/form-data">
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
                        <div class="col-md-12 col-lg-6 col-xl-6 mb-8">
                            <label for="name" class="form-label required">Nombre</label>
                            <input type="text" class="form-control" placeholder="Nombre" id="name" name="name" value="{{old('name', $skill->name)}}"/>
                            @error('name')
                                <label for="name" class="text-danger">{{$message}}</label>
                            @enderror
                        </div>
                        <div class="col-md-12 col-lg-6 col-xl-6 mb-8">
                            <label for="name" class="form-label required">Tipo</label>
                            <select class="form-select" name="type" id="type"
                                    data-control="select2" data-placeholder="Selecciona una opción" >
                                    <option></option>
                                @foreach ($types as $i => $type)
                                    <option value="{{$i}}" @selected( $i == old('type', $skill->type) )>{{$type}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 col-lg-12 col-xl-12">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea type="text" class="form-control" placeholder="Proyecto"
                                    name="description" id="description" value="{{old('description',$skill->description)}}"> </textarea>
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
