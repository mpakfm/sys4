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
    });
    $('.js-delete-el').click(function(){
        let id   = $(this).data('id');
        let link = $(this).data('link');
        if (!id || !link) {
            return false;
        }
        window.location = link + id;
    });
    $('.js-edit-el').click(function(){
        let id   = $(this).data('id');
        let link = $(this).data('link');
        if (!id || !link) {
            return false;
        }
        window.location = link + id;
    });
});
