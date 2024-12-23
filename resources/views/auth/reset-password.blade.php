@extends('layouts.master_auth')

@section('content')
<!--begin::Authentication - New password -->
<div class="d-flex flex-column flex-lg-row flex-column-fluid">
    <!--begin::Body-->
    <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
        <!--begin::Form-->
        <div class="d-flex flex-center flex-column flex-lg-row-fluid">
            <!--begin::Wrapper-->
            <div class="w-lg-500px p-10">
                <!--begin::Form-->
                <form class="form w-100" action="{{route('password.update')}}" method="POST">
                    @csrf
                    <input type="hidden" value="{{$request->token}}" name="token">
                    <input type="hidden" value="{{$request->email}}" name="email">
                    <!--begin::Heading-->
                    <div class="text-center mb-10">
                        <!--begin::Title-->
                        <h1 class="text-white fw-bolder mb-3">Configura tu nueva contraseña</h1>
                        <!--end::Title-->
                        <!--begin::Link-->
                        <div class="text-gray-200 fw-semibold fs-6">Ya tienes tu contraseña ?
                        <a href="{{route('login')}}" class="link-primary fw-bold">Inciar sesión</a></div>
                        <!--end::Link-->
                    </div>
                    <!--begin::Heading-->
                    <!--begin::Input group-->
                    <div class="fv-row mb-8" data-kt-password-meter="true">
                        <!--begin::Wrapper-->
                        <div class="mb-1">
                            <!--begin::Input wrapper-->
                            <div class="position-relative mb-3">
                                <input class="form-control bg-white" type="password" placeholder="Password" name="password" autocomplete="off" />
                                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                    <i class="bi bi-eye-slash fs-2"></i>
                                    <i class="bi bi-eye fs-2 d-none"></i>
                                </span>
                            </div>
                            <!--end::Input wrapper-->
                            <!--begin::Meter-->
                            <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                                <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                            </div>
                            <!--end::Meter-->
                        </div>
                        <!--end::Wrapper-->
                        <!--begin::Hint-->
                        <div class="text-muted">Se recomienda usar 8 caracteres, utilizando letras, números y simbolos.</div>
                        <!--end::Hint-->
                    </div>
                    <!--end::Input group=-->
                    <!--end::Input group=-->
                    <div class="fv-row mb-8">
                        <!--begin::Repeat Password-->
                        <input type="password" placeholder="Repeat Password" name="password_confirmation" autocomplete="off" class="form-control bg-white" />
                        <!--end::Repeat Password-->
                    </div>
                    @foreach ($errors->all() as $e)
                    <div class="fv-plugins-message-container invalid-feedback mb-4">
                        <div>{{$e}}</div>
                    </div>
                    @endforeach
                    <!--end::Input group=-->

                    <!--begin::Action-->
                    <div class="d-grid mb-10">
                        <button type="submit" class="btn btn-primary">
                            Enviar
                        </button>
                    </div>
                    <!--end::Action-->
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
                {{-- <a href="../../demo1/dist/pages/team.html" class="px-5" target="_blank">Terms</a> --}}
                {{-- <a href="../../demo1/dist/pages/contact.html" class="px-5" target="_blank">Contact Us</a> --}}
            </div>
            <!--end::Links-->
        </div>
        <!--end::Footer-->
    </div>
    <!--end::Body-->
    <!--begin::Aside-->
    <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2">
        <!--begin::Content-->
        <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
            <!--begin::Logo-->
            <a href="../../demo1/dist/index.html" class="mb-0 mb-lg-12">
                <img alt="Logo" src="{{asset('assets/images/logo-blank.png')}}" class="h-60px h-lg-75px" />
            </a>
            <!--end::Logo-->
            <!--begin::Image-->
            <img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20" src="{{asset('assets/media/misc/auth-screens-collage.svg')}}" alt="" />
            <!--end::Image-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Aside-->
</div>
<!--end::Authentication - New password-->
@endsection
