$(document).ready(function () {

    function disableBtns() {
        $('.operationBtn').attr('disabled', 'disabled').addClass('disabled');
    }

    function enableBtns() {
        $('.operationBtn').attr('disabled', false).removeClass('disabled');
    }

    $.each($('.plugin'), function (index, val) {

        var pluginName = $(this).data('name');
        var $returnContenaire = $(this).find('div.returnContainer');

        if ($(this).find('button.activePlugin').length) {

            var $btn = $(this).find('button.activePlugin');

            $btn.attr('disabled', 'disabled').addClass('disabled').html('<i class="fas fa-circle-notch fa-spin"></i>');
            $.post(
                '/app/ajax/plugin.php',
                {
                    checkTable: pluginName
                },
                function (response) {
                    response = parseInt(response);
                    if (response > 0) {
                        $btn.remove();
                        $returnContenaire.html('<p><strong>Plugin Activé</strong></p><p>Tables activés : ' + response + '</p>');
                    } else {
                        $btn.attr('disabled', false).removeClass('disabled').html('Activer');
                    }
                });
        } else {
            $returnContenaire.html('<p><strong>Plugin Activé</strong></p>');
        }
    });

    setTimeout(function () {
        busyApp();
        $.each($('.pluginVersion'), function (index, val) {
            var $versionContenair = $(this);
            var pluginName = $versionContenair.data('pluginname');
            var responseVersion = $versionContenair.next('small.responseVersion');
            responseVersion.html('<i class="fas fa-circle-notch fa-spin"></i>');
            $.post(
                '/app/ajax/plugin.php',
                {
                    checkVersion: pluginName
                },
                function (response) {
                    if (response) {
                        try {
                            response = $.parseJSON(response);
                            if (response.version != $.trim($versionContenair.text())) {
                                $('#pluginSystemContenair').slideDown('fast');
                                responseVersion.html('<em class="text-danger">' + response.version + '</em>');
                            } else {
                                responseVersion.html('<em class="text-info">' + response.version + '</em>');
                            }
                        } catch (e) {

                        }
                    }
                }
            );
        });
        availableApp();
    }, 2000);

    setTimeout(function () {
        var $versionContenair = $('#systemVersion');
        var systemVersion = $.trim($versionContenair.data('systemversion'));
        var responseVersion = $versionContenair.next('small.responseVersion');
        responseVersion.html('<i class="fas fa-circle-notch fa-spin"></i>');
        $.post(
            '/app/ajax/plugin.php',
            {
                checkSystemVersion: 'ok'
            },
            function (response) {
                if (response) {
                    try {
                        response = $.parseJSON(response);
                        if (response.version != systemVersion) {
                            $('#updateSystemBtnContainer').slideDown('fast');
                            responseVersion.html('<em class="text-danger">' + response.version + '</em>');
                        } else {
                            responseVersion.html('<em class="text-info">' + response.version + '</em>');
                        }
                    } catch (e) {

                    }
                }
            }
        );
    }, 2000);

    $('#updatePlugins').on('click', function () {
        systemAjaxRequest({
            downloadPlugins: 'OK'
        }).done(function (data) {
            if (data) {
                window.location.href = window.location.href;
            } else {
                $('#loader').fadeOut();
            }
        });
    });

    $('#updateSystem').on('click', function () {
        systemAjaxRequest({
            downloadSystemCore: 'OK'
        }).done(function (data) {
                if (data) {
                    window.location.href = window.location.href;
                } else {
                    $('#loader').fadeOut();
                }
            }
        );
    });

    $('#updateSitemap').on('click', function () {
        systemAjaxRequest({
            updateSitemap: 'OK'
        }).done(function (data) {
            if (data === true || data === 'true') {
                $('#updateSitemap').removeClass('operationBtn').html('Sitemap actualisé');
                enableBtns();
            }
            $('#loader').fadeOut();
        });
    });

    $('.activePlugin').on('click', function () {
        busyApp();
        var $btn = $(this);
        var pluginPath = $btn.data('pluginpath');
        $btn.attr('disabled', 'disabled').addClass('disabled').html(loaderHtml());
        var $returnContenaire = $btn.parent('div').next('div.returnContainer');
        $returnContenaire.load('/app/ajax/plugin.php', {setupPath: pluginPath}, function () {
            $returnContenaire.append('<p><strong>Vous devez recharger la page pour voir les nouvelles fonctionnalités.</strong></p>');
            $btn.html('Activé');
        });
        availableApp();
    });

    $('.deletePlugin').on('click', function () {
        busyApp();
        var $btn = $(this);
        var pluginName = $btn.data('pluginname');
        $btn.attr('disabled', 'disabled').addClass('disabled').html(loaderHtml());
        systemAjaxRequest({deletePluginName: pluginName}).done(function (data) {
            if (data) {
                window.location.href = window.location.href;
            }
        });
        availableApp();
    });

    $('#cleanDataBase').on('click', function () {

        var $btn = $(this);
        var $parent = $btn.parent();

        systemAjaxRequest({
            optimizeDb: true
        }).done(function (data) {
            if (data) {
                $btn.remove();
                $parent.prepend('<p>' + data + '</p>');
            }
            enableBtns();
            $('#loader').fadeOut();
        });
    });

    $('#saveFiles').on('click', function () {

        var $btn = $(this);
        var $parent = $btn.closest('div.input-group');

        systemAjaxRequest({
            saveFile: true,
            folder: $parent.find('select#files option:selected').text()
        }).done(function (data) {
            if (data) {
                $('#saveInfos').append('<p>' + data + '</p>');
            }
            enableBtns();
            $('#loader').fadeOut();
        });
    });

    $('.operationBtn').on('click', function (e) {
        e.preventDefault();
        $(this).html('<i class="fas fa-circle-notch fa-spin"></i>');
        disableBtns();
    });
});