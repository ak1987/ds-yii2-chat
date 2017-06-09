$().ready(function () {
    socket = new WebSocket('ws://yii2-chat.loc:8080');
    socket.onopen = function (e) {
    };
    socket.onmessage = function (e) {
        var message = JSON.parse(e.data);
        var htmlString;
        switch (message.type) {
            case 1:
                htmlString = '<div class="chat-msg"><div class="username"><span class="label label-default">' + message.user + '</span></div><div class="message">' + message.msg + '</div> </div>'
                break;
            case 2:
                htmlString = '<div class="chat-msg"><div class="username"><span class="label label-info">SERVER RESPONSE</span></div><div class="message">' + message.msg + '</div> </div>';
                refreshUsers();
                break;
            case 3:
                htmlString = '';
                var $div = $('#userlist');
                var html = '';
                $.each(message.msg,function(key,value){
                    html = html+'<div>'+value+' <span class="kick-user glyphicon glyphicon-remove" aria-hidden="true" data-username="'+value+'"></span></div>';
                });
                $div.html(html);
                break;

        }
        if (htmlString) {
            $('#chatwindow').append(htmlString);
            $('#chatwindow').scrollTop(100000000000000000);
        }
    };
    //$('#send-msg').hide();
    //$('#msg').hide();

    $('#send-msg').on('click', function (e) {
        e.preventDefault();
        var msg = $('#msg').val();
        var post = JSON.stringify({action: 'send-message', msg: msg});
        socket.send(post);
    });

    $('#login').on('click', function (e) {
        e.preventDefault();
        var user = $('#user').val();
        if(!user) {
            alert('Please fill form');
            return(false);
        }
        var post = JSON.stringify({action: 'set-user', user: user});
        socket.send(post);
        $('#loginform').fadeOut(function () {
            $('#chatscreen').fadeIn();
        });
    });

    $('#userlist').on('click', '.kick-user', function (e) {
        e.preventDefault();
        var user = $(this).data('username');
        var post = JSON.stringify({action: 'kick-user', user: user});
        socket.send(post);
    });

    function refreshUsers() {
        var post = JSON.stringify({action: 'get-users'});
        socket.send(post);
    }
});