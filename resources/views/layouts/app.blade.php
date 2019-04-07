<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Campus Granadilla Norte') }}</title>

    <!-- Styles -->
    @yield('styles')
    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons' rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link href="{{ asset('css/vuetify.min.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app" class="main">
        @if (Auth::guest())
            <App login="false"  name=""></App>
        @else
            <App login="true"  name={{ Auth::user()->name }}></App>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
               {{ csrf_field() }}
            </form>
        @endif
        <v-app>
         <v-content>
            <div style=" backgroud-color:white; padding-top: 64px; height: 100%;">
               @yield('content')
            </div>
         </v-content>
      </v-app>
    </div>
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
