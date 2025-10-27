jQuery(document).ready(function ($) {

    $(document.body).on('change', '.updatePreference', function () {

        busyApp(false);
        let key = $(this).attr('name');
        let value = $(this).is(':checked') ? 'true' : 'false';
        let type = $(this).attr('data-config-type');
        setPreference(type, key, value).done(function (data) {
            if (data == 'true' || data === true) {
                alert('Enregistré');
            } else {
                alert('Problèmes');
            }
            availableApp();
        });
    });

    $(document.body).on('click', '#addMyIp', function () {
        let $btn = $(this);
        let ip = $btn.text();
        $('input[name="addPermissionAccess"]').val(ip);
        $('#submitAddPermissionAccess').trigger('click');
    });

    $(document).on('click', 'button.seeMail', function () {
        let obj = $(this).data('obj');
        let msg = $(this).data('msg');

        let $modalInfo = $('#modalInfo');
        let $modalInfoHeader = $('#modalInfo h5#modalTitle');
        let $modalInfoBody = $('#modalInfo div#modalBody');

        $modalInfoHeader.html(obj);
        $modalInfoBody.html(msg);
        $modalInfo.modal('show');
    });

    $(document.body).on('click', '#submitAddPermissionAccess', function (e) {

        busyApp(false);
        $('#errorIpAccess').remove();
        let $input = $('input[name="addPermissionAccess"]');
        if ($input.val()) {
            if (isIP($input.val())) {

                let exist = false;

                $('.ipAccess').each(function () {
                    if ($(this).text() === $input.val()) {
                        $input.closest('div').append('<span id="errorIpAccess" class="text-danger">L\'adresse IP est déjà autorisée</span>');
                        $input.val('');
                        exist = true;
                    }
                });

                if (!exist) {
                    $.post('/app/ajax/config.php', {
                        addAccessPermission: 'OK',
                        ipAddress: $input.val()
                    }).done(function (data) {
                        if (data == 'true' || data === true) {
                            $('#allPermissions div.slimScroll').append('<div class="text-success">' + $input.val() + '</div>');
                            $input.val('');
                        } else {
                            alert('Problèmes');
                        }
                        availableApp();
                    });
                }
            } else {
                alert('L\'adresse IP est incorrecte !');
            }
        }
    });

    $(document.body).on('change', 'input.themeColorChange', function (e) {
        let $input = $(this);
        if ($input.val()) {

            busyApp(false);
            $('#' + $input.data('title')).text($input.val());
            let rgb = hex2Rgb($input.val());
            document.documentElement.style.setProperty($input.data('class'), 'rgb(' + rgb.join() + ')');

            let key = $input.data('class');
            let type = 'THEME';
            setPreference(type, key, $input.val()).done(function (data) {
                if (data == 'true' || data === true) {

                    if ($input.data('class') === '--colorPrimary' || $input.data('class') === '--colorSecondary') {
                        let key = $input.data('class') + 'Opacity';
                        let value = 'rgba(' + rgb.join() + ',0.7)';
                        let type = 'THEME';
                        setPreference(type, key, value);
                    }
                    alert('Enregistré');
                } else {
                    alert('Problèmes');
                }
                availableApp();
            });
        }
    });

    $(document.body).on('click', '.deleteIp', function () {

        busyApp(false);
        let $div = $(this).closest('div');
        let ip = $div.data('ip');
        let id = $div.data('ipaccess-id');
        if (id && ip && isIP(ip)) {

            $.post('/app/ajax/config.php', {
                deleteAccessPermission: 'OK',
                ipAddressId: id
            }).done(function (data) {
                if (data == 'true' || data === true) {
                    $div.remove();
                } else {
                    alert('Problèmes');
                }
                availableApp();
            });

        } else {
            alert('L\'adresse IP est incorrecte !');
        }
    });

    $(document.body).on('mouseenter', 'div.ipAccess', function () {
        $(this).prepend('<i class="fas fa-times text-danger deleteIp" style="cursor:pointer"></i> ').css('font-weight', 'bold');
    });

    $(document.body).on('mouseleave', 'div.ipAccess', function () {
        $(this).html($(this).data('ip')).css('font-weight', 'normal');
    });

    $(document.body).on('click', '#clearFilesCache', function () {

        if (confirm('Vous êtes sur le point de vider tout le cache des fichiers')) {

            var $btn = $(this);
            $btn.html(loaderHtml());

            busyApp(false);
            $.post('/app/plugin/cms/process/ajaxProcess.php', {clearFilesCache: 'OK'}).done(function (data) {
                if (data == 'true' || data === true) {
                    $btn.html('<i class="fas fa-check"></i> Cache des fichiers vidé!').blur();
                    $btn.removeClass('btn-outline-danger').addClass('btn-success');
                } else {
                    alert('Un problème est survenu lors de la vidange du cache');
                }
                availableApp();
            });
        }
    });

    $(document.body).on('click', '#clearServerCache', function () {

        if (confirm('Vous êtes sur le point de purger le cache du serveur')) {

            var $btn = $(this);
            $btn.html(loaderHtml());

            busyApp(false);
            $.post('/app/ajax/config.php', {clearServerCache: 'OK'}).done(function (data) {
                if (data == 'true' || data === true) {
                    $btn.html('<i class="fas fa-check"></i> Cache du serveur purgé!').blur();
                    $btn.removeClass('btn-outline-danger').addClass('btn-success');
                } else {
                    alert('Un problème est survenu lors de la purge du cache');
                }
                availableApp();
            });
        }
    });
});