@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Comprovantes ACI'
])

<div class="container-fluid mt--7">
    <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
            <div class="card shadow p-3">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Lista de Comprovantes ACI</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive"> 
                        {!! $dataTable->table(['class' => 'table w-100']) !!}
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