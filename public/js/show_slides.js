// to get the slide details and put it in the left side of the page
function getSlide(i) {

    // to remove the "active" class from all slides
    $(".card_slide").removeClass("active");

    // add "active" class to the clicked slide
    $("#card_slide_" + i).addClass("active");

    // to get full url of image 
    var image = slides[i].image;
    var imageUrl = baseImageUrl + image;

    // to get full url of audio 
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
