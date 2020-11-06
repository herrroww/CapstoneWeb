<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <script src="https://kit.fontawesome.com/fac72c378f.js" crossorigin="anonymous"></script>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="sidebar">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('sidebar.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            <!--@if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif-->
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4 container-fluid">
            
            <div id="sidebar" class="sidebar float-left">
                <div class="text-center">
                    <!--fontawesome.com--->
                    <img src="https://via.placeholder.com/100" alt="">
                    <h5>{{ Auth::user()->name }}</h5>
                </div>

                <div class="items-container ">
                <hr>
                    <a href="{{ route('gestionop') }}">
                        <h6 class="menu-item"><i class="fas fa-hard-hat"></i> Gestion Operarios</h6>
                    </a>
                    <a href="a">
                        <h6 class="menu-item"><i class="fas fa-boxes"></i> Gestion Componente Mecanico</h6>
                    </a>
                    <a href="a">
                        <h6 class="menu-item"><i class="far fa-folder-open"></i> Gestion Archivos</h6>
                    </a>
                    <a href="a">
                        <h6 class="menu-item"><i class="fas fa-clipboard-list"></i> Asignar Componente Mecanico</h6>
                    </a>
                    <a href="a">
                        <h6 class="menu-item"><i class="fas fa-chart-bar"></i> Historico</h6>
                    </a>
                    <a href="a">
                        <h6 class="menu-item"><i class="fas fa-exclamation-triangle"></i> Reporte de Problemas</h6>
                    </a>
                    <hr>
                    <a href="a">
                        <h6 class="menu-item"><i class="fas fa-info-circle"></i> Ayuda</h6>
                    </a>
                </div>
            </div>
            <div class="content">            
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>