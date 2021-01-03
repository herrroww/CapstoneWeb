<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AdminLTE 3</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
    <script src="dist/js/adminlte.js"></script>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('dist/css/adminlte.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div id="app">
        <div class="wrapper">

            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white  bg-blue">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                    </li>
                </ul>

                <!-- SEARCH FORM -->
                <!--<form class="form-inline ml-3">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search"
                            aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>-->

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto ">
                <a class="dropdown-item bg-orange color-white" href="{{ route('logout') }}" onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();">
                                    Cerrar Sesión
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                    </li>
                    <!-- Notifications Dropdown Menu -->
                    <li class="nav-item dropdown">
                        
                            
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4 bg-blue">
                <!-- Brand Logo -->
                <a href="" class="brand-link">
                <!--<a href="{{ url('/') }}" class="brand-link">-->
                    <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                        style="opacity: .8">
                    <span class="brand-text font-weight-light">Capstone Web</span>
                </a>

               
                <!-- Sidebar -->
                <div class="sider-azul sidebar">
                @if($activemenu == 'user')
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex bg-orange">
                    
                        <div class="image bg-orange">
                            <img src="https://media.discordapp.net/attachments/758330677157953536/793668087206903828/unknown.png" class="img-circle elevation-2" alt="User Image">
                        </div>
                
                
                        <div class="info bg-orange">
                        @else
                        <div class="user-panel mt-3 pb-3 mb-3 d-flex ">
                        <div class="image ">
                            <img src="https://media.discordapp.net/attachments/758330677157953536/793668087206903828/unknown.png" class="img-circle elevation-2" alt="User Image">
                        </div>
                
                
                        <div class="info ">
                        @endif
                      
                           <a href="{{ route('showuser') }}" class="d-block">
                           
                                
                                @guest
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Iniciar Sesión') }}</a>
                                @else
                                {{ Auth::user()->name }}
                                <!--<a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();">
                                    Cerrar Sesión
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>-->

                                @endguest
                            </a>
                        </div>
                    </div>
             
                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                            data-accordion="false">

                            @if($activemenu == 'home') 
                                            <li class="nav-item bg-orange">
                                                @else
                                            <li class="nav-item">
                                                @endif

                           
                                <a href="{{ route('home') }}" class="{{  Request::path() === '/' ? 'nav-link active' : 'nav-link' }}">
                                @if($activemenu == 'home') 
                                    <i class="fas fa-home color-white"></i>
                                    <p class="color-white">
                                        Home
                                    </p>
                                    @else
                                    <i class="fa fa-home"></i>
                                    <p>
                                        Home
                                    </p>
                                    @endif
                                </a>
                            </li>

                            
                            @if($activemenu == 'empresa') 
                                            <li class="nav-item bg-orange">
                                                @else
                                            <li class="nav-item">
                                                @endif

                    
                                <a href="{{ route('empresaop') }}"
                                    class="{{ Request::path() === 'usuarios' ? 'nav-link active' : 'nav-link' }}">
                                    @if($activemenu == 'empresa')
                                    <i class="fas fa-building color-white"></i>
                                    <p class="color-white">
                                        Gestión Empresa
                                    </p>
                                    @else
                                    <i class="fa fa-building"></i>
                                    <p >
                                        Gestión Empresa
                                    </p>
                                    @endif
                                </a>
                            </li>
                            @if($activemenu == 'operario') 
                                            <li class="nav-item bg-orange">
                                                @else
                                            <li class="nav-item">
                                                @endif

                                <a href="{{ route('gestionop') }}"
                                    class="{{ Request::path() === 'usuarios' ? 'nav-link active' : 'nav-link' }}">
                                    @if($activemenu == 'operario')
                                    <i class="fas fa-hard-hat color-white"></i>
                                    <p class="color-white">
                                        Gestión Operarios
                                    </p>
                                    @else
                                    <i class="fas fa-hard-hat"></i>
                                    <p>
                                        Gestión Operario
                                    </p>
                                    @endif
                                </a>
                            </li>

                            
                            @if($activemenu == 'componente') 
                                            <li class="nav-item bg-orange">
                                                @else
                                            <li class="nav-item">
                                                @endif
                                <a href="{{ route('componenteop') }}"
                                    class="{{ Request::path() === 'usuarios' ? 'nav-link active' : 'nav-link' }}">
                                    @if($activemenu == 'componente')
                                    <i class="fas fa-boxes color-white"></i>
                                    <p class='color-white'>
                                        Gestión Componente Mecánico
                                    </p>
                                    @else
                                    <i class="fas fa-boxes"></i>
                                    <p>
                                        Gestión Componente Mecánico
                                    </p>
                                    @endif
                                </a>
                            </li>


                         
                            @if($activemenu == 'asignar') 
                                            <li class="nav-item bg-orange">
                                                @else
                                            <li class="nav-item">
                                                @endif

                                <a href="{{ route('asignarop') }}"
                                    class="{{ Request::path() === 'usuarios' ? 'nav-link active' : 'nav-link' }}">
                                    @if($activemenu == 'asignar')
                                    <i class="fas fa-chart-bar color-white "></i>
                                    <p class='color-white'>
                                        Asignar Componente Mecánico
                                    </p>
                                    @else
                                    <i class="fas fa-chart-bar "></i>
                                    <p>
                                        Asignar Componente Mecánico
                                    </p>
                                    @endif
                                </a>
                            </li>

                            @if($activemenu == 'historial') 
                                            <li class="nav-item bg-orange">
                                                @else
                                            <li class="nav-item">
                                                @endif

                                <a href="{{ route('historialop') }}"
                                    class="{{ Request::path() === 'usuarios' ? 'nav-link active' : 'nav-link' }}">
                                    @if($activemenu == 'historial')
                                    <i class="fas fa-exclamation-triangle color-white"></i>
                                    <p class='color-white'>
                                        Historial de Gestión
                                    </p>
                                    @else
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <p>
                                        Historial de Gestión
                                    </p>
                                    @endif
                                </a>
                            </li>

                            @if($activemenu == 'reporteproblema') 
                                            <li class="nav-item bg-orange">
                                                @else
                                            <li class="nav-item">
                                                @endif
                                <a href="{{ route('reporteop') }}"
                                    class="{{ Request::path() === 'usuarios' ? 'nav-link active' : 'nav-link' }}">
                                    @if($activemenu == 'reporteproblema')
                                    <i class="fas fa-info-circle color-white"></i>
                                    <p class='color-white'>
                                        Reporte De Problemas
                                    </p>
                                    @else
                                    <i class="fas fa-info-circle"></i>
                                    <p>
                                       Reporte De Problemas
                                    </p>
                                    @endif
                                </a>
                            </li>

                            @if($activemenu == 'contactoadmin') 
                                            <li class="nav-item bg-orange">
                                                @else
                                            <li class="nav-item">
                                                @endif
                            
                                <a href="{{ route('/sendemail') }}"
                                    class="{{ Request::path() === 'usuarios' ? 'nav-link active' : 'nav-link' }}">
                                    @if($activemenu == 'contactoadmin')
                                    <i class="fas fa-headset color-white"></i>
                                    <p class='color-white'>
                                        Contacte con Administrador
                                    </p>
                                    @else
                                    <i class="fas fa-headset"></i>
                                    <p>
                                       Contacte con Administrador
                                    </p>
                                    @endif
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="usuarios"
                                    class="{{ Request::path() === 'usuarios' ? 'nav-link active' : 'nav-link' }}">
                                    <i class="fas fa-boxes"></i>
                                    <p>
                                        Ayuda
                                    </p>
                                </a>
                            </li>


                        </ul>
                    </nav>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">

                </div>
                <!-- /.content-header -->

                <!-- Main content -->
                <section class="content">
                    @yield('content')
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
            <footer class="main-footer">
                <!-- NO QUITAR -->
                <strong>Proyecto Capstone
                    <div class="float-right d-none d-sm-inline-block">
                        <b>Version</b> 1.0
                    </div>
            </footer>

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->
        </div>
    </div>
</body>

</html>