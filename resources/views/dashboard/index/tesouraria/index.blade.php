@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Tesouraria'
])
    


<div class="container-fluid mt--7">
    
    <div class="row">
        <div class="col-xl-3 mt-3">
            <div class="card shadow h-100">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class=" mb-0">Sem informações</h2>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body"  style="height: 300px;">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow" style="height: 100px; width: 100px">
                                <i class="fas fa-exclamation" style="font-size: 50px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>       
    </div>
</div>

<!-- Modal -->

@endsection
