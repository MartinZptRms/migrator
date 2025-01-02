<div>
    {{-- In work, do what you enjoy. --}}
    <div class="card">
        <!--begin::Card body-->
        <div class="card-body">
            <!--begin::Stepper-->
            <div class="stepper stepper-links d-flex flex-column pt-5" id="service_setting_stepper">
                <!--begin::Nav-->
                <div class="stepper-nav">
                    <!--begin::Step 1-->
                    <div class="stepper-item {{$this->step == 1 ? 'current' : null}}" data-kt-stepper-element="nav">
                        <h3 class="stepper-title">Bases de Datos</h3>
                    </div>
                    <!--end::Step 1-->
                    <!--begin::Step 2-->
                    <div class="stepper-item {{$this->step == 2 ? 'current' : null}}" data-kt-stepper-element="nav">
                        <h3 class="stepper-title">Tablas</h3>
                    </div>
                    <!--end::Step 2-->
                    <!--begin::Step 3-->
                    <div class="stepper-item {{$this->step == 3 ? 'current' : null}}" data-kt-stepper-element="nav">
                        <h3 class="stepper-title">Integración</h3>
                    </div>
                    <!--end::Step 3-->
                </div>
                <!--end::Nav-->
                <!--begin::Form-->
                <form class="mx-auto w-100 pt-5 pb-5" novalidate="novalidate" id="service_setting_form">
                    <!--begin::Step 1-->
                    <div class="{{$this->step == 1 ? 'current' : null}}" data-kt-stepper-element="content">
                        <!--begin::Wrapper-->
                        <div class="w-100">
                            <!--begin::Heading-->
                            <div class="pb-10 pb-lg-15">
                                <!--begin::Title-->
                                <h2 class="fw-bold d-flex align-items-center text-dark">Selecciona tus bases de datos
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Billing is issued based on your selected account type"></i></h2>
                                <!--end::Title-->
                                <!--begin::Notice-->
                                <div class="text-muted fw-semibold fs-6">
                                    Selecciona primero tu BD de origen y después la de destino
                                </div>
                                <!--end::Notice-->
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            <div class="fv-row">
                                <!--begin::Row-->
                                <div class="row">
                                    @foreach ($databases as $d)
                                    <!--begin::Col-->
                                    <div class="col-lg-3">
                                        <!--begin::Option-->
                                        <input type="checkbox" class="btn-check" name="databases[]" value="{{$d->id}}" id="databases_{{$d->id}}" wire:model="inputDatabases"/>
                                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center mb-10" for="databases_{{$d->id}}">
                                            <span class="svg-icon svg-icon-3x me-5">
                                                <i class="fa-solid fa-database" style="font-size: 3rem;"></i>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <!--begin::Info-->
                                            <span class="d-block fw-semibold text-start">
                                                <span class="text-dark fw-bold d-block fs-4 mb-2">{{$d->name}}</span>
                                                <span class="text-muted fw-semibold fs-6">{{$d->size}}</span>
                                            </span>
                                            <!--end::Info-->
                                        </label>
                                        <!--end::Option-->
                                    </div>
                                    <!--end::Col-->
                                    @endforeach
                                </div>
                                <!--end::Row-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Step 1-->

                    <!--begin::Step 2-->
                    <div class="{{$this->step == 2 ? 'current' : null}}" data-kt-stepper-element="content">
                        <!--begin::Wrapper-->
                        <div class="w-100">
                            <!--begin::Heading-->
                            <div class="pb-5 pb-lg-15">
                                <!--begin::Title-->
                                <h2 class="fw-bold d-flex align-items-center text-dark">Selecciona tus tablas de origen
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Selecciona tus tablas de origen"></i></h2>
                                <!--end::Title-->
                                <!--begin::Notice-->
                                <div class="text-muted fw-semibold fs-6">
                                    Selecciona tus tablas de origen
                                </div>

                                <label class="form-check form-check-custom form-check-solid mt-4">
                                    <input class="form-check-input" type="checkbox" id="allTables" wire:model.live="allTables">
                                    <span class="form-check-label">
                                        Seleccionar todos
                                    </span>
                                </label>
                                <!--end::Notice-->
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            <div class="fv-row">
                                <!--begin::Row-->
                                <div class="row">
                                    @foreach ($tables as $t)
                                    <!--begin::Col-->
                                    <div class="col-lg-3">
                                        <!--begin::Option-->
                                        <input type="checkbox" class="btn-check" name="tables[]" value="{{$t->id}}" id="tables_{{$t->id}}" wire:model.live="inputTables"/>
                                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center mb-5" for="tables_{{$t->id}}">
                                            <span class="svg-icon svg-icon-3x me-5">
                                                <i class="fa-solid fa-table" style="font-size: 3rem;"></i>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <!--begin::Info-->
                                            <span class="d-block fw-semibold text-start">
                                                <span class="text-dark fw-bold d-block fs-6 mb-2">{{$t->name}}</span>
                                                <span class="text-muted fw-semibold fs-8">{{$t->size}}</span>
                                            </span>
                                            <!--end::Info-->
                                        </label>
                                        <!--end::Option-->
                                    </div>
                                    <!--end::Col-->
                                    @endforeach
                                </div>
                                <!--end::Row-->
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Step 2-->

                    <!--begin::Step 3-->
                    <div class="{{$this->step == 3 ? 'current' : null}}" data-kt-stepper-element="content">
                        <!--begin::Wrapper-->
                        <div class="w-100">
                            <!--begin::Heading-->
                            <div class="pb-5 pb-lg-5">
                                <div class="row">
                                    <div class="col-2">
                                        <!--begin::Title-->
                                        <h2 class="fw-bold d-flex align-items-center text-dark">
                                            Integración <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Integración"></i>
                                        </h2>
                                        <!--end::Title-->
                                        <!--begin::Notice-->
                                        <div class="text-muted fw-semibold fs-6">
                                            Realiza tu configuración
                                        </div>
                                        <!--end::Notice-->
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-sm btn-primary" wire:click="equalTo">
                                            <i class="fa-solid fa-equals"></i> Espejo
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            <div class="row">
                                <div class="col-12 col-lg-3">
                                    <!--begin::User menu-->
                                    <div class="card mb-5 mb-xl-8">
                                        <div class="card-header border-0 p-0 min-h-auto">
                                            <h3 class="card-title">Tablas</h3>
                                        </div>
                                        <!--begin::Body-->
                                        <div class="card-body pt-0 px-0">
                                            <!--begin::Navbar-->
                                            <div class="m-0">
                                                <!--begin::Navs-->
                                                <ul class="nav nav-pills nav-pills-custom flex-column border-transparent fs-7 fw-bold">
                                                    <!--begin::Nav item-->
                                                    @foreach($serviceSourceTables as $t)
                                                    <li class="nav-item mt-2">
                                                        <button type="button" class="nav-link text-muted text-active-primary ms-0 py-0 me-4 ps-2 border-0 {{$selectedServiceSourceTable?->id == $t->id ? 'active' : null}}"
                                                                wire:click="changeSourceTable({{$t->id}})">
                                                            <span class="svg-icon svg-icon-3 svg-icon-muted">
                                                                <i class="fa-solid fa-table"></i>
                                                            </span>
                                                            {{$t->table->name}}
                                                            <span class="bullet-custom position-absolute start-0 top-0 w-3px h-100 bg-primary rounded-end"></span>
                                                        </button>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                <!--begin::Navs-->
                                            </div>
                                            <!--end::Navbar-->
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::User menu-->
                                </div>

                                <div class="col-12 col-lg-3">
                                    <!--begin::User menu-->
                                    <div class="card mb-5 mb-xl-8">
                                        <div class="card-header border-0 p-0 min-h-auto">
                                            <h3 class="card-title">Columnas</h3>
                                        </div>
                                        <!--begin::Body-->
                                        <div class="card-body pt-0 px-0">
                                            <!--begin::Navbar-->
                                            <div class="m-0">
                                                <!--begin::Navs-->
                                                <ul class="nav nav-pills nav-pills-custom flex-column border-transparent fs-5 fw-bold">
                                                    <!--begin::Nav item-->
                                                    @foreach($serviceSourceColumns as $ssc)
                                                    <li class="nav-item mt-2">
                                                        <button class="d-inline nav-link text-muted text-active-primary ms-0 py-0 me-4 ps-9 border-0 {{$selectedSourceColumn?->id == $ssc->id ? 'active' : null}}"
                                                            wire:click="changeSourceColumn({{$ssc->id}})" type="button">
                                                            <span class="svg-icon svg-icon-3 svg-icon-muted me-3">
                                                                <i class="fa-solid fa-table-cells"></i>
                                                            </span>
                                                            {{$ssc->name}}
                                                            <span class="bullet-custom position-absolute start-0 top-0 w-3px h-100 bg-primary rounded-end"></span>
                                                        </button>
                                                        @if($selectedSourceColumn?->id == $ssc->id)
                                                        <button class="d-inline btn btn-sm btn-light-success py-0 px-4" wire:click="equalToColumn">
                                                            <i class="fa-solid fa-arrow-right"></i>
                                                        </button>
                                                        @endif
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                <!--begin::Navs-->
                                            </div>
                                            <!--end::Navbar-->
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::User menu-->
                                </div>

                                <div class="col-12 col-lg-3 d-flex flex-row-reverse">
                                    <!--begin::User menu-->
                                    <div class="card mb-5 mb-xl-8">
                                        <div class="card-header border-0 p-0 min-h-auto justify-content-end">
                                            <h3 class="card-title text-end">Columnas</h3>
                                        </div>
                                        <!--begin::Body-->
                                        <div class="card-body pt-0 px-0">
                                            <!--begin::Navbar-->
                                            <div class="m-0">
                                                <!--begin::Navs-->
                                                <ul class="nav nav-pills nav-pills-custom flex-column border-transparent fs-5 fw-bold justify-content-end">
                                                    <!--begin::Nav item-->
                                                    @foreach($serviceTargetColumns as $tc)
                                                    <li class="nav-item ms-auto me-0 mt-2">
                                                       <button type="button" class="nav-link text-muted text-active-primary me-0 py-0 ms-10 pe-9 border-0 text-end {{$selectedServiceTargetColumn?->id == $tc->id ? 'active' : null}}"
                                                                wire:click="changeTargetColumn({{$tc->id}})">
                                                            {{$tc?->custom_column?->name ?? $tc->column->name}}
                                                            <span class="svg-icon svg-icon-3 svg-icon-muted me-3">
                                                                <i class="fa-solid fa-table"></i>
                                                            </span>
                                                            <span class="bullet-custom position-absolute end-0 top-0 w-3px h-100 bg-primary rounded-end"></span>
                                                        </button>
                                                    </li>
                                                    @endforeach
                                                    @if($selectedSourceColumn)
                                                    <li class="nav-item ms-auto me-0 mt-2">
                                                        <div class="d-flex align-items-center collapsible py-0 toggle mb-0 active">
                                                            <div class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-2" wire:click="addCustomColumn">
                                                                <span class="svg-icon svg-icon-success svg-icon-1">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor"></rect>
                                                                        <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="currentColor"></rect>
                                                                        <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            <input type="text" class="form-control form-control-sm" wire:model="customColumn">
                                                        </div>
                                                    </li>
                                                    @endif
                                                </ul>
                                                <!--begin::Navs-->
                                            </div>
                                            <!--end::Navbar-->
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::User menu-->
                                </div>

                                <div class="col-12 col-lg-3 d-flex flex-row-reverse">
                                    <!--begin::User menu-->
                                    <div class="card mb-5 mb-xl-8">
                                        <div class="card-header border-0 p-0 min-h-auto justify-content-end">
                                            <h3 class="card-title text-end">Tablas</h3>
                                        </div>
                                        <!--begin::Body-->
                                        <div class="card-body pt-0 px-0">
                                            <!--begin::Navbar-->
                                            <div class="m-0">
                                                <!--begin::Navs-->
                                                <ul class="nav nav-pills nav-pills-custom flex-column border-transparent fs-5 fw-bold justify-content-end">
                                                    <!--begin::Nav item-->
                                                    @foreach($serviceTargetTables as $t)
                                                    <li class="nav-item ms-auto me-0 mt-2">
                                                        <button type="button" class="nav-link text-muted text-active-primary me-0 py-0 ms-10 pe-9 border-0 text-end {{$selectedServiceTargetTable?->id == $t->id ? 'active' : null}}"
                                                                wire:click="changeTargetTable({{$t->id}})">
                                                            {{$t?->table?->name}}
                                                            <span class="svg-icon svg-icon-3 svg-icon-muted me-3">
                                                                <i class="fa-solid fa-table"></i>
                                                            </span>
                                                            <span class="bullet-custom position-absolute end-0 top-0 w-3px h-100 bg-primary rounded-end"></span>
                                                        </button>
                                                    </li>
                                                    @endforeach
                                                    <li class="nav-item ms-auto me-0 mt-2">
                                                        <div class="d-flex align-items-center collapsible py-0 toggle mb-0 active">
                                                            <div class="btn btn-sm btn-icon mw-20px btn-active-color-primary me-2" wire:click="addCustomTable">
                                                                <span class="svg-icon svg-icon-success svg-icon-1">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor"></rect>
                                                                        <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="currentColor"></rect>
                                                                        <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                            <input type="text" class="form-control form-control-sm" wire:model="customTable">
                                                        </div>
                                                    </li>
                                                </ul>
                                                <!--begin::Navs-->
                                            </div>
                                            <!--end::Navbar-->
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::User menu-->
                                </div>
                                <div class="col-12 col-lg-12 row">
                                    <h3 class="fw-bold d-flex align-items-center text-dark">
                                        Scripts
                                        {{-- <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Integración"></i> --}}
                                    </h3>
                                    <div class="text-muted fw-semibold fs-6 mb-4">
                                        Estos scripts se ejecutarán por tabla al extraer la información
                                    </div>

                                    <div class="col-12 col-lg-2">
                                        <label for="clause" class="form-label required pb-0 mb-0">Cláusula</label>
                                        <input type="text" class="form-control" name="clause" id="clause" placeholder="Cláusula" wire:model="clause">
                                    </div>
                                    <div class="col-12 col-lg-9 row">
                                        <label for="field" class="form-label required ps-0 pb-0 mb-0">Condición</label>
                                        <div class="col m-0 ps-0">
                                            <input type="text" class="form-control" name="field" id="field" placeholder="Campo" wire:model="field">
                                        </div>
                                        <div class="col-2 m-0 ps-1">
                                            <input type="text" class="form-control" name="operator" id="operator" placeholder="Operador" wire:model="operator">
                                        </div>
                                        <div class="col m-0 ps-1">
                                            <input type="text" class="form-control" name="value" id="value" placeholder="Valor" wire:model="value">
                                        </div>
                                    </div>
                                    <div class="col-12 col-lg-1 d-flex align-items-end">
                                        <div class="btn btn-md btn-icon mw-20px btn-active-color-primary me-2" wire:click="addClause">
                                            <span class="svg-icon svg-icon-success svg-icon-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor"></rect>
                                                    <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="currentColor"></rect>
                                                    <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor"></rect>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    @foreach ($serviceSourceClauses as $ssc)
                                    <div class="col-12 col-lg-2 text-center mt-4">
                                        <span class="text-muted  fs-5">{{$ssc->clause}}</span>
                                    </div>
                                    <div class="col-12 col-lg-9 text-center mt-4 row">
                                        <div class="col m-0 ps-0">
                                            <span class="text-muted text-center fs-5">{{$ssc->field}}</span>
                                        </div>
                                        <div class="col-2 m-0 ps-1">
                                            <span class="text-muted text-center fs-5">{{$ssc->operator}}</span>
                                        </div>
                                        <div class="col m-0 ps-1">
                                            <span class="text-muted text-center fs-5">{{$ssc->value}}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                            </div>
                            <!--end::row-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Step 3-->

                    <!--begin::Actions-->
                    <div class="d-flex flex-stack pt-15">
                        <div class="mr-2">
                            <button type="button" class="btn btn-lg btn-light-primary me-3" data-kt-stepper-action="previous">
                                <span class="svg-icon svg-icon-4 me-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="11" width="13" height="2" rx="1" fill="currentColor" />
                                        <path d="M8.56569 11.4343L12.75 7.25C13.1642 6.83579 13.1642 6.16421 12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75L5.70711 11.2929C5.31658 11.6834 5.31658 12.3166 5.70711 12.7071L11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25C13.1642 17.8358 13.1642 17.1642 12.75 16.75L8.56569 12.5657C8.25327 12.2533 8.25327 11.7467 8.56569 11.4343Z" fill="currentColor" />
                                    </svg>
                                </span>
                                Back
                            </button>
                        </div>
                        <div>
                            <button type="button" class="btn btn-lg btn-primary me-3" data-kt-stepper-action="submit">
                                <span class="indicator-label">Submit
                                    <span class="svg-icon svg-icon-3 ms-2 me-0">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                            <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                </span>
                                <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                            <button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="next" wire:click="stepNext">Continue
                                <span class="svg-icon svg-icon-4 ms-1 me-0">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                        <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Stepper-->
        </div>
        <!--end::Card body-->
    </div>
</div>
