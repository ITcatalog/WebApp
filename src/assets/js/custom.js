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
        url: "admin/github.php?branch=dev",
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
