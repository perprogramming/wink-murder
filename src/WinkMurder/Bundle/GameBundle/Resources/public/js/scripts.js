$(function() {

    $('.fx-autosubmit').each(function() {
        var form = $(this);
        form.find('input').change(function() {
            form.submit();
        });
    });

});