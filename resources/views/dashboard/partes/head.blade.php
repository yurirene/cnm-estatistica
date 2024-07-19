<div class="header bg-gradient-primary {{!isset($remover) ? 'pb-6' : ''}} pt-5 pt-md-8">
    <div class="container-fluid">
        <h3 class="display-3 text-white">{{$titulo ?? ''}}</h3>
        <h2 class=" text-white">{{$subtitulo ?? ''}}</h2>
        <div class="header-body">
            @if(isset($botaoRetorno))

            <a class="btn btn-outline-white" href="{{$botaoRetorno}}">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            @endif
            <!-- Card stats -->

        </div>
    </div>
</div>
