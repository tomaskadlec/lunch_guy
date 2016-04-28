/** Handles asynchronous displaying of the data */
$('document').ready(function () {
    $('div.restaurant').each(function(index, element) {
        var url = $(this).attr('data-url');
        $.get(url, function(response) {
            element.innerHTML = response;
        });
    })
});