$(document).ready(function() {

    $('.js-popup').on('click', function(){
        // listen click, open modal and .load content
        $($(this).data('target')).modal('show')
            .find('.modalContent')
            .load($(this).attr('data-url'));
        return false;
    });
});