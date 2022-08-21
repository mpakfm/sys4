document.addEventListener("DOMContentLoaded", function(event) {
    $('.js-select-all').click(function(){
        let checked = $(this).prop('checked');
        let parentForm = $(this).parents('form');
        let checkboxes = parentForm.find('.js-select-el');
        if (checked) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    })
});
