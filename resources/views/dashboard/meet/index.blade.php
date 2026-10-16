@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Sala Meet',
    'subtitulo' => 'Reuniões',
])

<div class="container-fluid mt--7">
    <div class="row mt-4">
        <div class="col-xl-5">
            <div class="card shadow">
                <div class="card-header border-0">
                    <h3 class="mb-0">Nova sala</h3>
                </div>
                <div class="card-body">
                    @if(!empty($meetNaoConfigurado))
                    <p class="text-muted mb-0">A conta de serviço e o e-mail organizador ainda não foram configurados no sistema.</p>
                    @else
                    <form method="POST" action="{{ route('dashboard.meet.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="titulo">Título</label>
                            <input id="titulo" name="titulo" type="text" maxlength="150" class="form-control" value="{{ old('titulo') }}" required>
                            @error('titulo')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="para_user_id">Para o usuário</label>
                            <select id="para_user_id" name="para_user_id" class="form-control" required>
                                @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}" @selected(old('para_user_id', auth()->id()) == $usuario->id)>
                                    {{ $usuario->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('para_user_id')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="inicia_em">Início</label>
                            <input id="inicia_em" name="inicia_em" type="datetime-local" class="form-control" value="{{ old('inicia_em') }}" required>
                            @error('inicia_em')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="termina_em">Fim</label>
                            <input id="termina_em" name="termina_em" type="datetime-local" class="form-control" value="{{ old('termina_em') }}" required>
                            @error('termina_em')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Gerar link</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="card shadow">
                <div class="card-header border-0">
                    <h3 class="mb-0">Calendário</h3>
                </div>
                <div class="card-body">
                    <div id="calendario-meet"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-12 mb-4">
            <div class="card shadow">
                <div class="card-header border-0">
                    <h3 class="mb-0">Solicitações</h3>
                </div>
                <div class="card-body">
                    @if($salas->isEmpty())
                    <p class="text-muted mb-0">Nenhuma sala foi solicitada ainda.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Título</th>
                                    <th>Solicitante</th>
                                    <th>Para</th>
                                    <th>Início</th>
                                    <th>Fim</th>
                                    <th>Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salas as $sala)
                                <tr>
                                    <td>{{ $sala->titulo }}</td>
                                    <td>{{ $sala->solicitante->name ?? '—' }}</td>
                                    <td>{{ $sala->para->name ?? '—' }}</td>
                                    <td>{{ $sala->inicia_em?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                    <td>{{ $sala->termina_em?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ $sala->meet_uri }}" target="_blank" rel="noopener">Abrir</a>
                                        <button type="button" class="btn btn-sm btn-secondary ml-2 js-copiar-meet" data-link="{{ $sala->meet_uri }}">Copiar</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $salas->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css">
@endpush

@push('script')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/pt-br.js"></script>
<script>
document.querySelectorAll('.js-copiar-meet').forEach(function (botao) {
    botao.addEventListener('click', function () {
        var link = botao.getAttribute('data-link');
        var original = botao.textContent;
        navigator.clipboard.writeText(link).then(function () {
            botao.textContent = 'Copiado';
            setTimeout(function () {
                botao.textContent = original;
            }, 1500);
        });
    });
});

var calendario = document.getElementById('calendario-meet');
if (calendario && typeof FullCalendar !== 'undefined') {
    var agenda = new FullCalendar.Calendar(calendario, {
        locale: 'pt-br',
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        events: @json(route('dashboard.meet.calendario')),
        eventClick: function (info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.open(info.event.url, '_blank', 'noopener');
            }
        }
    });
    agenda.render();
}
</script>
@endpush
