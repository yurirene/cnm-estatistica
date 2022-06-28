@extends('layouts.app')

@section('content')

@include('dashboard.partes.head', [
    'titulo' => 'Sinodais'
])
    


<div class="container-fluid mt--7">
    
    <div class="row">
        <div class="col-xl-3 mt-3">
            <div class="card shadow h-100">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class=" mb-0">Ranking</h2>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow" style="height: 100px; width: 100px">
                                <i class="fas fa-medal" style="font-size: 50px;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <h2 class="text-center"><span class="badge badge-primary h2" >X°</span></h2>
                            <h5 class="text-center">Posição</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="col-xl-5 mt-3 mb-5 mb-xl-0">
            @include('dashboard.index.avisos')
        </div>
       
    </div>
</div>

<!-- Modal -->

@endsection
