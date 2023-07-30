@extends('layouts.app')

@section('content')
<!-- start of page title -->
<div class="container-fluid stories-page" style="flex: 1 !important;">
    <div class="row text-center mt-4">
        <div class="title my-5">
            لوحة التحكم
        </div>
    </div>
</div>
<!-- end of page title -->

<!-- start of square statics section -->
<div class="container">

    <div id="statistic" class="row justify-content-center gx-5 gy-2 mx-2 mb-4 my-2 mx-md-5 mx-sm-2">

        <!-- statics one to display total users and is the total of male and female -->
        <div class="col-8 col-lg-4 col-md-6 col-sm-6 mb-2 mb-md-4">
            <div class="square-statistic shadow p-4">
                <div class="row">
                    <div class="col">
                        <span class="square-large-text count-animation" data-final-count="{{ $userCount->sum() }}">0</span>
                    </div>
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <i class="fa-solid fa-person user-icon"></i>
                                <span class="count-animation" data-final-count="{{ $userCount->only([0, 1, 2])->sum() }}">0</span>
                            </div>
                            <div class="col">
                                <i class="fa-solid fa-person-dress user-icon"></i>
                                <span class="count-animation" data-final-count="{{ $userCount->only([3, 4, 5])->sum() }}">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <p class="square-text">عدد المستخدمين</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- statics two to display total users in each level -->
        <div class="col-8 col-lg-4 col-md-6 col-sm-6 mb-2 mb-md-4">
            <div class="square-statistic shadow p-4">

                <!-- to display user count in level one -->
                <div class="row d-flex w-100">
                    <div class="col-8">
                        <span class="feature-level">
                            <span class="count-animation" data-final-count="{{ $userLevelCount->get(1, 0) }}">0</span>
                            مستخدم
                        </span>
                    </div>
                    <div class="col-4">
                        <i class="fa-solid fa-1 level-icon"></i>
                    </div>
                </div>

                <!-- to display user count in level two -->
                <div class="row d-flex w-100">
                    <div class="col-8">
                        <span class="feature-level">
                            <span class="count-animation" data-final-count="{{ $userLevelCount->get(2, 0) }}">0</span>
                            مستخدم
                        </span>
                    </div>
                    <div class="col-4">
                        <i class="fa-solid fa-2 level-icon"></i>
                    </div>
                </div>

                <!-- to display user count in level three -->
                <div class="row d-flex w-100">
                    <div class="col-8">
                        <span class="feature-level">
                            <span class="count-animation" data-final-count="{{ $userLevelCount->get(3, 0) }}">0</span>
                            مستخدم
                        </span>
                    </div>
                    <div class="col-4">
                        <i class="fa-solid fa-3 level-icon"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <p class="square-text">عدد المستخدمين في كل مستوى</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- statics three to display total admins -->
        <div class="col-8 col-lg-4 col-md-6 col-sm-6 mb-2 mb-md-4">
            <div class="square-statistic shadow p-4">

                <div class="row d-flex w-100">
                    <div class="col-8 text-center">
                        <span class="square-large-text count-animation" data-final-count="{{ $adminCount }}">0</span>
                    </div>

                    <div class="col-4 ml-auto">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <p class="square-text">عدد المدراء</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
<!-- end of square statics section -->


<!-- start of circle statics section -->
<div class="container">
    <div id="round-total" class="row justify-content-center">


        <!-- round statics title  -->
        <div class="stories-statics-title text-center my-5">
            عدد القصص في كل مستوى
        </div>

        <!-- Loop through each level and display the count -->
        @for ($level = 1; $level <= 3; $level++) 
        <?php
            // to convert story level number to text like: 3 => hard
            $level_type = ($level == 1) ? 'سھل' : ($level == 2 ? 'متوسط' : 'صعب');
            ?>
            
            <!-- Check if the level exists in $levelCounts -->
            @if ($levelCounts->has($level))
            <?php $count = $levelCounts[$level]; ?>

            <!-- Display the level box with the count of story in it -->
            <div class="col-md-4 col-sm-6 d-flex align-items-center justify-content-center round-box-container">
                <div class="round-box mb-2 p-4 ">
                    <div class="row">
                        <div class="col text-center">
                            <p class="count-animation" data-final-count="{{ $count }}">0</p>
                            <p>{{ $level_type }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Display the level box with count 0 -->
            <div class="col-md-4 col-sm-6 d-flex align-items-center justify-content-center">
                <div class="round-box mb-2 p-4 ">
                    <div class="row">
                        <div class="col text-center">
                            <p>0</p>
                            <p>{{ $level_type }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @endfor

    </div>
</div>
<!-- end of circle statics section -->


<!-- star of line chart section -->

<!-- import google chart script -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<!-- pass userGrowth data to chart.js -->
<script>
    var userGrowthData = @json($userGrowth);
</script>

<!-- load the chart.js file -->
<script type="text/javascript" src="{{ asset('js/chart.js') }}"></script>


<!-- line chart design section -->
<div class="container-fluid" id="chart_container">
    <div class="row my-4">
        <div class="col-lg-3 col-md-4 d-none d-md-block">
            <img src="{{ asset('img/person_two.png') }}" class=" img-fluid chart-img" alt="">
        </div>
        <div class="col-lg-6 col-md-4 col-12 d-flex justify-content-center m-0 p-0">
            <div id="curve_chart" style="width: 900px; height: 500px;"></div>
        </div>
        <div class="col-lg-3 col-md-4 d-none d-md-block">
            <img src="{{ asset('img/person_one.png') }}" class=" img-fluid chart-img" alt="">
        </div>
    </div>
</div>

<!-- end of line chart section -->


@endsection