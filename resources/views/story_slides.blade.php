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

                    <div class="story-title shadow position-relative py-4 pe-3">{{ $story->name }}</div>
                    <div class="slides">

                        <!-- Include the JavaScript file and pass the slides data -->
                        <script>
                            var slides = @json($slides);
                            var baseImageUrl = '{{ URL::to('/') }}/storage/upload/slides_photos/';
                            var baseAudioUrl = '{{ URL::to('/') }}/storage/upload/slides_sounds/';
                        </script>

                        @php
                            $not_published  = !$story->published;
                            $colClass = $not_published ? 'col-4' : 'col-6';
                        @endphp

                        <div id="sortable">
                            @foreach ($slides as $slide)
                                <div class="card_slide card" id="card_slide_{{ $slide->page_no }}"
                                    data-slide-id="{{ $slide->id }}" onclick="getSlide({{ $slide->page_no }})">
                                    <div class="row px-1 justify-content-center align-items-center">
                                        <!-- add sort icon only if the story has not been published      -->
                                        @if ($not_published)
                                            <div class="col-2 d-flex justify-content-center align-items-center">
                                                <i class="fa-solid fa-bars sort-icon"></i>
                                            </div>
                                        @endif
                                        <div class="col-4 card-image my-1 p-0">
                                            <img id="image{{ $slide->id }}"
                                                src="{{ asset('storage/upload/slides_photos/thumbs/' . $slide->image) }}"
                                                class="img-fluid " alt="...">
                                        </div>
                                        <div class="{{ $colClass }} pe-3 card-text">
                                            <p id="text{{ $slide->id }}">{{ $slide->text }}</p>
                                        </div>
                                        <!-- delete the icon if the story has been published     -->
                                        @if ($not_published)
                                            <div class="col-2 px-1">
                                                <div class="delete-slide"
                                                    onclick="event.stopPropagation(); deletePopup({{ $slide->id }},'delete_slide','del_slide_id')">
                                                    <i class="fa fa-trash-can"></i>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($not_published)
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
                @if ($not_published)
                    <!-- CDN link for jQuery SortableJS -->
                    <!-- <script src="https://unpkg.com/sortablejs-make/Sortable.min.js"></script> -->
                    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@1.0.1/jquery-sortable.min.js"></script> -->

                    <!-- another way using jQuery UI  -->
                    <!-- <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script> -->

                    <!-- or user local jQuery SortableJS -->
                    <script src="{{ URL::asset('js/Sortable.min.js') }}"></script>
                    <script src="{{ URL::asset('js/jquery-sortable.min.js') }}"></script>

                    <!-- load the chart.js file -->
                    <script src="{{ URL::asset('js/sort_slides.js') }}" async></script>
                @endif
            </div>

            <!-- start of the story content -->
            @php
                if (count($slides) > 0) {
                    $last_slide = $slides[count($slides) - 1];
                    $slide_photo = $last_slide->image;
                    $slide_audio = $last_slide->audio;
                    $slide_text = $last_slide->text;
                    $slide_id = $last_slide->id;
                    $hasSlide = true;
                
                    // change method and ui
                    $imageClick = "editMedia('image', '/editSlideImage')";
                    $imageText = 'تعديل';
                    $audioClick = "editMedia('audio', '/editSlideAudio')";
                    $textClick = 'editText()';
                } else {
                    $slide_photo = 'img_upload.svg';
                    $slide_audio = '';
                    $slide_text = 'أدخل النص هنا';
                    $slide_id = null;
                    $hasSlide = false;
                
                    // change method and ui
                    $imageClick = 'addPhoto()';
                    $imageText = 'إضافة';
                    $audioClick = 'addSound()';
                    $textClick = 'addText()';
                }
            @endphp

            <div class="col-xlg-8 col-lg-8 p-0">
                <div class="view-slide">
                    <div id="story_id" style="display : none">{{ $story->id }}</div>
                    <div id="slide_id" style="display : none">{{ $slide_id }}</div>

                    <div id="error-image-message" class="shadow"></div>

                    <div class="row image p-0">
                        @if ($not_published)
                            <div class="edit-img py-1 px-4 " id="edit_image" onclick="{{ $imageClick }}">
                                <span id="icon_text">{{ $imageText }}</span>
                                <div class="fa fa-pen"></div>
                            </div>
                        @endif

                        <div id="imageInput"></div>
                        <img id="slide_image" src="{{ asset('storage/upload/slides_photos/' . $slide_photo) }}"
                            class="img-fluid w-100 p-0" alt="...">
                    </div>

                    <div class="row sound align-items-center py-4 px-4">
                        <audio controls class="col-11" id="slide_audio"
                            src="{{ asset('storage/upload/slides_sounds/' . $slide_audio) }}">
                            {{-- if there is more than format of audio file we can use the source tag her --}}
                            Your browser does not support the audio element.
                        </audio>
                        @if ($not_published)
                            <span class="replace px-3 col-1 m-lg-0 m-2" id="replace_audio" onclick="{{ $audioClick }}">
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
                        @if ($not_published)
                            <span class="edit-text px-3 shadow-lg col-1 m-lg-0 m-2" id="edit_text_icon"
                                onclick="{{ $textClick }}">
                                <div class="fa fa-pen"></div>
                            </span>
                        @endif
                    </div>

                    <div id="error-text-message" class="align-items-center  px-4 text-center invalid-feedback d-block">
                    </div>

                    <div class="add-slide-btns modal-footer justify-content-evenly pb-4" style="border-top: none;">
                        @if (!$hasSlide)
                            <button type="button" class="btn save" id="add_slide" onclick="saveSlide()">حفظ</button>
                            <input type="reset" class="cancel slide-cancel btn btn-secondary" value="إلغاء">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($not_published)
        {{-- dele popup --}}
        @component('components.delete-confirmation-modal', [
            'modalId' => 'delete_slide',
            'modalTitle' => 'حذف صفحة',
            'formAction' => '/deleteSlide',
            'formInputName' => 'del_slide_id',
            'modalMessage' => 'هل أنت متأكد من الحذف؟',
        ])
        @endcomponent
    @endif
@endsection
