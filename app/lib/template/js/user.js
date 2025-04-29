$(document).ready(function () {

    $(document.body).on('click', '.deleteUser', function () {

        if (confirm('Vous allez bannir cet utilisateur !')) {
            var $btn = $(this);
            var idUser = $btn.data('iduser');

            $.post(
                '/app/ajax/users.php',
                {
                    idDeleteUser: idUser
                },
                function (data) {
                    if (true === data || data == 'true') {
                        $btn.parent('td').parent('tr').slideUp();
                    }
                }
            );
        }
    });

    $(document.body).on('click', '.defaultEmailUser', function () {

        if (confirm($(this).data('email') + ' deviendra l\'adresse Email par défaut d\'APPOE !')) {
            let $btn = $(this);
            let icon = $btn.find('span');

            busyApp(false);
            setPreference('DATA', 'defaultEmail', $(this).data('email')).done(function (data) {
                if (data == 'true' || data === true) {

                    $('button.defaultEmailUser').each(function (num, el) {
                        $(el).prop('disabled', false);
                        $(el).find('span').removeClass('text-success');
                    }).promise().done(function () {
                        $btn.prop('disabled', true);
                        icon.addClass('text-success');
                    });
                } else {
                    alert('Problèmes');
                }
                availableApp();
            });
        }
    });

    $(document.body).on('click', '.valideUser', function () {

        if (confirm('Vous allez accepter cet utilisateur !')) {
            var $btn = $(this);
            var idUser = $btn.data('iduser');

            $.post(
                '/app/ajax/users.php',
                {
                    idValideUser: idUser
                },
                function (data) {
                    if (true === data || data == 'true') {
                        $btn.parent('td').parent('tr').removeClass('table-warning');
                        $btn.remove();
                    }
                }
            );
        }
    });

    $(document.body).on('click', '#seePswd', function (e) {
        e.preventDefault();

        let $btn = $(this);
        let $inputPass = $('input[name="password"]');

        if ($inputPass.attr('type') === 'password') {
            $inputPass.attr('type', 'text');
            $btn.html('<i class="far fa-eye-slash"></i>');
        } else {
            $inputPass.attr('type', 'password');
            $btn.html('<i class="far fa-eye"></i>');
        }
    });
});