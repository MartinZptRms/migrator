@extends('layouts.master')

@section('title')
    Base de Datos
@endsection

@section('content')
<div class="row">
    @livewire('services.setting', ['service' => $service], key($service->id))
</div>

@endsection
@push('custom-scripts')

@endpush
