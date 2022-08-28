document.addEventListener("DOMContentLoaded", function(event) {
    handlers();
});

function handlers() {
    $('#js-copy-item').click(function(){
        if (!$(this).data('id')) {
            return;
        }
        window.location = "/manage/user/copy/" + $(this).data('id');
    });
    $('form[name="user"]').submit(async function(event){
        console.log('[submit]', '=====================================');
        $('.invalid-feedback').empty().remove();
        let verified = verifiedFields();
        console.log('[submit] verified', verified);
        if (!verified) {
            console.log('[submit] !verified: return false;');
            event.preventDefault();
            event.stopPropagation();
            return;
        }
        let checked = $(this).data('checked');
        console.log('[submit] data form checked', checked);
        if (!checked) {
            event.preventDefault();
            event.stopPropagation();
            let result = await checkEmail();
            console.log('[submit] result checkEmail', result);
            if (result) {
                $(this).attr('data-checked', 1);
                $(this).data('checked', 1)
                $(this).submit();
                return;
            }
            let html = '<span class="invalid-feedback d-block"><span class="d-block">\n' +
                '<span class="form-error-icon badge badge-danger text-uppercase">Error</span> <span class="form-error-message">Этот Email уже используется</span>\n' +
                '</span></span>';
            $(html).insertAfter($('#user_email'));
        }
        return;
    });
}

function verifiedFields() {
    let isVerifiedFields = true;
    let form   = $('form[name="user"]');
    let userId = $(form).find('#user_id').val();

    let userEmail = $(form).find('#user_email').val();
    if (!userEmail) {
        let html = '<span class="invalid-feedback d-block"><span class="d-block">\n' +
            '<span class="form-error-icon badge badge-danger text-uppercase">Error</span> <span class="form-error-message">Необходимо заполнить</span>\n' +
            '</span></span>';
        $(html).insertAfter($('#user_email'));
        isVerifiedFields = false;
    }

    let userPasswordFirst  = $(form).find('#user_password_first').val();
    let userPasswordSecond = $(form).find('#user_password_second').val();
    if (!userId) {
        if (!userPasswordFirst) {
            let html = '<span class="invalid-feedback d-block"><span class="d-block">\n' +
                '<span class="form-error-icon badge badge-danger text-uppercase">Error</span> <span class="form-error-message">Необходимо заполнить</span>\n' +
                '</span></span>';
            $(html).insertAfter($('#user_password_first'));
            isVerifiedFields = false;
        }

        if (!userPasswordSecond) {
            let html = '<span class="invalid-feedback d-block"><span class="d-block">\n' +
                '<span class="form-error-icon badge badge-danger text-uppercase">Error</span> <span class="form-error-message">Необходимо заполнить</span>\n' +
                '</span></span>';
            $(html).insertAfter($('#user_password_second'));
            isVerifiedFields = false;
        }
    }
    if (userPasswordFirst && userPasswordFirst !== userPasswordSecond) {
        let html = '<span class="invalid-feedback d-block"><span class="d-block">\n' +
            '<span class="form-error-icon badge badge-danger text-uppercase">Error</span> <span class="form-error-message">Пароль не совпадает</span>\n' +
            '</span></span>';
        $(html).insertAfter($('#user_password_second'));
        isVerifiedFields = false;
    }

    let userName = $(form).find('#user_name').val();
    if (!userName) {
        let html = '<span class="invalid-feedback d-block"><span class="d-block">\n' +
            '<span class="form-error-icon badge badge-danger text-uppercase">Error</span> <span class="form-error-message">Необходимо заполнить</span>\n' +
            '</span></span>';
        $(html).insertAfter($('#user_name'));
        isVerifiedFields = false;
    }

    let userLastName = $(form).find('#user_lastName').val();
    if (!userLastName) {
        let html = '<span class="invalid-feedback d-block"><span class="d-block">\n' +
            '<span class="form-error-icon badge badge-danger text-uppercase">Error</span> <span class="form-error-message">Необходимо заполнить</span>\n' +
            '</span></span>';
        $(html).insertAfter($('#user_lastName'));
        isVerifiedFields = false;
    }

    return isVerifiedFields;
}

async function checkEmail() {
    let form = $('form[name="user"]');
    let userId    = $(form).find('#user_id').val();
    let userEmail = $(form).find('#user_email').val();
    if (userId) {
        let userOriginEmail = $(form).find('#user_origin_email').val();
        if (userOriginEmail === userEmail) {
            return true;
        }
        let data = await getData('/manage/user/check_email', {email: userEmail});
        if (data.count === 0) {
            return true;
        }
    } else {
        let data = await getData('/manage/user/check_email', {email: userEmail});
        if (data.count === 0) {
            return true;
        }
    }
    return false;
}

async function getData(url, data) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: 'post',
            dataType: 'json',
            data: data,
            success: (data) => { resolve(data); },
            error: (data) => { reject(data); }
        });
    });
}
