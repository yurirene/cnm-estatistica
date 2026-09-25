@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Adote um Missionário'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">

            @if($adotado)
                <div class="card shadow p-4 mb-4 border-left border-primary" style="border-left-width: 4px !important;">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            @if($adotado->foto_url)
                                <img src="{{ $adotado->foto_url }}" alt="{{ $adotado->nome }}"
                                    class="img-fluid rounded" style="max-height: 280px; width: 100%; object-fit: contain; background: #f4f5f7;">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center mx-auto"
                                    style="width: 180px; height: 180px;">
                                    <i class="fas fa-user fa-4x text-white"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <span class="badge badge-primary mb-2">Seu missionário adotado</span>
                            <h2 class="mb-1">{{ $adotado->nome }}</h2>
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt"></i>
                                {{ $adotado->cidade }}@if($adotado->estado)/{{ $adotado->estado->sigla }}@endif
                                @if($adotado->regiao) · {{ $adotado->regiao->nome }}@endif
                                · {{ $adotado->pais }}
                            </p>
                            @if($adotado->whatsapp)
                                <p class="mb-1"><i class="fab fa-whatsapp"></i> {{ $adotado->whatsapp }}</p>
                            @endif
                            @if($adotado->email)
                                <p class="mb-1"><i class="fas fa-envelope"></i> {{ $adotado->email }}</p>
                            @endif
                            @if($adotado->outras_informacoes)
                                <p class="mt-3 mb-0">{{ $adotado->outras_informacoes }}</p>
                            @endif
                            @if($adotado->adotado_em)
                                <small class="text-muted d-block mt-2">
                                    Adotado em {{ $adotado->adotado_em->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow p-3 mb-4">
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard.adote-missionario.escolher') }}" class="form-row align-items-end">
                            <div class="form-group col-md-4 mb-2 mb-md-0">
                                <label for="regiao_id">Região</label>
                                <select name="regiao_id" id="regiao_id" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todas</option>
                                    @foreach($regioes as $regiao)
                                        <option value="{{ $regiao->id }}" @selected((string) $regiaoFiltro === (string) $regiao->id)>
                                            {{ $regiao->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4 mb-2 mb-md-0">
                                <label for="agencia">Agência</label>
                                <select name="agencia" id="agencia" class="form-control" onchange="this.form.submit()">
                                    <option value="">Todas</option>
                                    <option value="jmn" @selected($agenciaFiltro === 'jmn')>JMN</option>
                                    <option value="apmt" @selected($agenciaFiltro === 'apmt')>APMT</option>
                                </select>
                            </div>
                            @if($regiaoFiltro || $agenciaFiltro)
                                <div class="form-group col-md-4 mb-0">
                                    <a href="{{ route('dashboard.adote-missionario.escolher') }}" class="btn btn-outline-secondary">
                                        Limpar filtros
                                    </a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>

                @if($missionarios->isEmpty())
                    <div class="card shadow p-4">
                        <p class="mb-0 text-muted text-center">
                            Nenhum missionário disponível para adoção com os filtros selecionados.
                        </p>
                    </div>
                @else
                    <div class="row">
                        @foreach($missionarios as $missionario)
                            <div class="col-md-4 mb-4">
                                <div class="card shadow h-100">
                                    @if($missionario->foto_url)
                                        <div class="d-flex align-items-center justify-content-center"
                                            style="height: 240px; background: #f4f5f7;">
                                            <img src="{{ $missionario->foto_url }}"
                                                alt="{{ $missionario->nome }}"
                                                style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                        </div>
                                    @else
                                        <div class="bg-secondary d-flex align-items-center justify-content-center"
                                            style="height: 240px;">
                                            <i class="fas fa-user fa-3x text-white"></i>
                                        </div>
                                    @endif
                                    <div class="card-body d-flex flex-column">
                                        @if($missionario->agencia)
                                            <span class="badge badge-{{ $missionario->agencia === 'JMN' ? 'info' : 'secondary' }} align-self-start mb-2">
                                                {{ $missionario->agencia }}
                                            </span>
                                        @endif
                                        <h3 class="card-title mb-1">{{ $missionario->nome }}</h3>
                                        <p class="text-muted mb-2">
                                            {{ $missionario->cidade }}@if($missionario->estado)/{{ $missionario->estado->sigla }}@endif
                                            @if($missionario->regiao) · {{ $missionario->regiao->nome }}@endif
                                            · {{ $missionario->pais }}
                                        </p>
                                        @if($missionario->outras_informacoes)
                                            <p class="small text-muted flex-grow-1">
                                                {{ \Illuminate\Support\Str::limit($missionario->outras_informacoes, 120) }}
                                            </p>
                                        @else
                                            <div class="flex-grow-1"></div>
                                        @endif
                                        <button type="button" class="btn btn-primary btn-block mt-3"
                                            onclick="alertConfirmar('{{ route('dashboard.adote-missionario.adotar', $missionario->id) }}', 'Confirma a adoção deste missionário? Esta ação não poderá ser desfeita pela sua instância.')">
                                            Adotar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>
@endsection
