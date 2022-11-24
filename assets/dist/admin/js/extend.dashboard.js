document.addEventListener("DOMContentLoaded", function(event) {
    $('.js-select-all').click(function(){
        let selectType = $('#js-select-all-type').val();
        let checked    = $(this).prop('checked');
        let parentForm = $(this).parents('form');
        let checkboxes = parentForm.find('.js-select-el');
        if (selectType == 'all') {
        if (checked) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
        } else if (selectType == 'invert') {
            for (var i = 0; i < checkboxes.length; i++) {
                let item = $(checkboxes)[i];
                if ($(item).prop('checked')) {
                    $(item).prop('checked', false);
                } else {
                    $(item).prop('checked', true);
                }
            }
        }
    });
    $('#js-select-all-action').on('change', function(event){
        if ($(this).val() === 'delete') {
            deleteSelected(event);
        }
    });
    $('.js-delete-el').click(function(){
        let id   = $(this).data('id');
        let link = $(this).data('link');
        if (!id || !link) {
            return false;
        }
        let url = link + id;
        $.ajax({
            url: url,
            method: 'get',
            success: (data) => { window.location.reload(); },
            error: (data) => { console.log('error data', data) }
        });
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
        window.location   = changeQueryString('limit', valueSelected);
    });
    $('.js-order').click(function(){
        let res;
        let sort  = $(this).data('sort');
        let order = ($(this).data('order') === 'desc' ? 'desc' : 'asc');
        let query = window.location.href;
        if (query.indexOf('?') < 0) {
            query += '?';
        }
        if (query.indexOf('sort') < 0) {
            res = query + '&sort=' + sort + '&order=' + order;
        } else {
            str     = /sort=(\w+)/;
            replace = `sort=${sort}`;
            query   = query.replace(str, replace);
            str     = /order=(\w+)/;
            replace = `order=${order}`;
            res     = query.replace(str, replace);
        }
        let parent = $(this).parents('tr');
        $(parent).find('i.fas').attr('class', 'fas fa-sort');
        if (order === 'desc') {
            $(this).data('order', 'desc');
            $(this).attr('data-order', 'desc');
            $(this).find('i').attr('class', 'fas fa-sort-down');
        } else {
            $(this).data('order', 'asc');
            $(this).attr('data-order', 'asc');
            $(this).find('i').attr('class', 'fas fa-sort-up');
        }
        window.location = res;
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

function deleteSelected(event) {
    let result = confirm('Точно всё выбранное удалить?');
    if (!result) {
        setTimeout(function () {
            $('#js-select-all-action option:selected').each(function(){
                this.selected=false;
            });
            $('#js-select-all-action option[value="no"]').attr('selected','selected');
        }, 100);
        return false;
    }
    let parentForm = $('#js-select-all-action').parents('form');
    let checkboxes = parentForm.find('.js-select-el');
    let ids = [];
    for (var i = 0; i < checkboxes.length; i++) {
        let item = $(checkboxes)[i];
        if ($(item).prop('checked')) {
            ids.push($(item).data('id'));
        }
    }
    let url = $('#js-select-all-action option[value="delete"]').data('url');
    $.ajax({
        url: url,
        method: 'post',
        dataType: 'json',
        data: {ids: ids},
        success: (data) => { window.location.reload(); },
        error: (data) => { console.log('error data', data) }
    });
}
