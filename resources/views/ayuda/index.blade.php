@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="col-12 pt-3 pb-3 text-center ">
        <h2>Ayuda</h2>
    </div>
    <hr>
    <div class="col-12 pt-3 pb-3 text-center ">
        <h3>Maquinas 1</h3>
    </div>
    <div class="card-group mt-2">
        <div class="card m-2" style="width: 18rem;">
            <div class="contenedor-video d-inline-block position-relative text-truncate" style="height: 150px;width: 100%">
                <img class="card-img-top img-fluid" src="{{ asset('dist/img/brazo1.jpg') }}" alt="Card image">
                <a href="https://www.youtube.com/watch?v=pgvvuZzjUmU" class="venobox-video" data-vbtype="video"
                   title="Brazo 1"><i class="fas fa-play"></i></a>
            </div>
            <div class="card-body">
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
        </div>
        <div class="card m-2" style="width: 18rem;">
            <div class="contenedor-video d-inline-block position-relative text-truncate img-fluid" style="height: 150px;width:100%">
                <img class="card-img-top" src="{{ asset('dist/img/brazo2.jpg') }}" alt="Card image">
                <a href="https://www.youtube.com/watch?v=iI5Z9rJ_lwM" class="venobox-video" data-vbtype="video"
                   title="Brazo 2"><i class="fas fa-play"></i></a>
            </div>
            <div class="card-body">
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
        </div>
        <div class="card m-2" style="width: 18rem;">
            <div class="contenedor-video d-inline-block position-relative text-truncate img-fluid" style="height: 150px;width:100%">
                <img class="card-img-top img-fluid" src="{{ asset('dist/img/brazo3.jpg') }}" alt="Card image">
                <a href="https://www.youtube.com/watch?v=sWgvIAkfqXQ" class="venobox-video" data-vbtype="video"
                   title="Brazo 3"><i class="fas fa-play"></i></a>
            </div>
            <div class="card-body">
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
        </div>
    </div>
    <hr>

    <div class="col-12 pt-3 pb-3 text-center ">
        <h3>Maquinas 2</h3>
    </div>
    <div class="card-group mt-2">
        <div class="card m-2" style="width: 18rem;">
            <div class="contenedor-video d-inline-block position-relative text-truncate img-fluid" style="height: 150px;width:100%">
                <img class="card-img-top img-fluid" src="{{ asset('dist/img/brazo4.jpg') }}" alt="Card image">
                <a href="https://www.youtube.com/watch?v=M-IzaLUZsvk" class="venobox-video" data-vbtype="video"
                   title="Brazo 4"><i class="fas fa-play"></i></a>
            </div>
            <div class="card-body">
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
        </div>
        <div class="card m-2" style="width: 18rem;">
            <div class="contenedor-video d-inline-block position-relative text-truncate img-fluid" style="height: 150px;width:100%">
                <img class="card-img-top img-fluid" src="{{ asset('dist/img/brazo5.jpg') }}" alt="Card image">
                <a href="https://www.youtube.com/watch?v=-N7_h_qFtC8" class="venobox-video" data-vbtype="video"
                   title="Brazo 5"><i class="fas fa-play"></i></a>
            </div>
            <div class="card-body">
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
        </div>
        <div class="card m-2" style="width: 18rem;">
            <div class="contenedor-video d-inline-block position-relative text-truncate img-fluid" style="height: 150px;width:100%">
                <img class="card-img-top img-fluid" src="{{ asset('dist/img/brazo6.jpg') }}" alt="Card image">
                <a href="https://www.youtube.com/watch?v=hLB2WuPMel0" class="venobox-video" data-vbtype="video"
                   title="Brazo 6."><i class="fas fa-play"></i></a>
            </div>
            <div class="card-body">
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
        </div>
    </div>
</div>
@endsection