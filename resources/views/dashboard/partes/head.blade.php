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
            @if (isset($url_tutorial))
            <button class="btn btn-outline-white" data-bs-toggle="modal" data-bs-target="#modal-tutorial">
                <i class="fas fa-question-circle"></i> Tutorial
            </button>
            @endif
            <!-- Card stats -->

        </div>
    </div>
</div>
@if (isset($url_tutorial))
<div class="modal fade" id="modal-tutorial" tabindex="-1" role="dialog" aria-labelledby="modal-tutorial" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal-title-tutorial">Tutorial</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <iframe
                    width="100%"
                    height="400"
                    src="{{$url_tutorial}}"
                    title="TUTORIAL"
                    frameborder="0"
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen
                ></iframe>
                
            </div>
        </div>
    </div>
</div>
@endif