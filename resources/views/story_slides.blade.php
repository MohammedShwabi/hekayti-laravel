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

                <div class="slides" id="sortable">
                    @php
                    // this to check if there is slides coming
                    $hasSlide = count($slides) > 0;
                    // this count to using it on the js func to get the index of array
                    $i = 0;
                    @endphp

                    <script>
                        // pass the blade array to js array
                        var slides = @json($slides);

                        // to get the slide details and put it in the left side of the page
                        function getSlide(i) {
                            // to add the active class

                            $(".card_slide").removeClass("active");

                            // add "active" class to the clicked slide
                            $("#card_slide_" + i).addClass("active");

                            // to get full url of image 
                            var baseImageUrl = '{{ URL::to('/') }}/storage/upload/slides_photos/';
                            var image = slides[i].image;
                            var imageUrl = baseImageUrl + image;

                            // to get full url of audio 
                            var baseAudioUrl = '{{ URL::to('/') }}/storage/upload/slides_sounds/';
                            var audio = slides[i].audio;
                            var audioUrl = baseAudioUrl + audio;

                            // set data from js array to html page  
                            $('#slide_id').text(slides[i].id);
                            $('#slide_image').attr('src', imageUrl);
                            $('#slide_audio').attr('src', audioUrl);
                            $('#slide_text').text(slides[i].text);

                            $('#edit-image').attr('onclick', "editMedia('image','/editSlideImage')");
                            $('#replace_audio').attr('onclick', "editMedia('audio', '/editSlideAudio')");
                            $('#edit_text_icon').attr('onclick', "editText()");

                            $("#icon_text").text("تعديل");

                            $("#error-image-message").text("");
                            $("#error-sound-message").text("");
                            $("#error-text-message").text("");

                            $('.add-slide-btns').html('');

                        }
                    </script>
                    {{-- Merge the id with the id to distinguish it --}}
                    @if ($hasSlide)
                    @foreach ($slides as $slide)
                    <div class="card_slide card" id="card_slide_{{ $i }}" data-slide-id="{{ $slide->id }}" onclick="getSlide({{ $i }})" >
                        <div class="row px-1 justify-content-center align-items-center">
                            <div class="col-4 card-image my-1 p-0">
                                <img id="image{{ $slide->id }}" src="{{ asset('storage/upload/slides_photos/thumbs/' . $slide->image) }}" class="img-fluid " alt="...">
                            </div>
                            <div class="col-6 pe-3 card-text">
                                <p id="text{{ $slide->id }}">{{ $slide->text }}</p>
                            </div>
                            <!-- delete the icon if the story has been published     -->
                            @if (!$story->published)
                            <div class="col-2 px-1">
                                <div class="delete-slide" onclick="event.stopPropagation(); deletePopup({{$slide->id}},'delete_slide','del_slide_id')">
                                    <i class="fa fa-trash-can"></i>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @php
                    // incerment the count
                    $i++;
                    @endphp
                    @endforeach
                    @php
                    // this to get the last slide to print it
                    $last_slide = $slides[count($slides) - 1];
                    @endphp
                    @endif

                    @if (!$story->published)
                    <!-- this card for the add slide btn -->

                    <div class="card justify-content-center px-4">
                        <div class="row add-slide">
                            <div class="add-slide-btn col-4 ">
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

            <!-- enable sort only if story is not published  -->
            @if (!$story->published)
            <!-- for sort story slide using jQuery SortableJS -->
            <script src="https://unpkg.com/sortablejs-make/Sortable.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>

            <!-- another way, you can sort using jQuery UI  -->
            <!-- <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script> -->

            <!-- load the chart.js file -->
            <script src="{{ URL::asset('js/sort_slid.js') }}" async></script>
            @endif

        </div>
        <div class="col-xlg-8 col-lg-8 p-0">
            <div class="view-slide">

                @php
                $slide_photo = $hasSlide ? $last_slide->image : 'img_upload.svg';
                $slide_audio = $hasSlide ? $last_slide->audio : '';
                $slide_text = $hasSlide ? $last_slide->text : 'أدخل النص هنا';
                $slide_id = $hasSlide ? $last_slide->id : null;
                @endphp
                {{-- style="display : none" --}}

                <div id="story_id" style="display : none">{{ $story->id }}</div>
                <div id="slide_id" style="display : none">{{ $slide_id }}</div>

                <div id="error-image-message" class="shadow"></div>

                <div class="row image p-0">
                    @if (!$story->published)
                    <div class="edit-img py-1 px-4 " id="edit-photo" onclick="{{ $hasSlide ? 'editMedia(\'image\', \'/editSlideImage\')' : 'addPhoto()' }}">
                        <span id="icon_text">{{ $hasSlide ? 'تعديل' : 'إضافة' }}</span>
                        <div class="fa fa-pen"></div>
                    </div>
                    @endif
                    <img id="slide_image" src="{{ asset('storage/upload/slides_photos/'.$slide_photo) }}" class="img-fluid w-100 p-0" alt="...">
                </div>
                <div id="imageInput"></div>

                <div class="row sound align-items-center py-4 px-4">
                    <audio controls class="col-11" id="slide_audio" src="{{ asset('storage/upload/slides_sounds/'.$slide_audio) }}">
                        {{-- if there is more than formatt of audio file we can use the source tag her --}}
                        Your browser does not support the audio element.
                    </audio>
                    @if (!$story->published)
                    <span class="replace px-3 col-1 m-lg-0 m-2" id="replace_sound" onclick="{{ $hasSlide ? 'editMedia(\'audio\', \'/editSlideAudio\')' : 'addSound()' }}">
                        <div class="fa-solid fa-repeat"></div>
                    </span>
                    @endif
                </div>
                <div id="error-audio-message" class="px-4 text-center invalid-feedback d-block"></div>
                <div id="audioInput"></div>

                <div class="row editable-div text align-items-center py-4 px-4">
                    <div class="col-11">
                        <p id="slide_text">{{ $slide_text }}</p>
                    </div>
                    @if (!$story->published)
                    <span class="edit-text px-3 shadow-lg col-1 m-lg-0 m-2" id="edit_text_icon" onclick="{{ $hasSlide ? 'editText()' : 'addText()' }}">
                        <div class="fa fa-pen"></div>
                    </span>
                    @endif
                </div>

                <div id="error-text-message" class="align-items-center  px-4 text-center invalid-feedback d-block">
                </div>

                <div class="add-slide-btns modal-footer  justify-content-evenly pb-4" style="border-top: none;">
                    @if (!$hasSlide)

                    <button type="button" class="btn save" id="add_slide" onclick="saveSlide()">حفظ</button>
                    <input type="reset" onclick="getSlide({{ $i }})" class="cancel slide-cancel btn btn-secondary" value="إلغاء">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- dele popup --}}
<div class="modal fade" tabindex="-1" id="delete_slide" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">حذف صفحة</h5>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/deleteSlide" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="slide_id" id="del_slide_id">
                    <p class="text-center delete-text">هل انت متاكد من الحذف</p>
                </div>
                <div class="modal-footer justify-content-evenly">
                    <button type="submit" class="btn save" id="delete_btn">حذف</button>
                    <button type="button" class="btn btn-secondary cancel" data-bs-dismiss="modal">الغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script></script>
@endsection