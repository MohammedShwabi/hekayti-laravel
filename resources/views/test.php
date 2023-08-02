@extends('layouts.app')
@section('content')
<div class="container-fluid full-page p-0" style="margin-bottom: -47px;">
    <!-- start of loading overlay element -->
    <div id="loading-overlay">
        <div class="spinner"></div>
    </div>
    <!-- end of loading overlay element -->
    <div class="row h-100 p-0">
        <div class="col-xlg-4 col-lg-4 p-0" style="background: #E86565;">
            <div class="slides-part">
                <div class="story-title shadow position-relative py-4 pe-3">
                    {{ $story->name }}
                </div>

                <div class="slides">
                    @php
                    // this to check if there are slides coming
                    $hasSlide = count($slides) > 0;
                    @endphp

                    <script>
                        // pass the blade array to js array
                        var slides = @json($slides);

                        // to get the slide details and put it in the left side of the page
                        function getSlide(i) {
                            $(".card_slide").removeClass("active");
                            $("#card_slide_" + i).addClass("active");

                            // ...
                        }
                    </script>

                    {{-- Merge the id with the id to distinguish it --}}
                    @foreach ($slides as $i => $slide)
                    <div class="card_slide card {{ $i === count($slides) - 1 ? 'active' : '' }}" id="card_slide_{{ $i }}" onclick="getSlide({{ $i }})">
                        <div class="row px-1 justify-content-center align-items-center">
                            <div class="col-4 card-image my-1 p-0">
                                <img id="image{{ $slide->id }}" src="{{ asset('upload/slides_photos/thumbs/' . $slide->image) }}" class="img-fluid" alt="...">
                            </div>
                            <div class="col-6 pe-3 card-text">
                                <p id="text{{ $slide->id }}">{{ $slide->text }}</p>
                            </div>
                            <!-- Delete the icon if the story has not been published -->
                            @if (!$story->published)
                            <div class="col-2 px-1">
                                <div class="delete-slide" onclick="event.stopPropagation(); deletePopup({{$slide->id}},'delete_slide','del_slide_id')">
                                    <i class="fa fa-trash-can"></i>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    @if (!$story->published)
                    <!-- This card is for the add slide button -->
                    <div class="card justify-content-center px-4">
                        <div class="row add-slide">
                            <div class="add-slide-btn col-4">
                                <i class="fa fa-add"></i>
                            </div>
                            <div class="add-slide-text col-8 d-flex justify-content-center align-items-center">
                                <p class="p-0 m-0">إضافة صفحة جديدة</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xlg-8 col-lg-8 p-0">
            <div class="view-slide">
                {{-- ... --}}
                <div id="story_id" style="display: none">{{ $story->id }}</div>
                <div id="slide_id" style="display: none">{{ $hasSlide ? $last_slide->id : null }}</div>
                {{-- ... --}}
            </div>
        </div>
    </div>
</div>

{{-- ... --}}
@endsection
