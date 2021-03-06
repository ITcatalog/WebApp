$(function () {
    $('.dialog-button').click(function () {
        var dialog = document.querySelector('#' + $(this).data('dialog'));

        if (!dialog.showModal) {
            dialogPolyfill.registerDialog(dialog);
        }
        dialog.showModal();
        dialog.querySelector(".close").addEventListener("click", function () {
            dialog.close();
        })
    });
});


$('.itcat-service > .mdl-card__supporting-text').truncate({
    width: 'auto',
    token: '&hellip;',
    side: 'right',
    multiline: true
});

$('.itcat-category > .mdl-card__supporting-text').truncate({
    width: 'auto',
    token: '&hellip;',
    side: 'right',
    multiline: true
});

$('.mdl-card__title-text').truncate({
    width: 'auto',
    token: '&hellip;',
    side: 'right',
    multiline: false
});


$.ajax({
        url: "admin/github.php",
        beforeSend: function () {
            console.log ('Checking for update ...');
        }
    })
    .done(function (data) {
        if (data == 'Already up-to-date.') {
            console.log('App is up-to-date');
        }
        else if (data == 'Running on localhost.') {
            console.log(data)
        }
        else {
            console.log(data);
            console.log('App updated. Reload.');
            location.reload();
        }
    })
    .fail(function () {
        console.log("Can't check for updates");
    });
