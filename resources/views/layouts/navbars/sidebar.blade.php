<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0" href="{{ route('home') }}">
            <img src="/img/logo.png" class="navbar-brand-img" alt="..." style="max-height: 60px;">
        </a>
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                        <img alt="Image placeholder" src="{{ asset('argon') }}/img/theme/team-1-800x800.jpg">
                        </span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                   
                </div>
            </li>
        </ul>
        <!-- Collapse -->
        <div class="collapse navbar-collapse" id="sidenav-collapse-main">
            <!-- Collapse header -->
            <div class="navbar-collapse-header d-md-none">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('argon') }}/img/brand/blue.png">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#sidenav-collapse-main" aria-controls="sidenav-main" aria-expanded="false" aria-label="Toggle sidenav">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Form -->
            <form class="mt-4 mb-3 d-md-none">
                <div class="input-group input-group-rounded input-group-merge">
                    <input type="search" class="form-control form-control-rounded form-control-prepended" placeholder="{{ __('Search') }}" aria-label="Search">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-search"></span>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Navigation -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.home') }}">
                        <i class="fas fa-home"></i> Início
                    </a>
                </li>
                @canAtLeast(['dashboard.usuarios.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.usuarios.index') }}">
                        <i class="fas fa-users"></i> Usuários
                    </a>
                </li>
                @endCanAtLeast
                @canAtLeast(['dashboard.sinodais.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.sinodais.index') }}">
                        <i class="fas fa-user-plus"></i> Sinodais
                    </a>
                </li>
                @endCanAtLeast
                @canAtLeast(['dashboard.federacoes.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.federacoes.index') }}">
                        <i class="fas fa-user-plus"></i> Federação
                    </a>
                </li>
                @endCanAtLeast
                @canAtLeast(['dashboard.locais.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.locais.index') }}">
                        <i class="fas fa-user-plus"></i> UMP Local
                    </a>
                </li>
                @endCanAtLeast
                @canAtLeast(['dashboard.atividades.index'])                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.atividades.index') }}">
                        <i class="fas fa-calendar"></i> Atividades
                    </a>
                </li>
                @endCanAtLeast
                @canAtLeast(['dashboard.formularios-locais.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.formularios-locais.index') }}">
                        <i class="fas fa-file"></i> Formulário UMP Local
                    </a>
                </li>
                @endCanAtLeast


                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.formularios-sinodais.index') }}">
                        <i class="fas fa-file"></i> Formulário Sinodal
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.formularios-federacoes.index') }}">
                        <i class="fas fa-file"></i> Formulário Federação
                    </a>
                </li>





                @canAtLeast(['administrador'])
                <li class="nav-item">
                    <a class="nav-link pai" href="#cadastros" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="cadastros">
                        <i class="fas fa-user-plus" ></i>
                        <span class="nav-link-text" > Cadastros</span>
                    </a>

                    <div class="collapse" id="cadastros">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard.sinodais.index') }}">
                                    Sinodais
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard.federacoes.index') }}">
                                    Federações
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard.locais.index') }}">
                                    UMPs Locais
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link pai" href="#formularios" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="formularios">
                        <i class="fas fa-file-alt" ></i>
                        <span class="nav-link-text" > Formulários</span>
                    </a>

                    <div class="collapse" id="formularios">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard.formularios-locais.index') }}">
                                    Sinodais
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard.formularios-locais.index') }}">
                                    Federações
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard.formularios-locais.index') }}">
                                    UMPs Locais
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </li>
                @endCanAtLeast

            </ul>
            <hr class="my-3">

            {{-- <h6 class="navbar-heading text-muted">Formulário</h6> --}}
        </div>
    </div>
</nav>
