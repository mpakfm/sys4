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
    $('#js-on-page-selector').on('change', function (e) {
        var valueSelected = this.value;
        let query = changeQueryString('limit', valueSelected);
        window.location = query;
    });
});

function changeQueryString(param, value) {
    let res;
    let query = window.location.href;
    if (query.indexOf('?') < 0) {
        query += '?';
    }
    if (query.indexOf(param) < 0) {
        res = query + '&' + param + '=' + value;
    } else {
        let str     = '';
        let replace = '';
        switch (param) {
            case "limit":
                str = /limit=(\d+)/;
                replace = 'limit=' + value;
            break;
            case "offset":
                str = /offset=(\d+)/;
                replace = 'offset=' + value;
                break;
        }
        res = query.replace(str, replace);
    }
    return res;
}
