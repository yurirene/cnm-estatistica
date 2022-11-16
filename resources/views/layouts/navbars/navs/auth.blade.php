<!-- Top navbar -->
<nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
    <div class="container-fluid">
        <!-- Brand -->
        <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="{{ route('home') }}">{{ __('Dashboard') }}</a>
        <!-- Form -->
        <!-- User -->
        <ul class="navbar-nav align-items-center d-none d-md-flex">
            <li class="nav-item dropdown">
                <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="media align-items-center">
                        <span class="avatar avatar-sm rounded-circle">
                            <img alt="Image placeholder" src="/img/azul.jpg">
                        </span>
                        <div class="media-body ml-2 d-none d-lg-block">
                            <span class="mb-0 text-sm  font-weight-bold">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
                    <a href="#"  data-toggle="modal" data-target="#trocar-senha" class="dropdown-item">
                        <i class="ni ni-settings-gear-65"></i>
                        <span>Trocar Senha</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item" onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                        <i class="ni ni-user-run"></i>
                        <span>{{ __('Logout') }}</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<div class="modal fade" id="trocar-senha" tabindex="-1" aria-labelledby="trocar-senhaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trocar-senhaLabel">Trocar Senha</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'route' => 'dashboard.trocar-senha', 'class' => 'form-horizontal']) !!}
            <div class="modal-body">
                <div class="form-group{{ $errors->has('antiga_senha') ? ' has-error' : '' }}">
                    {!! Form::label('antiga_senha', 'Senha Antiga', ['class' => 'control-label']) !!}
                    {!! Form::password('antiga_senha', ['class' => 'form-control', 'required' => 'required', 'autocomplete' => 'off']) !!}
                    <small class="text-danger">{{ $errors->first('antiga_senha') }}</small>
                </div>
                <div class="form-group{{ $errors->has('nova_senha') ? ' has-error' : '' }}">
                {!! Form::label('nova_senha', 'Nova Senha', ['class' => 'control-label']) !!}
                {!! Form::password('nova_senha', ['class' => 'form-control', 'required' => 'required', 'autocomplete' => 'off']) !!}
                <small class="text-danger">{{ $errors->first('nova_senha') }}</small>
                
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>