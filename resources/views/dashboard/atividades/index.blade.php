@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Atividades'
])

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
                                aria-selected="false">Atividades
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="primeiro" role="tabpanel" aria-labelledby="primeiro-tab">
                            <div class="row mt-3">
                                <div class="col-md-7 mt-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div id="app_calendar"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5 mt-3">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            Atividades
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive mt-4" style="min-height: 150px;">
                                                <table class="table align-items-center table-flush">
                                                    <thead class="thead-dark text-center">
                                                        <tr>
                                                            <th scope="col" class="sort">Data</th>
                                                            <th scope="col" class="sort">Evento</th>
                                                            <th scope="col" class="sort">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="list" id="tabela-eventos">
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="segundo" role="tabpanel" aria-labelledby="segundo-tab">
                            <div class="row mt-3">
                                <div class="col-md-12 mt-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive"> 
                                                {!! $dataTable->table(['class' => 'table w-100']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  
@endsection

@push('js')
{!! $dataTable->scripts() !!}
<script>

$(document).ready(function() {

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
        events: '{{route("dashboard.atividades.calendario")}}',
        eventSourceSuccess: function(content, xhr) {
            var html = '';
            content.forEach((item) => {
                let status = item.status == 0 ? '<span class="badge badge-danger">Pendente</span>' : '<span class="badge badge-success">Presente</span>';
                html += `<tr>
                            <td class="text-center">
                                ${item.dt}
                            </td>                        
                            <td class="text-left">
                                ${item.title}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-dot mr-4">
                                    ${status}
                                </span>
                            </td>
                        </tr>`
            });
            $('#tabela-eventos').html(html);
        }
    });
    calendar.setOption('locale', 'pt-br');
    calendar.render();
});

</script>
@endpush