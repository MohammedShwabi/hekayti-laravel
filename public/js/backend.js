// ************ start of general function section ************

// initialize var for search section
var currentFocus = -1;

// Function to toggle the loading overlay
function toggleLoadingOverlay(show) {
    $('#loading-overlay').fadeToggle(show);
}

// to expand add button on hover
$(document).ready(function () {
    $('.add-btn').hover(function () {
        $('.add-btn span').show();
    }, function () {
        $('.add-btn span').hide();
    });
});

// to call function only when page loaded
$(document).ready(function () {
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

// general search function to search for story and admin
function searchInput(url, options) {
    // also check if the search text is less than 3 characters
    if (options.search.length < 3) {
        $("#result_list").empty();
        return;
    }
    $.ajax({
        type: 'GET',
        url: url,
        data: options,
        beforeSend: function () {
            // to show loading icon
            $("#search_icon").addClass('fa-circle-notch fa-spin').removeClass('fa-magnifying-glass');
        },
        success: function (response) {

            if (response && response.length > 0) {

                var html = response.map(function (item) {

                    var href = `${url}?search=${encodeURIComponent(item)}` + (url === 'stories' ? `&level=${encodeURIComponent(options.level)}` : '');

                    return `<a href='${href}' class='list-group-item list-group-item-action search-item'>${item}</a>`;
                }).join('');
            }
            else {
                html = "<a href='#' class='list-group-item list-group-item-action search-item'>لا توجد نتائج</a>";
            }

            // add the list item to the page
            $("#result_list").html(html);

            // to hide loading icon
            $("#search_icon").addClass('fa-magnifying-glass').removeClass('fa-circle-notch fa-spin');

            // to reset the current focus suggestion item
            currentFocus = -1;
        },
        error: function (xhr, status, error) {
            console.log('Error:', error);
        }
    });
}

// ************ end of general function section ************

// ************** start of home page **************

$(document).ready(function () {

    // get all elements with count-animation class to animate it later
    var animatedElements = $('.count-animation');

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
        animatedElements.each(function () {
            var targetTop = $(this).offset().top;
            if ($(this).is(':visible') && !$(this).hasClass('animation-started') && windowBottom > targetTop) {
                animateCount(this);
                $(this).addClass('animation-started');
            }
        });
    }

    // Bind scroll event to check element visibility
    $(window).scroll(checkVisibility);

    // Check visibility initially
    checkVisibility();
});

// ************** end of home page **************

// ************** start of login page **************

// for Hide and Show the password by using the EYE icons
function hidePassword(target) {
    var input = document.getElementById(target);

    if (input.type === 'password') {
        input.type = "text";
        $("#hide").addClass("fa-eye-slash").removeClass("fa-eye");
    } else {
        input.type = "password";
        $("#hide").addClass("fa-eye").removeClass("fa-eye-slash");
    }
}

// ************** end of login page **************

// ************** start of admin page **************

// to get id of admin and send it to delete popup form
function deleteAdmin(admin_id) {
    $('#admin_id').val(admin_id);
    $('#delete_manager').modal('show');
}

// show the edit admin popup
function editAdmin(admin_id, admin_name, admin_email) {
    id = admin_id;
    Aname = admin_name;
    email = admin_email;
    $('#edit_admin_id').val(id);
    $('#nameEditInput').val(Aname);
    $('#emailEditInput').val(email);
    // $('#passwordEditInput').val(admin_name);
    $('#edit_manager').modal('show');
}

$(function () {

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
                // Hide loading overlay
                toggleLoadingOverlay(false);
            })
            .fail(function (response) {
                // Hide loading overlay
                toggleLoadingOverlay(false);
            });
    });


    // add manager pop up
    $('#manager_form').submit(function (e) {

        e.preventDefault();
        // Serialize form data
        let formData = $(this).serializeArray();

        // get the input
        var managerInput = $("#manager_form input");

        // Clear previous error messages
        managerInput.toggleClass("is-invalid", false).siblings('.invalid-feedback').children('strong').empty();

        // Show loading overlay
        toggleLoadingOverlay(true);

        $.ajax({
            method: "POST",
            headers: {
                Accept: "application/json"
            },
            url: "register",
            data: formData,
            success: () => {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                // Redirect to admin page
                window.location.assign("manage")
            },
            error: (response) => {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                if (response.status === 422) {
                    // Handle validation errors
                    let errors = response.responseJSON.errors;

                    Object.keys(errors).forEach(function (key) {
                        $("#" + key + "Input").addClass("is-invalid");
                        $("#" + key + "Error").children("strong").text(errors[key][0]);
                    });
                } else {
                    // Handle other error cases
                    console.log(response.status);
                }
            }
        })
    });

    // to rest add admin popup when the popup closed
    $('#add_manager').on('hidden.bs.modal', function () {

        // empty all input in the form
        $('#manager_form')[0].reset();

        // remove invalid error message from all input
        $("#manager_form input").removeClass("is-invalid").siblings('.invalid-feedback').children('strong').empty();

    });

    // edit manager pop up
    $('#edit_manager_form').submit(function (e) {
        e.preventDefault();

        // Serialize form data
        let formData = $(this).serializeArray();

        // get the input
        var managerEditInput = $("#edit_manager_form input");

        // Clear previous error messages
        managerEditInput.toggleClass("is-invalid", false).siblings('.invalid-feedback').children('strong').empty();

        // Show loading overlay
        toggleLoadingOverlay(true);

        $.ajax({
            method: "POST",
            headers: {
                Accept: "application/json"
            },
            url: "editManager",
            data: formData,
            success: () => {
                // Hide loading overlay
                toggleLoadingOverlay(false);
                // Redirect to admin page
                window.location.assign("manage")
            },
            error: (response) => {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                if (response.status === 422) {
                    // Handle validation errors
                    let errors = response.responseJSON.errors;

                    console.log(errors);

                    Object.keys(errors).forEach(function (key) {
                        $("#" + key + "EditInput").addClass("is-invalid");
                        $("#" + key + "EditError").children("strong").text(errors[key][0]);
                        console.log(key + "EditError");

                    });
                } else {
                    // Handle other error cases
                    console.log(response.status);
                }
            }
        })
    });

    // to rest edit admin popup when the popup closed
    $('#edit_manager').on('hidden.bs.modal', function () {

        // empty all input in the form
        $('#edit_manager_form')[0].reset();

        // remove invalid error message from all input
        $("#edit_manager_form input").removeClass("is-invalid").siblings('.invalid-feedback').children('strong').empty();

    });

})

// ************** end of admin page **************

// ************** start of profile page **************
// to edit name 
$('#edit_name_form').submit(function (e) {
    e.preventDefault();

    // Serialize form data
    var formData = $(this).serializeArray();

    // get the input
    var nameInput = $('#edit_name_form #nameInput');

    // remove invalid error message input
    nameInput.removeClass('is-invalid').siblings('.invalid-feedback').children('strong').empty();

    // Show loading overlay
    toggleLoadingOverlay(true);

    $.ajax({
        method: "POST",
        headers: {
            Accept: "application/json"
        },
        url: "editName",
        data: formData,
        success: function () {
            // Hide loading overlay
            toggleLoadingOverlay(false);

            // Redirect to profile page
            window.location.assign("profile");
        },
        error: function (response) {
            // Hide loading overlay
            toggleLoadingOverlay(false);

            if (response.status === 422) {
                // Handle validation errors
                var errors = response.responseJSON.errors;
                nameInput.addClass('is-invalid').siblings('.invalid-feedback').children('strong').text(errors.name[0]);

            } else {
                console.log(response.status);
                // Handle other error cases
                // window.location.reload();
            }
        }
    });
});

// to change password
$('#change_pass').submit(function (e) {
    e.preventDefault();

    // Serialize form data
    var formData = $(this).serializeArray();

    // get the input
    var changePassInput = $("#change_pass input");

    // remove invalid error message input
    changePassInput.toggleClass("is-invalid", false).siblings('.invalid-feedback').children('strong').empty();

    // Show loading overlay
    toggleLoadingOverlay(true);

    $.ajax({
        method: "POST",
        headers: {
            Accept: "application/json"
        },
        url: "changePassword",
        data: formData,
        success: function () {
            // Hide loading overlay
            toggleLoadingOverlay(false);

            // Redirect to profile page
            window.location.assign("profile");
        },
        error: function (response) {
            // Hide loading overlay
            toggleLoadingOverlay(false);

            if (response.status === 422) {
                // Handle validation errors
                var { responseJSON: { errors } } = response;
                Object.keys(errors).forEach(function (key) {
                    $("#" + key + "Input").addClass("is-invalid");
                    $("#" + key + "Error").children("strong").text(errors[key][0]);
                });
            }
            if (response.status === 401) {
                // Handle unauthorized error
                let message = response.responseJSON.message;
                $("#old_passwordInput").addClass("is-invalid");
                $("#old_passwordError").children("strong").text(message);
            } else {
                console.log(response.status);
                // Handle other error cases
                // window.location.reload();
            }
        }
    });
});

// edit profile photo 
$('#edit_profile_photo').click(function () {
    // Create an input field of type "file" and make it hidden
    var input = $('<input>', {
        type: 'file',
        accept: 'image/*',
        style: 'display: none;'
    }).change(function () {
        // Prepare form data with the selected image file
        var formData = new FormData();
        formData.append("image", this.files[0]);

        // Show loading overlay
        toggleLoadingOverlay(true);

        // Send an AJAX request to upload the image
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "editProfilePhoto",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                // Update the nav profile photo
                $('#round-profile').attr('src', data.thumbUrl);
                // Update the preview image with the new URL
                $('#profile_photo').attr('src', data.url);

                // Clear any error messages
                $('#error-profile-photo').text("");
            },
            error: function (xhr, status, error) {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                var errors = xhr.responseJSON.errors;

                if (errors) {
                    // Get the first error message
                    var errorMessage = Object.values(errors)[0][0];
                    // Display the error message
                    $('#error-profile-photo').text(errorMessage);
                }
            }
        });
    }).trigger('click');
});

// to rest edit name popup when the popup closed
$("#edit_name_pop").on('hidden.bs.modal', function () {

    // empty all input in the form
    $('#edit_name_form')[0].reset();

    // remove invalid error message from all input
    $('#edit_name_form input').removeClass('is-invalid').siblings('.invalid-feedback').children('strong').empty();

});

// to rest change password popup when the popup closed
$("#change_password").on('hidden.bs.modal', function () {

    // empty all input in the form
    $('#change_pass')[0].reset();

    // remove invalid error message from all input
    $('#change_pass input').removeClass('is-invalid').siblings('.invalid-feedback').children('strong').empty();

});

// ************** end of profile page **************

// ************** start of stories page **************

$(document).ready(function () {

    // add story pop up
    $('#story_form').submit(function (e) {
        e.preventDefault();

        // get the input
        var storyInput = $("#story_form input");

        // Clear previous error messages
        storyInput.toggleClass("is-invalid", false).siblings('.invalid-feedback').children('strong').empty();

        // Show loading overlay
        toggleLoadingOverlay(true);

        $.ajax({
            method: "POST",
            multipart: true,
            headers: {
                Accept: "application/json"
            },
            data: new FormData(this),
            contentType: false,
            processData: false,
            url: "addStory",
            success: () => {
                // Hide loading overlay
                toggleLoadingOverlay(false);
                // Redirect to admin page
                window.location.reload()
            },
            error: (response) => {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                if (response.status === 422) {
                    // Handle validation errors
                    let errors = response.responseJSON.errors;

                    // loop throw the error and display them
                    Object.keys(errors).forEach(function (key) {
                        $("#" + key + "Input").addClass("is-invalid");
                        $("#" + key + "Error").children("strong").text(errors[key][0]);
                    });
                } else {
                    // Handle other error cases
                    console.log(response.status);
                }
            }
        })
    });

    // edit story pop up
    $('#edit_story_form').submit(function (e) {
        e.preventDefault();
        $(".invalid-feedback").children("strong").text("");
        $("#edit_story_form input").removeClass("is-invalid");

        // Show loading overlay
        toggleLoadingOverlay(true);

        $.ajax({
            method: "POST",
            multipart: true,
            // processData: false,
            headers: {
                Accept: "application/json"
            },
            data: new FormData(this),
            contentType: false,
            processData: false,
            url: "editStory",
            success: () => {
                // Hide loading overlay
                toggleLoadingOverlay(false);
                window.location.reload()
            },
            error: (response) => {
                // Hide loading overlay
                toggleLoadingOverlay(false);
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;
                    Object.keys(errors).forEach(function (key) {
                        $("#" + key + "EditInput").addClass(
                            "is-invalid");
                        $("#" + key + "EditError").children(
                            "strong").text(errors[
                                key][0]);
                    });
                } else {
                    console.log(response.status);
                    // window.location.reload();
                }
            }
        })
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

    $.get('get-last-order', { level: level })
        .done(function (order) {
            // Set the value of the input field to the last order value returned by the server
            $(orderInput).val(order + 1).data('order', order + 1);
        })
        .fail(function (xhr, status, error) {
            // Handle any errors that may occur
            console.log(error);
        });
}

// add the story no when add story pop-up is show
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

// to get id of story and send it to delete popup form
function deleteStory(story_id) {
    $('#story_id').val(story_id);
    $('#delete_story').modal('show');
}

// to get id of story and send it to delete popup form
function publishStory(story_id) {
    $('#publish_story_id').val(story_id);
    $('#publish_story').modal('show');
}

// show the edit story popup
function editStory(story_id, story_name, story_author, story_photo, story_order, level) {

    var spanHTML = '<span class="icon-bordered upload-icon"><i class="fa fa-image"></i></span>';

    // fill all pop-up input with data
    $('#edit_level').val(level);
    $('#edit_story_id').val(story_id);
    $('#nameEditInput').val(story_name);
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

// to rest add story popup when closed
$("#add_story").on('hidden.bs.modal', function () {
    // empty all input in the form
    $('#story_form')[0].reset();
    // remove invalid error message from all input
    $('#story_form input').removeClass('is-invalid').siblings('.invalid-feedback').children('strong').empty();
    $("#warning_order").empty();

    // rest the story photo lable
    var spanHTML = '<span class="icon-bordered upload-icon"><i class="fa fa-upload"></i></span>';
    $("#cover_photoLabel").html("اختر صورة لرفعها" + spanHTML);
});

// to rest edit story popup when closed
$("#edit_story").on('hidden.bs.modal', function () {
    // empty all input in the form
    $('#edit_story_form')[0].reset();
    // remove invalid error message from all input
    $('#edit_story_form input').removeClass('is-invalid').siblings('.invalid-feedback').children('strong').empty();
    $("#warning_edit_order").empty();
});

// ************** end of stories page **************

// ************** start of story slide page **************

function deleteSlide(thisSlide) {
    var slideId = $(thisSlide).data('id');
    $('#del_slide_id').val(slideId);
    $('#delete_slide').modal('show');
}

$(document).ready(function () {
    // show the delete slide popup
    // $('.delete-slide').click(function () {
    //     var slideId = $(this).data('id');
    //     $('#del_slide_id').val(slideId);
    //     $('#delete_slide').modal('show');
    // });

    // add new slide
    $('.add-slide-btn').click(function () {
        $('#slide_imge').attr('src', "upload/slides_photos/img_upload.svg");
        $('#slide_sound').attr('src', "");
        $('#slide_text').text("أدخل النص هنا");

        $('#edit-photo').attr('onclick', "addPhoto()");
        $('#replace_sound').attr('onclick', "addSound()");
        $('#edit_text_icon').attr('onclick', "addText()");
        // edit here

        $('.add-slide-btns').html(
            '<button type="button" class="btn save" id="add_slide" onclick="saveSlide()">حفظ</button>' +
            '<input type="button" onclick="closeSlide()" class="cancel slide-cancel btn btn-secondary" value="إلغاء">'
        );
        // $('.add-slide-btns').html(
        //     '<button type="button" class="btn save" id="add_slide" onclick="saveSlide()">حفظ</button>' +
        //     '<input type="reset" class="cancel slide-cancel btn btn-secondary" value="إلغاء">'
        // );


        $("#icon_text").text("إضافة");

        $("#error-photo-message").text("");
        $("#error-sound-message").text("");
        $("#error-text-message").text("");

    });

    // make the last slide active 
    $('.card_slide:last').addClass('active');
});

// for add slide actions 
function addPhoto() {
    // Create an input field of type "file"
    var input = $('<input />', {
        type: 'file',
        accept: 'image/*',
        name: 'add_slide_imge',
        id: 'add_slide_imge',
        style: 'display: none;',

    });

    // Trigger a click event on the input field
    input.trigger('click');

    // Listen for a change event on the input field
    input.change(function () {
        var file = this.files[0];


        // Check if the selected file is an image
        if (!file.type.match('image.*')) {
            alert('Please select an image file.');
            return;
        }

        // Check if the selected file belongs to a specific file type
        if (file.type !== 'image/png' && file.type !== 'image/jpeg') {
            alert('Please select a PNG or JPEG image file.');
            return;
        }

        var reader = new FileReader();

        reader.onload = function (event) {
            // Set the src attribute of the image tag to the data URL of the selected image
            $('#slide_imge').attr('src', event.target.result.toString());
        };

        reader.readAsDataURL(file);
    });
    $('#imginput').html(input);
}

function addSound() {
    // Create an input field of type "file"
    var input = $('<input />', {
        type: 'file',
        accept: 'audio/*',
        name: 'add_slide_audio',
        id: 'add_slide_audio',
        style: 'display: none;'
    });
    // Trigger a click event on the input field
    input.trigger('click');

    // Listen for a change event on the input field
    input.change(function () {
        var file = this.files[0];


        // Check if the selected file is an image
        if (!file.type.match('audio.*')) {
            alert('Please select an image file.');
            return;
        }

        // Check if the selected file belongs to a specific file type
        // if (file.type !== 'image/png' && file.type !== 'image/jpeg') {
        //     alert('Please select a PNG or JPEG image file.');
        //     return;
        // }
        // $('#error-sound-message').text("");
        var reader = new FileReader();

        reader.onload = function (event) {
            // Set the src attribute of the image tag to the data URL of the selected image
            $('#slide_sound').attr('src', event.target.result);
        };

        reader.readAsDataURL(file);
    });

    $('#soundinput').html(input);
}

function addText() {
    var $element = $('#slide_text');

    var current_text = ($element.text() == "أدخل النص هنا") ? "" : $element.text();

    // edit here
    var $input = $('<input>').attr('type', 'text').attr('id', 'slide_input').val(current_text);
    $input.addClass('form-control');

    $element.replaceWith($input);
    $input.focus();

    $input.blur(function () {
        var current_text = ($.trim($input.val()) == "") ? "أدخل النص هنا" : $input.val();
        // edit here
        var $newElement = $('<div>').attr('id', 'slide_text').text(current_text);
        $input.replaceWith($newElement);
        // $('#error-text-message').text('');
    });

}

$('input[type="submit"]').on('click', function (event) {
    var errorMessage = $('#error-photo-message');

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
    // Get the selected image data

    // Get the selected image data
    var fileInput = $('#add_slide_imge')[0];
    // validaite from image 
    if (!fileInput || !fileInput.files || !fileInput.files.length > 0) {

        // Scroll to the error message after it's shown
        document.querySelector('#edit-photo').scrollIntoView({ behavior: 'smooth' });
        $("#error-photo-message").text("لطفا قم بإختيار الصورة").append('<i class="fa fa-close close-btn" onclick="deleteText()"></i>');

        return;
    }
    var img = fileInput.files[0];

    // Get the selected audio file
    var audioInput = $('#add_slide_audio')[0];
    // validaite from sound
    if (!audioInput || !audioInput.files || !audioInput.files.length > 0) {
        // Access input.files safely here
        $("#error-sound-message").text("لطفا قم بإختيار الصوت");
        // alert('eror in chose sound');
        return;
    }
    var audio = audioInput.files[0];

    // Get the contents of the "story-content" div
    var slideText = $('#slide_text').html();
    if (slideText == '' || slideText == 'أدخل النص هنا') {
        // alert('eror in text');
        // $("#error-photo-message").text("");
        $("#error-photo-message").empty();
        $("#error-sound-message").text("");
        $("#error-text-message").text("لطفا قم بإدخال النص");
        return;
    }

    // Create a new FormData object
    var formData = new FormData();

    // Append the selected image file to the form data
    formData.append('photo', img);

    // Append the selected audio file to the form data
    formData.append('sound', audio);

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
        url: 'addNewSlide?story_id=' + story_id,
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
                    if (key == 'photo') {
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
function editPhoto() {
    // Create an input field of type "file"
    var input = $('<input />', {
        type: 'file',
        accept: 'image/*',
        style: 'display: none;'
    });

    // Trigger a click event on the input field
    input.trigger('click');

    // Listen for a change event on the input field
    input.change(function () {
        var id = $('#slide_id').text();
        var formData = new FormData();
        formData.append("photo", this.files[0]);

        // Show loading overlay
        toggleLoadingOverlay(true);

        // Send an AJAX request to the server to upload the image
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "editSlidePhoto?id=" + id,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                // Update the preview image with the new URL
                $('#slide_imge').attr('src', data.url);
                $('#image' + id).attr('src', data.url);
                // alert("Image uploaded successfully!");
                $('#error-photo-message').text("");
            },
            error: function (xhr, status, error) {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                var errors = xhr.responseJSON.errors;
                if (errors) {
                    // Get the first error message
                    var errorMessage = Object.values(errors)[0][0];
                    //here to customaize the error message of fiald of uploud image
                    $('#error-photo-message').text(errorMessage);
                }
            }
        });
    });
}

function editSound() {
    // Create an input field of type "file"
    var input = $('<input />', {
        type: 'file',
        accept: 'audio/*',
        style: 'display: none;'
    });

    // Trigger a click event on the input field
    input.trigger('click');

    // Listen for a change event on the input field
    input.change(function () {
        //get id of the slide
        var id = $('#slide_id').text();
        var formData = new FormData();
        formData.append("sound", this.files[0]);

        // Show loading overlay
        toggleLoadingOverlay(true);

        // Send an AJAX request to the server to upload the audio
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "editSlideSound?id=" + id,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                // Update the preview image with the new URL
                //we append a cache-busting parameter to the URL by adding ?_= followed by the current timestamp using new Date().getTime(). 
                // This ensures that the browser loads the new version of the audio file instead of using a cached version.
                $('#slide_sound').attr('src', data.url + '?_=' + new Date().getTime());
                $('#error-sound-message').text("");
            },
            error: function (xhr, status, error) {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                var errors = xhr.responseJSON.errors;
                if (errors) {
                    // Get the first error message
                    var errorMessage = Object.values(errors)[0][0];
                    $('#error-sound-message').text(errorMessage);
                }
            }
        });
    });


}

function editText() {
    var $element = $('#slide_text');
    // edit here
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

            url: 'editSlideText?id=' + id,
            type: 'POST',
            data: {
                text: newText
            },
            success: function (data) {
                // Hide loading overlay
                toggleLoadingOverlay(false);

                // edit here
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
    document.getElementById("error-photo-message").innerHTML = "";
}

function closeSlide() {
    $('.card_slide:last').click();
}

// ************** end of story slide page **************