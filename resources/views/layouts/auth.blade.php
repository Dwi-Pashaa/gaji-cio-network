<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, viewport-fit=cover"
    />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>
      @yield('title') | {{ config('app.name') }}
    </title>
    <!-- CSS files -->
    <link href="{{asset('css/tabler.min.css?1738096682')}}" rel="stylesheet" />
    <link href="{{asset('css/tabler-flags.min.css?1738096682')}}" rel="stylesheet" />
    <link
      href="{{asset('css/tabler-socials.min.css?1738096682')}}"
      rel="stylesheet"
    />
    <link
      href="{{asset('css/tabler-payments.min.css?1738096682')}}"
      rel="stylesheet"
    />
    <link
      href="{{asset('css/tabler-vendors.min.css?1738096682')}}"
      rel="stylesheet"
    />
    <link
      href="{{asset('css/tabler-marketing.min.css?1738096682')}}"
      rel="stylesheet"
    />
    <link href="{{asset('css/demo.min.css?1738096682')}}" rel="stylesheet" />
    <link href="{{asset('css/cio-theme.css')}}" rel="stylesheet" />
    @stack('css')
  </head>
  <body class="d-flex flex-column auth-page-bg">
    <div class="page page-center py-4">
      <div class="auth-container-wrapper px-3 py-2">
        <div class="text-center mb-4">
          <a href="{{route('login')}}" class="navbar-brand navbar-brand-autodark">
            <img src="{{asset('img/logo.png')}}" width="210" class="auth-brand-logo" alt="{{ config('app.name') }}">
          </a>
        </div>
        @if (session()->has('error'))
            @include('components.alert.danger')
        @endif

        @if (session()->has('warning'))
            @include('components.alert.warning')
        @endif
        <div class="card auth-card shadow-lg border-0">
          <div class="card-body p-4 p-md-5">
            @yield('content')
          </div>
        </div>

        <div class="text-center text-muted small mt-4">
          &copy; {{date('Y')}} <span class="fw-semibold text-dark">{{ config('app.name') }}</span>. All Rights Reserved.
        </div>
      </div>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="{{asset('js/tabler.min.js?1738096682')}}"></script>
    <script src="{{asset('js/demo.min.js?1738096682')}}"></script>
    @stack('js')
  </body>
</html>
