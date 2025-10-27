jQuery(document).ready(function ($) {

    $(document.body).on('change', '.updatePreference', function () {

        busyApp(false);
        let key = $(this).attr('name');
        let value = $(this).is(':checked') ? 'true' : 'false';
        let type = $(this).attr('data-config-type');
        setPreference(type, key, value).done(function (data) {
            if (data == 'true' || data === true) {
                notification('Votre préférence a été enregistrée');
            } else {
                notification('Une erreur s\'est produite', 'danger');
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
                            notification('Une erreur s\'est produite', 'danger');
                        }
                        availableApp();
                    });
                }
            } else {
                notification('L\'adresse IP est incorrecte !', 'danger');
            }
        }
    });

    $(document.body).on('input', 'input.themeColorChange[type="text"]', function () {
        let $input = $(this);
        $('div#'+ $input.data('title')).css('background-color', $input.val());
        delay(function () {
            if ($input.val() && $input.val().length === 7) {
                busyApp();
                let rgb = hex2Rgb($input.val());
                document.documentElement.style.setProperty($input.data('class'), $input.val());
                let key = $input.data('class');
                let type = 'THEME';
                setPreference(type, key, $input.val()).done(function (data) {
                    if (data == 'true' || data === true) {

                        if ($input.data('class') === '--colorPrimary'
                            || $input.data('class') === '--colorSecondary'
                            || $input.data('class') === '--colorTertiary') {
                            let key = $input.data('class') + 'Opacity';
                            let value = 'rgba(' + rgb.join() + ',0.7)';
                            let type = 'THEME';
                            setPreference(type, key, value);
                        }
                        notification('La couleur a été enregistrée');
                    } else {
                        notification('Une erreur s\'est produite', 'danger');
                    }
                    availableApp();
                });
            }
        }, 1000);
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
                    notification('Une erreur s\'est produite', 'danger');
                }
                availableApp();
            });

        } else {
            notification('L\'adresse IP est incorrecte !', 'danger');
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
                    notification('Un problème est survenu lors de la vidange du cache', 'danger');
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
                    notification('Un problème est survenu lors de la purge du cache', 'danger');
                }
                availableApp();
            });
        }
    });
});