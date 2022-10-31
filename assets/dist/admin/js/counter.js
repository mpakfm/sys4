document.addEventListener("DOMContentLoaded", function (event) {
    let cnt = new Counter();
    cnt.setClient();
});

class Counter {
    constructor() {
    }

    setClient(ping = 0) {
        let self = this;
        $.ajax({
            url: '/counter',
            method: 'post',
            dataType: 'json',
            data: {'url': window.location.href, 'page' : window.location.pathname, 'ping': ping},
            success: (data) => { setTimeout(function() {
                self.setClient(1);
            }, 30000)},
        });
    }
}
