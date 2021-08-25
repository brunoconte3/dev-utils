function dispararAlerta(msg, status = 'warning', time = 5000, icon = '') {
    $('.alert').remove();
    $.notify({
        icon: icon,
        message: msg
    }, {
        type: status,
        allow_dismiss: true,
        newest_on_top: true,
        showProgressbar: false,
        placement: {
            from: "top",
            align: "center"
        },
        z_index: 1031,
        delay: time,
        timer: 200,
        animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
        }
    });
}
