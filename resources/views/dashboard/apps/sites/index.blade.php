@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Sites'
])



<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Painel de Apps - Site</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist" style="line-height: 40px;">


                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{
                                !session()->has('aba') || session('aba') == 'site' ? 'active' : ''
                            }}"
                                id="primeiro-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#primeiro"
                                type="button"
                                role="tab"
                                aria-controls="primeiro"
                                aria-selected="true">Site
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{
                                session()->has('aba') && session('aba') == 'evento' ? 'active' : ''
                            }}"
                                id="segundo-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#segundo"
                                type="button"
                                role="tab"
                                aria-controls="segundo"
                                aria-selected="false">Evento
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{
                                session()->has('aba') && session('aba') == 'inscritos' ? 'active' : ''
                            }}"
                                id="terceiro-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#terceiro"
                                type="button"
                                role="tab"
                                aria-controls="terceiro"
                                aria-selected="false">Inscritos
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        @include('dashboard.apps.sites.tabs.site')
                        @include('dashboard.apps.sites.tabs.evento')
                        @include('dashboard.apps.sites.tabs.inscritos')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
