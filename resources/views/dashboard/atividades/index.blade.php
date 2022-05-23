@extends('layouts.app')

@section('content')

@include('dashboard.partes.head')

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Lista de Atividades</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist" style="line-height: 40px;">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" 
                                id="primeiro-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#primeiro" 
                                type="button" 
                                role="tab" 
                                aria-controls="primeiro" 
                                aria-selected="true">Calendário
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" 
                                id="segundo-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#segundo" 
                                type="button" 
                                role="tab" 
                                aria-controls="segundo" 
                                aria-selected="false">Atividades Realizadas
                            </button>
                        </li>
                
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="primeiro" role="tabpanel" aria-labelledby="primeiro-tab">
                            <div class="row mt-3">
                                <div class="col-md-8 mt-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div id="app_calendar"></div>
                                            </div>
                                        </div>
                                </div>

                                <div class="col-md-4 mt-3">
                                        <div class="card">
                                            <div class="card-header">
                                                Eventos
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table align-items-center table-flush">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th scope="col" class="sort" data-sort="name">Data</th>
                                                                <th scope="col" class="sort" data-sort="budget">Evento</th>
                                                                <th scope="col" class="sort" data-sort="status">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="list">
                                                            <tr>
                                                                <td class="budget">
                                                                    $2500 USD
                                                                </td>
                                                            
                                                                <td class="text-right">
                                                                    $2500 USD
                                                                </td>

                                                                <td>
                                                                    <span class="badge badge-dot mr-4">
                                                                        <span class="badge badge-warning">Pendente</span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="segundo" role="tabpanel" aria-labelledby="segundo-tab">
                                bbb
                            </div>
                        
                        </div>
                    </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script>


document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('app_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        themeSystem: 'bootstrap5',
        buttonText: {
            today: 'Hoje'
        },
        buttonIcons: {
            prev: 'arrow-left',
            next: 'arrow-right',
            prevYear: 'arrow-left-circle', 
            nextYear: 'arrow-right-circle' 
        },
        events: '{{route("dashboard.atividades.calendario")}}'
    });
    calendar.setOption('locale', 'pt-br');
    calendar.render();
    
});
</script>
@endpush