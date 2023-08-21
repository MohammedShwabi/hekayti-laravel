
$(document).ready(function () {

    // Get the new order of the slides
    function getNewOrder() {
        var newOrder = [];
        $('#sortable .card_slide').each(function () {
            newOrder.push($(this).data('slide-id'));
        });
        return newOrder;
    }

    // Send the new order to the server using an AJAX request
    function updateSlideOrder() {
        $.ajax({
            url: '/updateSlideOrder',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { slideOrder: getNewOrder() },
            success: function (response) {
                // Handle success, if needed
            },
            error: function (error) {
                // Handle error, if needed
            }
        });
    }

    $('#sortable').sortable({
        ghostClass: "sortable-ghost",  // Class name for the drop placeholder
        onUpdate: updateSlideOrder,
    });
    
});

