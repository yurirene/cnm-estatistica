@extends('layouts.app')

@section('content')
    @include('dashboard.partes.head', [
        'titulo' => 'Diretoria',
    ])

    <div class="container-fluid mt--7">
        <div class="row mt-5">
            <div class="col-xl-12 mb-5 mb-xl-0">
                <div class="card shadow p-3">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-pills" id="custom-tabs-four-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{
                                    !session('aba') || session()->get('aba') == 'diretoria' ? 'active' : ''
                                }}"
                                    id="custom-tabs-four-home-tab"
                                    data-toggle="pill"
                                    href="#custom-tabs-four-home"
                                    role="tab"
                                    aria-controls="custom-tabs-four-home"
                                    aria-selected="true">
                                    Diretoria
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ session()->get('aba') == 'secretarios' ? 'active' : '' }}"
                                    id="custom-tabs-four-profile-tab"
                                    data-toggle="pill"
                                    href="#custom-tabs-four-profile"
                                    role="tab"
                                    aria-controls="custom-tabs-four-profile"
                                    aria-selected="false">
                                    Secretários
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-four-tabContent">
                            <div
                                class="tab-pane fade  {{
                                    !session('aba') || session()->get('aba') == 'diretoria' ? 'show active' : ''
                                }}"
                                id="custom-tabs-four-home"
                                role="tabpanel"
                                aria-labelledby="custom-tabs-four-home-tab"
                            >
                                @include('dashboard.diretoria.tabs.diretoria')
                            </div>
                            <div
                                class="tab-pane fade {{ session()->get('aba') == 'secretarios' ? 'show active' : '' }}"
                                id="custom-tabs-four-profile"
                                role="tabpanel"
                                aria-labelledby="custom-tabs-four-profile-tab"
                            >
                                @include('dashboard.diretoria.tabs.secretarios')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
