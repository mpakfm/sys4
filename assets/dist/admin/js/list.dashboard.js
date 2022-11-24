document.addEventListener("DOMContentLoaded", function(event) {
    handlers();
    init();
});

function init() {
    //flatpickr("#filter_date_time", {});
    let optional_config = {
        "locale": "ru",
        "allowInput": true,
        "dateFormat": "d.m.Y"
    };
    $("#filter_date_time").flatpickr(optional_config);
    if ($('input[name="query"]').val() !== '') {
        $('#query-delete').show();
    }
}

function handlers() {
    $('#list-filter-close').click(function(){
        $('#list-filter').hide();
    });
    $('#list-search input[name="query"]').click(function(){
        $('#list-filter').toggle();
    });
    $('#list-search input[type="reset"]').click(function(){
        $('#filter_date_time').val('').attr('value', '');
    });

    $('#query-delete').click(function(){
        $('input[name="query"]').val('');
        $('#query-delete').hide();
    });
    $('input[name="query"]').keydown(function(e){
        if(e.keyCode === 13) {
            $('form#list-search').submit();
        }
        if ($('#query-delete:hidden') && $('input[name="query"]').val() !== '') {
            $('#query-delete').show();
        }
    });
}
