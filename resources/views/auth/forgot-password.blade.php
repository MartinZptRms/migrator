@extends('layouts.master_auth')

@section('content')
<!--begin::Authentication - Password reset -->
<div class="d-flex flex-column flex-lg-row flex-column-fluid">
    <!--begin::Body-->
    <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
        <!--begin::Form-->
        <div class="d-flex flex-center flex-column flex-lg-row-fluid">
            <!--begin::Wrapper-->
            <div class="w-lg-500px p-10">
                <!--begin::Form-->
                <form class="form w-100" action="{{route('password.email')}}" method="POST">
                    @csrf
                    <!--begin::Heading-->
                    <div class="text-center mb-10">
                        <!--begin::Title-->
                        <h1 class="text-white fw-bolder mb-3">¿Olvidaste tu contraseña?</h1>
                        <!--end::Title-->
                        <!--begin::Link-->
                        <div class="text-white fw-semibold fs-6">Ingresa tu correo electronico para reestablecer tu contraseña</div>
                        <!--end::Link-->
                    </div>
                    <!--begin::Heading-->
                    <!--begin::Input group=-->
                    <div class="fv-row mb-8">
                        <!--begin::Email-->
                        <input type="text" placeholder="Email" name="email" autocomplete="off" class="form-control" />
                        <!--end::Email-->
                    </div>
                    @if(session('status'))
                    <div class="d-flex fw-semibold text-primary fs-4 mb-8">
                        <h4 class="text-primary">{{ session('status') }}</h4>
                    </div>
                    @endif
                    @if($errors->any())
                        @foreach ($errors->all() as $e)
                        <div class="d-flex fw-semibold fs-4 mb-8">
                            <h4 class="text-danger">{{ $e }}</h4>
                        </div>
                        @endforeach
                    @endif
                    <!--begin::Actions-->
                    <div class="d-flex flex-wrap justify-content-center pb-lg-0">
                        <button type="submit" id="kt_password_reset_submit" class="btn btn-primary me-4">
                            Enviar
                        </button>
                        <a href="{{route('login')}}" class="btn btn-white">Cancelar</a>
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Form-->
        <!--begin::Footer-->
        <div class="d-flex flex-center flex-wrap px-5">
            <!--begin::Links-->
            <div class="d-flex fw-semibold text-primary fs-base">
                <a href="#" class="px-5" target="_blank">Terminos y condiciones</a>
                <a href="#" class="px-5" target="_blank">Contactanos</a>
            </div>
            <!--end::Links-->
        </div>
        <!--end::Footer-->
    </div>
    <!--end::Body-->
    <!--begin::Aside-->
    <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2" >
        <!--begin::Content-->
        <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">

            <!--begin::Image-->
            <img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20"
                src="{{asset('assets/images/logo-blank.png')}}" alt="" />
            <!--end::Image-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Aside-->
</div>
<!--end::Authentication - Password reset-->
@endsection
