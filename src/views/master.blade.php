<!doctype html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!-- CSS files -->
    <link href="{{ asset('vendor/mediafile/css/style.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-dash-100 text-white">

    @include('MediaFile::layouts.navbar')
    
    @yield('breadcrumb')

    <!-- Content -->
    <div class="relative">
        <div class="absolute px-8 lg:px-16 left-0 -top-4 z-0 w-full">
            <div class="bg-dash-400 py-12 rounded-3xl"></div>
        </div>
        <div class="relative bg-dash-200 my-12 mx-3 lg:mx-8 pb-10 px-6 rounded-3xl z-10">
            @yield('content')
        </div>

        @include('MediaFile::layouts.notifications')
    </div>

    <script src="{{ asset('vendor/mediafile/js/flowbite.min.js') }}"></script>
    <script type="text/javascript">
        const notifyBtns = document.querySelectorAll('.close-notification');
            
        notifyBtns.forEach(function (notifyBtn) {
            notifyBtn.addEventListener('click', function(e) {
                e.preventDefault();
                notifyBtn.parentElement.remove();
            })
        });
    </script>
    @stack('scripts')
</body>
</html>
