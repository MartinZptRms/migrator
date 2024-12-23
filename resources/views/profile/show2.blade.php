@extends('layouts.master')
@section('content')
<div class="row">
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-wrap align-items-center my-1">
            <h3 class="fw-bolder fs-3 me-5 my-1">Perfil</h3>
        </div>
    </div>
    <div class="col-12">
        <div class="card mb-5 mb-xxl-8">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap">
                    <div class="me-7 mb-4">
                        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                            <img src=" {{asset('assets/media/avatars/blank.png')}}" alt="image">
                            <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{auth()->user()->full_name}}</a>
                                    <a href="#">
                                        <span class="svg-icon svg-icon-1 svg-icon-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                                <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="currentColor"></path>
                                                <path d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"></path>
                                            </svg>
                                        </span>
                                    </a>
                                </div>
                                <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                    <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor"/>
                                            <path d="M12.0006 11.1542C13.1434 11.1542 14.0777 10.22 14.0777 9.0771C14.0777 7.93424 13.1434 7 12.0006 7C10.8577 7 9.92348 7.93424 9.92348 9.0771C9.92348 10.22 10.8577 11.1542 12.0006 11.1542Z" fill="currentColor"/>
                                            <path d="M15.5652 13.814C15.5108 13.6779 15.4382 13.551 15.3566 13.4331C14.9393 12.8163 14.2954 12.4081 13.5697 12.3083C13.479 12.2993 13.3793 12.3174 13.3067 12.3718C12.9257 12.653 12.4722 12.7981 12.0006 12.7981C11.5289 12.7981 11.0754 12.653 10.6944 12.3718C10.6219 12.3174 10.5221 12.2902 10.4314 12.3083C9.70578 12.4081 9.05272 12.8163 8.64456 13.4331C8.56293 13.551 8.49036 13.687 8.43595 13.814C8.40875 13.8684 8.41781 13.9319 8.44502 13.9864C8.51759 14.1133 8.60828 14.2403 8.68991 14.3492C8.81689 14.5215 8.95295 14.6757 9.10715 14.8208C9.23413 14.9478 9.37925 15.0657 9.52439 15.1836C10.2409 15.7188 11.1026 15.9999 11.9915 15.9999C12.8804 15.9999 13.7421 15.7188 14.4586 15.1836C14.6038 15.0748 14.7489 14.9478 14.8759 14.8208C15.021 14.6757 15.1661 14.5215 15.2931 14.3492C15.3838 14.2312 15.4655 14.1133 15.538 13.9864C15.5833 13.9319 15.5924 13.8684 15.5652 13.814Z" fill="currentColor"/>
                                        </svg>                                        
                                        Developer
                                    </a>
                                    
                                    <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z" fill="currentColor"></path>
                                                <path d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        {{auth()->user()->email}}
                                    </a>
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::User-->
                        </div>
                        <div class="d-flex flex-wrap flex-stack">
                            <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3">
                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                    <span class="fw-semibold fs-6 text-gray-400">Perfil completado</span>
                                    <span class="fw-bold fs-6">50%</span>
                                </div>
                                <div class="h-5px mx-3 w-100 bg-light mb-3">
                                    <div class="bg-success rounded h-5px" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5 active" href="../../demo1/dist/pages/user-profile/overview.html">Perfil</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-xl-6">
        <div class="card mb-5 mb-xxl-8">
            <div class="card-header cursor-pointer">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Información general</h3>
                </div>
            </div>
            <div class="card-body pt-0 pb-0">
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                    @endif
                    <div class="row">
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="name" class="form-label fs-6 fw-bold">Nombre(s)</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Nombre" id="name" name="name" value="{{auth()->user()->name}}" disabled/>
                                    @error('name')
                                        <label for="name" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="lastname" class="form-label fs-6 fw-bold">Apellido(s)</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Nombre" id="lastname" name="lastname" value="{{auth()->user()->lastname}}" disabled/>
                                    @error('lastname')
                                        <label for="lastname" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="email" class="form-label fs-6 fw-bold">Correo electrónico</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Correo electrónico" id="email" name="email" value="{{auth()->user()->email}}" disabled/>
                                    @error('email')
                                        <label for="email" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="secondary_email" class="form-label fs-6 fw-bold">Correo electrónico secundario</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Correo electrónico" id="secondary_email" name="secondary_email" value="{{auth()->user()->secondary_email}}" disabled/>
                                    @error('secondary_email')
                                        <label for="secondary_email" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="mobile_phone" class="form-label fs-6 fw-bold">Télefono móvil</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Télefono móvil" id="mobile_phone" name="mobile_phone" value="{{auth()->user()->mobile_phone}}" disabled/>
                                    @error('mobile_phone')
                                        <label for="mobile_phone" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="office_phone" class="form-label fs-6 fw-bold">Télefono secundario</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Télefono móvil" id="office_phone" name="office_phone" value="{{auth()->user()->office_phone}}" disabled/>
                                    @error('office_phone')
                                        <label for="office_phone" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card mb-5 mb-xxl-8">
            <div class="card-header cursor-pointer">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Información general</h3>
                </div>
            </div>
            <div class="card-body pt-0 pb-0">
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                    @endif
                    <div class="row">
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="name" class="form-label fs-6 fw-bold">Nombre(s)</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Nombre" id="name" name="name" value="{{auth()->user()->name}}" disabled/>
                                    @error('name')
                                        <label for="name" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="lastname" class="form-label fs-6 fw-bold">Apellido(s)</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Nombre" id="lastname" name="lastname" value="{{auth()->user()->lastname}}" disabled/>
                                    @error('lastname')
                                        <label for="lastname" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="email" class="form-label fs-6 fw-bold">Correo electrónico</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Correo electrónico" id="email" name="email" value="{{auth()->user()->email}}" disabled/>
                                    @error('email')
                                        <label for="email" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="secondary_email" class="form-label fs-6 fw-bold">Correo electrónico secundario</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Correo electrónico" id="secondary_email" name="secondary_email" value="{{auth()->user()->secondary_email}}" disabled/>
                                    @error('secondary_email')
                                        <label for="secondary_email" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="mobile_phone" class="form-label fs-6 fw-bold">Télefono móvil</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Télefono móvil" id="mobile_phone" name="mobile_phone" value="{{auth()->user()->mobile_phone}}" disabled/>
                                    @error('mobile_phone')
                                        <label for="mobile_phone" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl-6">
                            <div class="row mb-8">
                                <div class="col-xl-12">
                                    <div class="d-flex justify-content-between align-items-end">
                                        <label for="office_phone" class="form-label fs-6 fw-bold">Télefono secundario</label>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <input type="text" class="form-control" placeholder="Télefono móvil" id="office_phone" name="office_phone" value="{{auth()->user()->office_phone}}" disabled/>
                                    @error('office_phone')
                                        <label for="office_phone" class="text-danger">{{$message}}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                @livewire('profile.update-profile-information-form')

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.update-password-form')
                </div>

                <x-section-border />
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="mt-10 sm:mt-0">
                    @livewire('profile.two-factor-authentication-form')
                </div>

                <x-section-border />
            @endif

            <div class="mt-10 sm:mt-0">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <x-section-border />

                <div class="mt-10 sm:mt-0">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-app-layout> --}}
