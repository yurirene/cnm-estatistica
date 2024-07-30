<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
    <div class="container-fluid">
        <!-- Toggler -->
        <button
            class="navbar-toggler"
            type="button"
            data-toggle="collapse"
            data-target="#sidenav-collapse-main"
            aria-controls="sidenav-main"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Brand -->
        <a class="navbar-brand pt-0 pb-0" href="{{ route('home') }}">
            <img src="/img/logos/logo.png" class="navbar-brand-img" alt="..." style="max-height: 90px;">
        </a>
        <!-- User -->
        <ul class="nav align-items-center d-md-none">
            <li class="nav-item dropdown">
                <a
                    class="nav-link"
                    href="#"
                    role="button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                            <i class="fas fa-user-circle mr-1"></i>
                        </span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                    <a href="#"  data-toggle="modal" data-target="#trocar-senha" class="dropdown-item">
                        <i class="ni ni-settings-gear-65"></i>
                        <span>Trocar Senha</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <button class="dropdown-item" onclick="document.getElementById('logout-form').submit();">
                        <i class="ni ni-user-run"></i>
                        <span>{{ __('Logout') }}</span>
                    </button>
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
                            <img src="/img/logos/logo.png" class="navbar-brand-img" alt="..." style="max-height: 60px;">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button
                            type="button"
                            class="navbar-toggler"
                            data-toggle="collapse"
                            data-target="#sidenav-collapse-main"
                            aria-controls="sidenav-main"
                            aria-expanded="false"
                            aria-label="Toggle sidenav"
                        >
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.home') }}">
                        <i class="fas fa-home"></i> Início
                    </a>
                </li>

                @canAtLeast(['dashboard.diretoria.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.diretoria.index') }}">
                        <i class="fas fa-bullhorn"></i> Diretoria
                    </a>
                </li>
                @endCanAtLeast

                @canAtLeast(['dashboard.usuarios.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.usuarios.index') }}">
                        <i class="fas fa-users"></i> Usuários
                    </a>
                </li>
                @endCanAtLeast

                @canAtLeast(['dashboard.diretoria-sinodal.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.diretoria-sinodal.index') }}">
                        <i class="fas fa-users"></i> Diretoria
                    </a>
                </li>

                @endCanAtLeast


                @canAtLeast(['dashboard.sinodais.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.sinodais.index') }}">
                        <i class="fas fa-object-group"></i> Sinodais
                    </a>
                </li>
                @endCanAtLeast
                @canAtLeast(['dashboard.federacoes.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.federacoes.index') }}">
                        <i class="fas fa-layer-group"></i> Federações
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.formularios-sinodais.index') }}">
                        <i class="fas fa-file"></i> Formulário Estatístico
                    </a>
                </li>
                @endCanAtLeast


                @canAtLeast(['dashboard.comprovante-aci.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.comprovante-aci.index') }}">
                        <i class="fas fa-file-invoice-dollar"></i> Comprovante de ACI
                    </a>
                </li>
                @endCanAtLeast

                @canAtLeast(['dashboard.pesquisas.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.pesquisas.index') }}">
                        <i class="fas fa-question"></i> Formulário de Pesquisa
                    </a>
                </li>
                @endCanAtLeast

                @canAtLeast(['dashboard.locais.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.locais.index') }}">
                        <i class="fas fa-church"></i> UMP Local
                    </a>
                </li>

                @endCanAtLeast
                {{-- @canAtLeast(['dashboard.atividades.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.atividades.index') }}">
                        <i class="fas fa-calendar"></i> Atividades
                    </a>
                </li>
                @endCanAtLeast --}}
                @canAtLeast(['dashboard.estatistica.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.estatistica.index') }}">
                        <i class="fas fa-chart-line"></i> Estatística
                    </a>
                </li>
                @endCanAtLeast
                @canAtLeast(['dashboard.comissao-executiva.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.comissao-executiva.index') }}">
                        <i class="fas fa-gavel"></i> Comissão Executiva
                    </a>
                </li>
                @endCanAtLeast
                @canAtLeast(['dashboard.ce-sinodal.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.ce-sinodal.index') }}">
                        <i class="fas fa-gavel"></i> Comissão Executiva
                    </a>
                </li>
                @endCanAtLeast

                @canAtLeast(['dashboard.produtos.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.produtos.index') }}">
                        <i class="fas fa-store"></i> Produtos
                    </a>
                </li>
                @endCanAtLeast

                @canAtLeast(['dashboard.demandas.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.demandas.index') }}">
                        <i class="fas fa-project-diagram"></i> Demandas
                    </a>
                </li>
                @endCanAtLeast
                @canAtLeast(['dashboard.minhas-demandas.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.minhas-demandas.index') }}">
                        <i class="fas fa-project-diagram"></i> Minhas Demandas
                    </a>
                </li>
                @endCanAtLeast
                @canAtLeast(['dashboard.digestos.index'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.digestos.index') }}">
                        <i class="fas fa-file-alt"></i> Digestos
                    </a>
                </li>
                @endCanAtLeast

                @canAtLeast(['dashboard.formularios-locais.index'])

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.formularios-locais.index') }}">
                        <i class="fas fa-file"></i> Formulário Estatístico
                    </a>
                </li>
                @endCanAtLeast
                @canAtLeast(['dashboard.formularios-federacoes.index'])

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.formularios-federacoes.index') }}">
                        <i class="fas fa-file"></i> Formulário Estatístico
                    </a>
                </li>
                @endCanAtLeast

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.tutoriais.index') }}">
                        <i class="fas fa-video"></i> Tutoriais
                    </a>
                </li>


                @canAtLeast(['dashboard.apps.liberacao'])

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.apps.liberacao') }}">
                        <i class="fas fa-key"></i> Liberar Apps
                    </a>
                </li>
                @endCanAtLeast

                @canAtLeast(['dashboard.avisos.index'])

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.avisos.index') }}">
                        <i class="fas fa-bullhorn"></i> Avisos
                    </a>
                </li>
                @endCanAtLeast

                @can('apps', [])
                <a class="nav-link"
                    href="#meusapps"
                    data-toggle="collapse"
                    aria-expanded="false"
                >
                    <i class="fas fa-tablet-alt"></i> Meus Apps
                </a>
                <ul class="collapse list-unstyled" id="meusapps" >
                    @can('apps', 'sites')
                    <li class="nav-item ml-3">
                        <a  class="nav-link" href="{{ route('dashboard.apps.sites.index') }}">
                            <i class="fab fa-chrome"></i>
                            Site
                        </a>
                    </li>
                    @endcan
                    @can('apps', 'tesouraria')
                    <li class="nav-item ml-3">
                        <a  class="nav-link" href="{{ route('dashboard.apps.tesouraria.index') }}">
                            <i class="fas fa-dollar-sign"></i>
                            Tesouraria
                        </a>
                    </li>
                    @endcan
                </ul>
                @endcan

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard.helpdesk.index') }}">
                        <i class="fas fa-exclamation-triangle"></i>
                        Problemas e Sugestões
                    </a>
                </li>
            </ul>
            <hr class="my-3">

            {{-- <h6 class="navbar-heading text-muted">Formulário</h6> --}}
        </div>
    </div>
</nav>
