<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
    	<meta name="revisit-after" content="1 day" />
	    <meta name="author" content="DeepskyLog - VVS" />
	    <meta name="keywords" content="VVS, Vereniging Voor Sterrenkunde, astronomie, sterrenkunde, astronomy, Deepsky, deep-sky, waarnemingen, observations, kometen, comets, planeten, planets, moon, maan" />

        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

        <link type="text/css" rel="stylesheet" href="{{ mix('css/app.css') }}">
        <script type="text/javascript" src="{{ asset('/js/app.js') }}"></script>

    	<title>@yield('title', 'DeepskyLog')</title>
    </head>

    <body>
        @include('layout.header')

        <div>
            @yield('content')
        </div>

        @include('layout.footer')

    </body>
</html>