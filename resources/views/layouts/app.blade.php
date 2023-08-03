<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Adding favicon -->
    <link rel="shortcut icon" href="{{ asset('img/logo.ico') }}" type="image/x-icon" />

    <!-- CDN link for bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- CDN link for font-awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- or user local bootstrap and font-awesome css -->
    <!-- <link href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet" /> -->
    <!-- <link href="{{ URL::asset('css/all.min.css') }}" rel="stylesheet" /> -->
   

    <!-- style css -->
    <link href="{{ URL::asset('css/backend.css') }}" rel="stylesheet" />


    <!-- CDN link for jquery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

    <!-- or user local jquery -->
    <!-- <script src="{{ URL::asset('js/jquery-3.6.0.min.js') }}" async></script> -->

</head>

<body>
    @guest
    @else
    <!-- start navbar -->
    <nav class="navbar navbar-expand-md fixed-top navbar-light shadow">
        <div class="container">
            <a class="navbar-brand" href="{{ (Auth::user()->role == 'admin') ? route('home') : route('stories').'?level=1' }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:60px;" class="img-fluid" />
            </a>

            <button class="navbar-toggler" type="button" aria-label="navbar toggler" data-bs-toggle="collapse" data-bs-target=".navitems">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center  navitems" id="mynavbar">
                <ul class="navbar-nav nav-icon text-center me-auto">
                    @if (Auth::user()->role == 'admin')
                    <li class="nav-item {{ Route::currentRouteName() === 'home' ? 'active' : '' }}">
                        <a class="nav-link" href="{{route('home') }}">
                            <p class="nav-text">لوحة التحكم</p>
                        </a>
                    </li>
                    @else
                    <li class="nav-item {{ Route::currentRouteName() === 'profile' ? 'active' : '' }}">
                        <a class="nav-link" href="{{  route('profile')  }}">
                            <p class="nav-text">المعلومات الشخصية</p>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item {{ Route::currentRouteName() === 'stories' || Route::currentRouteName() === 'storyslide' ? 'active' : '' }}">
                        <a class="nav-link" href="{{route('stories') }}?level=1">
                            <p class="nav-text">القصص</p>
                        </a>
                    </li>
                    @if (Auth::user()->role == 'admin')
                    <li class="nav-item {{ Route::currentRouteName() === 'manage' ? 'active' : '' }}">
                        <a class="nav-link" href="{{route('manage') }}">
                            <p class="nav-text"> الادارة</p>
                        </a>
                    </li>
                    @endif
                </ul>
                <ul class="navbar-nav me-auto justify-content-center align-items-center">
                    <li>
                        <a class="navbar-brand" href="{{  route('profile')  }}">
                            <!-- edit here -->
                            <img src="{{ asset('storage/upload/profiles_photos/thumbs/' . Auth::user()->image) }}" alt="Logo" class="img-fluid" id="round-profile" />
                        </a>
                    </li>
                    <li class="nav-item dropdown {{ Route::currentRouteName() === 'profile' ? 'active' : '' }}">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <?php
                            $words = explode(' ', Auth::user()->name);
                            ?>
                            {{$words[0]}}
                        </a>

                        <!-- edit here -->
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                {{ __('تسجيل خروج') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                {{ __('المعلومات الشخصية') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>

                    </li>

                </ul>
            </div>

        </div>
    </nav>
    <!-- end navbar -->
    @endguest
    @yield('content')

    <!-- edit here -->
    <div class="footer mt-5">
        <div class="text-center p-3">
            MountaineersTeam © 2023
        </div>
    </div>

    <!-- CDN link for Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous" async></script>
    <!-- CDN link for font-awesome-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" integrity="sha512-2bMhOkE/ACz21dJT8zBOMgMecNxx0d37NND803ExktKiKdSzdwn+L7i9fdccw/3V06gM/DBWKbYmQvKMdAA9Nw==" crossorigin="anonymous" referrerpolicy="no-referrer" async></script>


    <!-- or user local bootstrap and font-awesome js -->

    <!-- <script src="{{ URL::asset('js/bootstrap.bundle.min.js') }}" async></script> -->
    <!-- <script src="{{ URL::asset('js/all.min.js') }}" async></script> -->


    <!-- app javascript code -->
    <script src="{{ URL::asset('js/backend.js') }}" async></script>
</body>

</html>