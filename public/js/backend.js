// ************ start of general function section ************

// initialize variable to store the current focus  search result index
var currentFocus = -1;

// Function to toggle the loading overlay
function toggleLoadingOverlay(show) {
    $('#loading-overlay').fadeToggle(show);
}

// Function to perform search based on input and display results
function performSearch(url, searchData) {
    // Check if the search query is at least 3 characters long
    if (searchData.search.length < 3) {
        // Clear the result list
        $("#result_list").empty();
    } else {
        $.ajax({
            type: 'GET',
            url: url,
            data: searchData,
            beforeSend: () => $("#search_icon").toggleClass("fa-circle-notch fa-spin fa-magnifying-glass"), // to show loading icon
            success: (response) => handleSearchResult(response, url, searchData.level),
            error: (xhr, status, error) => console.log("Error:", error),
        });
    }
}

// Function to handle search results
function handleSearchResult(response, url, level) {

    // Check if the response is not empty
    const searchResultsHTML = response && response.length > 0 ?
        // Map the response to a list of links with the url, search query and level
        response.map(result => {
            const query = `${url}?search=${encodeURIComponent(result)}${url === "stories" ? `&level=${encodeURIComponent(level)}` : ""}`;
            return `<a href='${query}' class='list-group-item list-group-item-action search-item'>${result}</a>`;
        }).join("")
        : // Display a message that there are no results
        "<a href='#' class='list-group-item list-group-item-action search-item'>لا توجد نتائج</a>";

    // add the list item to the page
    $("#result_list").html(searchResultsHTML);

    // to hide loading icon
    $("#search_icon").toggleClass("fa-circle-notch fa-spin fa-magnifying-glass");

    // to reset the current focus suggestion item
    currentFocus = -1;
}

// Function to get id and send it to delete popup
function deletePopup(target_id, model, pop_input) {
    $(`#${pop_input}`).val(target_id);
    $(`#${model}`).modal('show');
}

// to reset form and errors
function resetFormAndErrors(formId) {
    const form = $(formId)[0];
    // Reset form inputs
    $(form).trigger('reset');
    // Remove validation error styling and messages
    $(`${formId} input`).removeClass('is-invalid').siblings('.invalid-feedback').children('strong').empty();
}

// Function to handle AJAX requests
function handleAjaxRequest(options) {
    $.ajax({
        method: options.method,
        multipart: options.multipart,
        headers: { Accept: "application/json" },
        data: options.data,
        contentType: false,
        processData: false,
        url: options.url,
        success: options.success,
        error: (response) => {
            if (response.status === 422) { handleValidationErrors(response.responseJSON.errors, options.url) }
            else if (response.status === 401) { handleUnauthorizedError(response.responseJSON.message) }
            else { console.log(response.status) }
        },
        complete: () => toggleLoadingOverlay(false) // Hide loading overlay
    });
}

// Function to handle validation errors
function handleValidationErrors(errors, url) {
    const suffix = url.includes("edit") ? "Edit" : "";

    Object.keys(errors).forEach(function (key) {
        $("#" + key + suffix + "Input").addClass("is-invalid")
            .siblings('.invalid-feedback').children('strong').text(errors[key][0]);
    });
}

// Function to handle unauthorized errors
function handleUnauthorizedError(message) {
    $("#old_passwordInput").addClass("is-invalid")
        .siblings('.invalid-feedback').children('strong').text(message);
}

// Function to handle form submissions and errors
function handleFormSubmission(formSelector, url, successCallback) {

    $(formSelector).submit(function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        var inputElements = $(formSelector + " input");
        inputElements.removeClass("is-invalid").siblings('.invalid-feedback').children('strong').empty();

        // Show loading overlay
        toggleLoadingOverlay(true);

        handleAjaxRequest({
            method: "POST",
            multipart: true,
            data: formData,
            url: url,
            success: function () {
                // Hide loading overlay
                toggleLoadingOverlay(false);
                successCallback();
            }
        });
    });
}


// to call function only when page loaded
$(document).ready(function () {

    // to expand add button on hover
    $('.add-btn').hover(function () {
        $('.add-btn span').toggle();
    });

    // make the width of search result equal to search input
    $('#result_list').width($('.search').width());

    // hide search result
    $("#search_txt").blur(function () {
        setTimeout(function () {
            $('#result_list').hide();
        }, 200);
    });

    // show search result
    $("#search_txt").focus(function () {
        $('#result_list').show();
    });

    // to navigate search suggestion using arrow key
    $("#search_txt").keydown(function (e) {
        switch (e.key) {
            case "ArrowDown":
                Navigate(1);
                break;
            case "ArrowUp":
                Navigate(-1);
                break;
            case "Enter":
                if ($("#result_list .active").length) {
                    e.preventDefault();
                    $("#result_list .active")[0].click();
                }
                break;
            default:
                return; // exit this handler for other keys
        }
    });

    // for suggestion navigation
    var Navigate = function (diff) {
        currentFocus += diff;
        var listItems = $(".search-item");

        if (currentFocus >= listItems.length) {
            currentFocus = 0;
        }
        if (currentFocus < 0) {
            currentFocus = listItems.length - 1;
        }

        // not eq(index) start index form 0
        listItems.removeClass("active").eq(currentFocus).addClass("active");
    };
});

// ************ end of general function section ************

// ************** start of home page **************

// to animate the counts inside the dashboard
function animateCount(element) {
    var target = $(element);
    var count = 0;
    var finalCount = parseInt(target.data('final-count'));
    var duration = 1000; // Animation duration in milliseconds
    var increment = finalCount / (duration / 100); // Increment value based on animation duration

    var interval = setInterval(function () {
        count += increment;
        target.text(Math.floor(count));
        if (count >= finalCount) {
            clearInterval(interval);
            target.text(finalCount); // Set the final count value
        }
    }, 100);

    target.addClass('fade-in'); // Add fade-in animation class
}

// to animate the number oly when its visible to the user view
function checkVisibility() {
    var windowBottom = $(window).scrollTop() + $(window).innerHeight();
    // get all elements with count-animation class and animate them
    $('.count-animation').each(function () {
        var targetTop = $(this).offset().top;
        if ($(this).is(':visible') && !$(this).hasClass('animation-started') && windowBottom > targetTop) {
            animateCount(this);
            $(this).addClass('animation-started');
        }
    });
}

$(document).ready(function () {
    // Bind scroll event to check element visibility
    $(window).scroll(checkVisibility);

    // Check visibility initially
    checkVisibility();
});

// ************** end of home page **************

// ************** start of login page **************

// Function to Hide and Show the password by using the EYE icons
function hidePassword(inputId) {
    const input = document.getElementById(inputId);
    input.type = (input.type === "password") ? "text" : "password";
    $("#hide").toggleClass("fa-eye-slash fa-eye");
}

// ************** end of login page **************

// ************** start of admin page **************
// Function to show the edit admin popup
function editAdmin(admin_id, admin_name, admin_email) {
    $('#edit_admin_id').val(admin_id);
    $('#usernameEditInput').val(admin_name);
    $('#emailEditInput').val(admin_email);
    $('#edit_manager').modal('show');
}

$(document).ready(function () {

    // Change state of the admin
    $('.toggle-class').change(function () {
        var admin_id = $(this).data('id');
        // Convert checked state to 1 or 0
        var locked = $(this).prop('checked') ? 1 : 0;

        // Show loading overlay
        toggleLoadingOverlay(true);

        // Change the value in the database
        $.get('adminChangeLocked', { 'locked': locked, 'admin_id': admin_id })
            .done(function (data) {
                toggleLoadingOverlay(false); // Hide loading overlay
            })
            .fail(function (response) {
                console.log('Error in change state');
                toggleLoadingOverlay(false); // Hide loading overlay
            }
            );

    });

    // Handle form submission for adding manager
    handleFormSubmission("#manager_form", "register", function () {
        window.location.assign("manage");
    });

    // Handle form submission for editing manager
    handleFormSubmission("#edit_manager_form", "editManager", function () {
        window.location.assign("manage");
    });

    // to rest modals form input when the popup closed
    $('#add_manager, #edit_manager, #edit_name_pop, #change_password, #add_story, #edit_story').on('hidden.bs.modal', function () {
        // Get the form ID
        const formId = $(this).find('form').attr('id');

        // Check which modal is being closed
        if ($(this).is('#add_story')) {
            // Clear warning and reset story photo label
            $("#warning_order").empty();
            const spanHTML = '<span class="icon-bordered upload-icon"><i class="fa fa-upload"></i></span>';
            $("#cover_photoLabel").html("اختر صورة لرفعها" + spanHTML);
        } else if ($(this).is('#edit_story')) {
            // Clear warning for edit story modal
            $("#warning_edit_order").empty();
        }

        // Call the resetFormAndErrors function to reset the form and errors
        resetFormAndErrors(`#${formId}`);
    });
})

// ************** end of admin page **************

// ************** start of profile page **************
$(document).ready(function () {
    // Handle form submission for editing profile name
    handleFormSubmission("#edit_name_form", "editName", function () {
        window.location.assign("profile");
    });

    // Handle form submission for changing profile password
    handleFormSubmission("#change_pass", "changePassword", function () {
        window.location.assign("profile");
    });
});
// ************** end of profile page **************

// ************** start of stories page **************

$(document).ready(function () {

    // Handle form submission for add story
    handleFormSubmission("#story_form", "/addStory", function () {
        window.location.reload()
    });

    // Handle form submission for editing story
    handleFormSubmission("#edit_story_form", "/editStory", function () {
        window.location.reload()
    });
});

// function check Last Order
function checkLastOrder(input, warningDiv) {
    // Extract the order value from the input's data attribute
    const orderValue = $(input).data("order");

    // Get the changed value from the input
    const changedValue = $(input).val();

    var warningText = ((changedValue > 0) && changedValue < orderValue) ? `ترتيب القصة الحالي هو ${orderValue}، وعند الحفظ سيتم تعديل ترتيب باقي القصص.` : '';

    // Update the warning div with the warning text
    $(warningDiv).text(warningText);
}

// get the last order then put it in the order field
function getStoryOrder(level, orderInput) {

    $.ajax({
        url: '/getLastOrder',
        method: 'GET',
        data: { level: level },
        dataType: 'json',
        success: function (response) {
            var order = response.lastOrder;
            $(orderInput).val(order + 1).data('order', order + 1);
        },
        error: function (xhr, status, error) {
            console.log(error);
        }
    });
}

// add the story number when add story pop-up is show
$("#add_story").on('shown.bs.modal', function () {
    // get the last order then put it in the order field
    getStoryOrder($('#level').val(), '#story_orderInput');

});

// update the stroy order filed when level select is change
$('#edit_level').change(function () {
    // get the last order then put it in the order field
    getStoryOrder($(this).val(), '#story_orderEditInput');

    // Update the warning div with the warning text
    $('#warning_edit_order').empty();
});

// show the edit story popup
function editStory(story_id, story_name, story_author, story_photo, story_order, level) {

    var spanHTML = '<span class="icon-bordered upload-icon"><i class="fa fa-image"></i></span>';

    // fill all pop-up input with data
    $('#edit_level').val(level);
    $('#edit_story_id').val(story_id);
    $('#story_nameEditInput').val(story_name);
    $('#authorEditInput').val(story_author);
    $('#cover_photoEditLabel').html(story_photo + spanHTML);
    $('#story_orderEditInput').val(story_order);
    $("#story_orderEditInput").data("order", story_order);
    $('#edit_story').modal('show');
}

// to get the file name in the label and change the upload icon to photo icon
function updateLabelName(label, input) {
    // get the photo name and put it in the label
    var file = input.files[0];

    var spanHTML = '<span class="icon-bordered upload-icon"><i class="fa fa-image"></i></span>';

    if (file) {
        // add the photo icon and text to lable
        $(label).html(file.name + spanHTML);
        // to remove the error message if its exist
        $(input).siblings('.invalid-feedback').children('strong').empty();
    }
}

// ************** end of stories page **************

// ************** start of story slide page **************
$(document).ready(function () {

    // add new slide
    $('.add-slide-btn').click(function () {
        $('#slide_image').attr('src', "storage/upload/slides_photos/img_upload.svg");
        $('#slide_audio').attr('src', "");
        $('#slide_text').text("أدخل النص هنا");

        $('#edit-photo').attr('onclick', "addPhoto()");
        $('#replace_sound').attr('onclick', "addSound()");
        $('#edit_text_icon').attr('onclick', "addText()");


        $('.add-slide-btns').html(
            '<button type="button" class="btn save" id="add_slide" onclick="saveSlide()">حفظ</button>' +
            '<input type="button" onclick="closeSlide()" class="cancel slide-cancel btn btn-secondary" value="إلغاء">'
        );

        $("#icon_text").text("إضافة");

        $("#error-image-message").text("");
        $("#error-audio-message").text("");
        $("#error-text-message").text("");

    });

    // make the last slide active 
    $('.card_slide:last').addClass('active');
});

// for add slide actions 
function addFile(type) {
    // Create an input field of type "file"
    var input = $("<input />", { type: "file", accept: type + "/*", name: "add_slide_" + type, id: "add_slide_" + type, style: "display: none;" });

    // Trigger a click event on the input field
    input.trigger("click");

    // Listen for a change event on the input field
    input.change(function () {

        var file = this.files[0];

        // Check if the selected file is correct type
        if (file.type.match(type + '.*')) {

            var reader = new FileReader();
            reader.onload = function (event) {
                // Set the src attribute of the image tag to the data URL of the selected image
                $("#slide_" + type).attr("src", event.target.result.toString());
            };
            reader.readAsDataURL(file);
        } else {
            alert("Please select a " + type.toUpperCase() + " file.");
        }
    });
    $("#" + type + "Input").html(input);
}

function addPhoto() {
    addFile("image");
}

function addSound() {
    addFile("audio");
}

function addText() {
    var $element = $('#slide_text');

    var current_text = ($element.text() == "أدخل النص هنا") ? "" : $element.text();


    var $input = $('<input>').attr('type', 'text').attr('id', 'slide_input').val(current_text);
    $input.addClass('form-control');

    $element.replaceWith($input);
    $input.focus();

    $input.blur(function () {
        var current_text = ($.trim($input.val()) == "") ? "أدخل النص هنا" : $input.val();

        var $newElement = $('<div>').attr('id', 'slide_text').text(current_text);
        $input.replaceWith($newElement);
    });

}

$('#add_slide').on('click', function (event) {
    var errorMessage = $('#error-image-message');

    // Check if error message is visible
    if (errorMessage.is(':visible')) {
        // Scroll to the error message
        $('html, body').animate({
            scrollTop: errorMessage.offset().top
        }, 1000);
        event.preventDefault(); // Prevent form submission
    }
});

// save slide 
function saveSlide() {

    // Get the selected input data
    var photoInput = $("#add_slide_image")[0];
    var soundInput = $("#add_slide_audio")[0];
    var textElement = $("#slide_text");
    var photoMessage = $("#error-image-message");
    var soundMessage = $("#error-audio-message");
    var textMessage = $("#error-text-message");

    // console.log(photoInput);

    // validate from image 
    if (!photoInput || !photoInput.files || !photoInput.files.length > 0) {


        // Scroll to the error message after it's shown
        document.querySelector('#edit-photo').scrollIntoView({ behavior: 'smooth' });
        photoMessage.text("لطفا قم بإختيار الصورة").append('<i class="fa fa-close close-btn" onclick="deleteText()"></i>');
        return;
    }


    var photoFile = photoInput.files[0];

    // validate from sound
    if (!soundInput || !soundInput.files || !soundInput.files.length > 0) {
        // Access input.files safely here
        soundMessage.text("لطفا قم بإختيار الصوت");
        return;
    }

    var soundFile = soundInput.files[0];

    // Get the contents of the "story-content" div
    var slideText = textElement.html();
    if (slideText == '' || slideText == 'أدخل النص هنا') {
        photoMessage.empty();
        soundMessage.text("");
        textMessage.text("لطفا قم بإدخال النص");
        return;
    }

    // Create a new FormData object
    var formData = new FormData();

    // Append the selected image file to the form data
    formData.append('image', photoFile);

    // Append the selected audio file to the form data
    formData.append('audio', soundFile);

    // Append the contents of the "slide text" div to the form data
    formData.append('text', slideText);
    // get story id 
    var story_id = $('#story_id').text();

    // Show loading overlay
    toggleLoadingOverlay(true);

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/addNewSlide?story_id=' + story_id,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,

        success: function (response) {
            // Hide loading overlay
            toggleLoadingOverlay(false);
            // Handle the response from the server
            location.reload();
        },
        error: function (response) {
            // Hide loading overlay
            toggleLoadingOverlay(false);

            // Handle errors
            if (response.status === 422) {
                // clear all error messages first
                $("[id^='error-']").empty();
                let errors = response.responseJSON.errors;
                Object.keys(errors).forEach(function (key) {
                    $("#error-" + key + "-message").text(errors[key][0]);
                    if (key == 'image') {
                        $("#error-" + key + "-message").text(errors[key][0]).append('<i class="fa fa-close close-btn" onclick="deleteText()"></i>');;
                    }
                });
            } else {
                console.log(response.status);
                // window.location.reload();
            }
            // alert('Error saving slide.');
        }
    });
}

// for editing slide 
function editMedia(type, url) {
    // Create an input field of type "file"
    var input = $("<input />", { type: "file", accept: type + "/*", style: "display: none;" });
    // Trigger a click event on the input field
    input.trigger("click");

    // Listen for a change event on the input field
    input.change(function () {

        var formData = new FormData();
        formData.append(type, this.files[0]);

        // this for edit slide photo and audio
        if (url == "/editSlideImage" || url == "/editSlideAudio") {
            var id = $('#slide_id').text();
            formData.append('id', id);
        }

        // Show loading overlay
        toggleLoadingOverlay(true);

        // Send an AJAX request to the server to upload the image
        $.ajax({
            headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                if (type === "image" && url == "/editSlideImage") {
                    // update main slid image
                    $("#slide_image").attr("src", data.url);

                    // update aside slid imag
                    $('#image' + id).attr('src', data.url);

                } else if (type === "audio") {
                    // This ensures that the browser loads the new version of the audio file instead of using a cached version.
                    //we append a cache-busting parameter to the URL by adding ?_= followed by the current timestamp using new Date().getTime(). 
                    $("#slide_audio").attr("src", data.url + "?" + (new Date()).getTime());
                }
                else if (type === "image" && url == "/editProfilePhoto") {
                    // Update the nav profile photo
                    $('#round-profile').attr('src', data.thumbUrl);
                    // Update the preview image with the new URL
                    $('#profile_photo').attr('src', data.url);
                }
                // empty error message
                $('#error-' + type + '-message').text("");

            },
            error: function (xhr) {
                // Hide loading overlay
                toggleLoadingOverlay(false);
                // Get the first error message
                var errors = xhr.responseJSON.errors;

                if (errors) {
                    // Get the first error message
                    var errorMsg = Object.values(errors)[0][0];
                    // Display the error message
                    $("#error-" + type + "-message").text(errorMsg);

                    if (url = '/editSlideImage') {
                        $("#error-" + type + "-message").append('<i class="fa fa-close close-btn" onclick="deleteText()"></i>');
                    }
                }
            }
        });
    });
}

function editText() {
    var $element = $('#slide_text');

    var $input = $('<input>').attr('type', 'text').attr('id', 'slide_input').val($element.text());
    $input.addClass('form-control');

    $element.replaceWith($input);
    $input.focus();

    $input.blur(function () {
        var newText = $input.val();
        var id = $('#slide_id').text();

        // Show loading overlay
        toggleLoadingOverlay(true);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            url: '/editSlideText?id=' + id,
            type: 'POST',
            data: {
                text: newText
            },
            success: function (data) {
                // Hide loading overlay
                toggleLoadingOverlay(false);


                var $newElement = $('<div>').attr('id', 'slide_text').text($input.val());
                $input.replaceWith($newElement);
                $('#text' + id).text($input.val());
                $('#error-text-message').text('');
            },
            error: function (xhr, textStatus, error) {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                var errors = xhr.responseJSON.errors;
                if (errors) {
                    // Get the first error message
                    var errorMessage = Object.values(errors)[0][0];
                    $('#error-text-message').text(errorMessage);
                    // ther is some error to add botostrap stylt
                    // $('#slide_input').addClass('is-invalid');
                }
            }
        });

    });
}

// to delete photo error message when user click on the close icon
function deleteText() {
    document.getElementById("error-image-message").innerHTML = "";
}

function closeSlide() {
    $('.card_slide:last').click();
}
// ************** end of story slide page **************