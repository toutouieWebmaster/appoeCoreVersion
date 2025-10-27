const $libraryContainer = jQuery('#loadMediaLibrary');
let loadedMedia = false;

function loaderHtml() {
    return '<i class="fas fa-circle-notch fa-spin"></i>';
}

function disabledHandle() {
    $('input, textarea, select, button').not(':focus, .noHandle').prop('disabled', true);
}

function enableHandle() {
    $('input, textarea, select, button').not('.noHandle').prop('disabled', false);
}

function busyApp(disabledInput = true) {

    if (true === disabledInput) {
        disabledHandle();
    }
    $('#appStatus').removeClass(function (index, className) {
        return (className.match(/\bbg-\S+/g) || []).join(' ');
    }).addClass('progress-bar-animated bg-warning').parent('div.progress').stop().animate({"height": "10px"}, 200);
}

function availableApp() {
    enableHandle();
    $('#appStatus').removeClass(function (index, className) {
        return (className.match(/\bbg-\S+/g) || []).join(' ');
    }).removeClass('progress-bar-animated').addClass('bg-light').parent('div.progress').stop().animate({"height": "1px"}, 200);
}

function showInfoModal(title, message, options = {}) {

    if ($('#modalInfo').length) {
        let $modalInfo = $('#modalInfo');
        let $modalInfoHeader = $('#modalInfo h5#modalTitle');
        let $modalInfoBody = $('#modalInfo div#modalBody');
        let $modalInfoDialog = $('#modalInfo div.modal-dialog');

        let defaultOptions = {
            'size': ''
        };
        $.extend(defaultOptions, options);

        $modalInfoDialog.addClass(defaultOptions.size);

        $modalInfoHeader.html(title);
        $modalInfoBody.html(message);

        $modalInfo.modal('show');
    }

    return false;
}

function setPreference(type, key, val) {
    return $.post('/app/ajax/config.php', {
        type: type,
        key: key,
        val: val
    });
}

function notification(msg, status = 'success') {

    if (!$('#alertFlashContainer').length) {
        $('body').append('<div id="alertFlashContainer" class=""></div>');
    }

    let $container = $('#alertFlashContainer');

    if($container.text() !== '') {
        $container.stop().html('').removeClass().hide();
    }

    setTimeout(function (){
        $container.addClass(status +'Icon').html(msg).show();
    },100);


    setTimeout(function () {
        $container.fadeOut(300, function (){$(this).html('').removeClass();});
    }, 5000);
}

function shortAccessLibrary() {

    let shortAccessArr = [];
    $.each($('.libraryName'), function () {
        let id = $(this).data('library-parent-id');
        let name = $(this).data('library-parent-name');
        if ($.inArray(id, shortAccessArr) === -1) {
            $('#shortAccessBtns').append('<button type="button" class="btn btn-sm btn-secondary" data-library-parent-id="' + id + '">' + name + '</button>');
            shortAccessArr.push(id);
        }
    });
}

function closeMediaDetails() {
    let $mediaDetails = $('#mediaDetails');
    $mediaDetails.animate({
        left: '100%'
    }, function () {
        $('#content-area div#content-size').removeClass().addClass('col-12');
    });
    $mediaDetails.attr('data-file-id', '');
}

function getMedia($container) {
    $($container).html(loaderHtml() + ' Chargement des medias').load('/app/ajax/getMedia.php', function () {
        shortAccessLibrary();
        $('#libraryModal').css('overflow', 'auto');
        $($container).fadeIn();
    });
}

jQuery(document).ready(function ($) {

    let selectedFiles = [];

    $(document.body).on('click', '.seeOnOverlay', function (event) {
        event.stopPropagation();
        event.preventDefault();

        let notAllowedFormat = ['svg'];
        let originSrc = $(this).data('originsrc');
        let format = originSrc.split('.').pop();
        if($.inArray(format, notAllowedFormat) === -1) {
            let $file = $(this).clone().attr('src', originSrc).removeClass().removeAttr('style');

            setTimeout(function () {
                $('#overlay #overlayContent').html($file);
                $('html, body').css('overflow', 'hidden');
                $('#overlay').css('display', 'flex').hide().fadeIn(200);
            }, 50);
        }
    });

    $(document.body).on('click', '#overlay', function () {
        $(this).css('display', 'none');
        $('html, body').css('overflow', 'auto');
        $('#overlay #overlayContent').html();
    });


    $(document.body).on('change', 'input[type="file"]', function (e) {
        let filenames = [];
        let files = document.getElementById(e.target.id).files;
        if (files.length > 2) {
            filenames.push(files.length + ' fichiers');
        } else {
            for (let i in files) {
                if (files.hasOwnProperty(i)) {
                    filenames.push(files[i].name);
                }
            }
        }
        $(this).next('.custom-file-label').html(filenames.join(', '));
    });

    //Clean input & disable "enter" touch
    $(document.body).on('input', 'input[type!="file"]', function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }

        if ($(this).val() !== '' && $(this).val() !== undefined) {
            $(this).val($(this).val().replace(/`|<|>|\\/gi, '-'));
        }
    });


    //open media library
    $(document).on('dblclick', 'input.urlFile', function (event) {
        event.preventDefault();
        event.stopPropagation();

        let $input = $(this);
        if ($input.prop('readonly') === true) {
            return false;
        }

        let randomId = Math.floor((Math.random() * 1000) + 1);
        $input.attr('data-input-random-id', randomId).popover('hide');
        $libraryContainer.attr('data-input-random-id', randomId);

        if (!loadedMedia) {
            busyApp();
            $libraryContainer.load(WEB_APP_URL + 'lib/assets/mediaLibrary.php', function () {
                loadedMedia = true;
                $('#libraryModal').modal('show');
                availableApp();
            });
        } else {
            $('#libraryModal').modal('show');
        }
    });

    $(document.body).on('shown.bs.modal', '#libraryModal', function () {
        getMedia('#chooseFileLibrary');
    });

    $(document.body).on('hidden.bs.modal', '#libraryModal', function () {
        $('#chooseFileLibrary').html(loaderHtml());
    });

    //choose media from library
    $(document.body).on('click', '#libraryModal .copyLinkOnClick', function (e) {
        e.preventDefault();
        let inputRandomId = $libraryContainer.attr('data-input-random-id');
        let src = $(this).parent().data('src');
        $('input[data-input-random-id="' + inputRandomId + '"]').val(src).trigger('input').removeAttr('data-input-random-id');
        $('#libraryModal').modal('hide');
    });

    $(document.body).on('click', '#helpPageBtn', function () {
        showInfoModal($(this).data('title'), decodeEscapedHtml($(this).data('message')));
    });

    //Loading text on submit form
    $(document.form).on('submit', function () {
        $('[type="submit"]', this).attr('disabled', 'disabled').html(loaderHtml()).addClass('disabled');
    });

    //Anchor link event
    $(document.body).on('click', 'a[href^="#"]:not(.sidebarLink)', function (e) {
        e.preventDefault();
        if ($(this).attr('href').length > 1) {
            $('html,body').stop().animate({scrollTop: $($(this).attr('href')).offset().top}, 'slow');
        }
    });

    /**
     * Clear all selected medias
     */
    $(document.body).on('click', '#closeAllMediaModalBtn', function (event) {
        event.stopPropagation();
        event.preventDefault();

        $('.checkedFile').each(function (i) {
            $(this).children('button.selectParentOnClick').trigger('click');
        });
    });

    /**
     * Select medias from all media container
     */
    $(document.body).on('click', '.selectParentOnClick', function (event) {
        event.stopPropagation();
        event.preventDefault();


        let $btn = $(this);
        let $file = $btn.parent();
        let $filename = $file.data('filename');

        if ($file.hasClass('checkedFile')) {

            if ($.inArray($filename, selectedFiles) > -1) {
                selectedFiles.splice($.inArray($filename, selectedFiles), 1);
            }
            $btn.html('<i class="fas fa-plus"></i>');
            $file.removeClass('border borderColorPrimary checkedFile');

        } else {

            if ($.inArray($filename, selectedFiles) === -1) {
                selectedFiles.push($filename);
            }
            $btn.html('<i class="fas fa-check"></i>');
            $file.addClass('border borderColorPrimary checkedFile');
        }

        $('#inputSelectFiles').val(selectedFiles.length + ' médias');
        $('#textareaSelectedFile').val(selectedFiles.join('|||'));
        $('#saveMediaModalBtn').html(selectedFiles.length + ' médias');
    });

    /**
     * Delete medias definitely
     */
    $(document.body).on('click', '.deleteDefinitelyImageByName', function (event) {
        event.stopPropagation();
        event.preventDefault();


        let $btn = $(this);
        let $addBtn = $btn.prev('button.selectParentOnClick');
        let $file = $btn.parent();
        let $filename = $btn.data('imagename');

        if (confirm('Vous allez supprimer ce fichier définitivement')) {

            if ($file.hasClass('checkedFile')) {

                if ($.inArray($filename, selectedFiles) > -1) {
                    selectedFiles.splice($.inArray($filename, selectedFiles), 1);
                }
                $addBtn.html('<i class="fas fa-plus"></i>');
                $file.removeClass('border borderColorPrimary checkedFile');

                $('#inputSelectFiles').val(selectedFiles.length + ' médias');
                $('#textareaSelectedFile').val(selectedFiles.join('|||'));
                $('#saveMediaModalBtn').html(selectedFiles.length + ' médias');

            }

            mediaAjaxRequest({
                deleteDefinitelyImageByName: 'OK',
                filename: $filename
            }).done(function (data) {
                if (data == 'true' || data === true) {
                    window.location.reload();
                } else {
                    alert(data);
                }
            });
        }
    });

    $('img.seeDataOnHover').popover({
        container: 'body',
        html: true,
        trigger: 'hover',
        placement: 'top',
        content: function () {

            if ($(this).data('width') !== undefined && $(this).data('height') !== undefined) {
                return '<div><strong>Nom:</strong> ' + $(this).data('filename') + '<br><strong>Largeur:</strong> ' + $(this).data('width') + 'px<br><strong>Hauteur:</strong> ' + $(this).data('height') + 'px</div>';
            } else if ($(this).data('filename').length > 0) {
                return '<div><strong>Nom:</strong> ' + $(this).data('filename') + '</div>';
            }
        }
    });

    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        $('.className').popover('disable');
    }

    //sidebar event
    $(document.body).on('click', '#sidebarCollapse', function (e) {
        e.preventDefault();
        $('#sidebar').stop().toggleClass('active');
    });

    //Sidebar open on plugin for user experience
    $('#sidebar ul > li.active').parent('ul').addClass('show').prev('a').attr('aria-expanded', 'true').removeClass('collapsed');

    $('#sidebar ul#adminMenu').slimScroll({
        height: $(window).height() - $('nav.navbar').height(),
    });
    $('.slimScroll').slimScroll();

    $(window).resize(function () {
        if ($(window).width() < 770) {
            $('#sidebar').removeClass('active');
        }

        $('#sidebar ul#adminMenu').slimScroll({
            height: $(window).height() - $('nav.navbar').height(),
        });
    });

    Waves.init();
    Waves.attach('.wave-effect', ['waves-button']);
    Waves.attach('.wave-effect-float', ['waves-button', 'waves-float']);

    let $langageSelectorContainer = $('#languageSelectorContainer');

    $langageSelectorContainer.on('mouseenter', function () {
        $(this).find('div.dropdown').addClass('show');
        $('#languageSelectorContent').addClass('show');
    });

    $langageSelectorContainer.on('mouseleave', function () {
        $(this).find('div.dropdown').removeClass('show');
        $('#languageSelectorContent').removeClass('show');
    });

    let appLang = $('html').attr('lang');
    let dataTableLang = '//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json';

    //sort Table
    $('.sortableTable').DataTable({
        "language": {
            "url": dataTableLang
        },
        "info": false,
        "iDisplayLength": 25,
        "order": []
    });

    switch (appLang) {
        case 'fr':
            dataTableLang = '//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json';
            break;
        case 'en':
            dataTableLang = '//cdn.datatables.net/plug-ins/1.10.16/i18n/English.json';
            break;
        case 'de':
            dataTableLang = '//cdn.datatables.net/plug-ins/1.10.16/i18n/German.json';
            break;
        default:
            dataTableLang = '//cdn.datatables.net/plug-ins/1.10.16/i18n/French.json';
            break;
    }

    $('.langSelector').on('click', function (e) {
        e.preventDefault();
        let langChoice = $(this).attr('id');
        if (langChoice != appLang) {
            $('#loader').fadeIn('fast');

            setLang(langChoice, true).done(function (data) {
                if (data) {
                    window.location.href = window.location.href;
                }
            });
        }
    });

    let $backTop = $("#back-top");
    $backTop.hide();

    if ($(document).height() > 1500) {

        $(window).scroll(function () {
            if ($(this).scrollTop() > 800) {
                $backTop.fadeIn();
            } else {
                $backTop.fadeOut();
            }
        });

        $backTop.on('click', function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
        });
    }

    if ($('table.fixed-header').length) {
        $(window).scroll(function () {
            fixeTableHeader($('header nav').outerHeight());
        });
    }
});

jQuery(window).on('load', function () {

    $('#loader').fadeOut('slow');
    $('#site').css({
        display: 'block',
        opacity: 0,
        visibility: 'visible'
    }).animate({opacity: 1});

    $('img.seeDataOnHover').each(function (index) {

        let $Img = $(this);
        let img = new Image();

        $Img.attr('data-toggle', 'popover');
        if ($Img.attr('data-originsrc') !== undefined) {

            img.src = $Img.attr('data-originsrc');
            img.onload = function () {
                $Img.attr('data-width', this.width);
                $Img.attr('data-height', this.height);
            };
        } else {
            $Img.attr('data-filename', $Img.data('filename'));
        }
    });

    $('.updateImgOverlay').hide();
    $('.updateImgOverlay').parent().each(function (num, el) {
        $(el).hover(function () {
            let updateCont = $(this).find('.updateImgOverlay');
            updateCont.stop(true).delay(1000)
                .html('<i class="fas fa-pencil-alt updateImgOverlayBtn" title="Modifier l\'image (L: 240px | H: 150px)"></i>' +
                    '<form method="POST" class="updateImgOverlayForm" enctype="multipart/form-data" action="' + WEB_APP_URL + 'ajax/media.php">' +
                    '<input type="hidden" name="UPDATEIMGOVERLAY" value="OK">' +
                    '<input type="hidden" name="oldFile" value="' + updateCont.prev('img').attr('src') + '">' +
                    '<input type="file" id="inputUpdateImgOverlay" class="updateImgOverlayInput" name="' + updateCont.data('update-img-overlay') + '">' +
                    '</form>').fadeIn();
        }, function () {
            $(this).find('.updateImgOverlay').stop(true).fadeOut().html('');
        });

        $('[data-seo]').each(function (num, el) {

            let $input = $(el);
            let id = $input.attr('id');

            if (!$('span#maxLengthCount-' + id).length) {
                $input.parent().addClass('position-relative');
                $('<span id="maxLengthCount-' + id + '" style="position:absolute;right:10px;bottom:10px;"></span>').insertAfter($input);
            }

            $input.on('input', function () {
                countChars($(this), $input.data('seo'));
            });
        });
    });

    $(document.body).on('click', '.updateImgOverlayBtn', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let updateImgOverlayCont = $(this).closest('.updateImgOverlay');
        let container = updateImgOverlayCont.parent();
        container.unbind('mouseover mouseenter mouseout mouseleave');

        let $form = $(this).next('form');
        let inputUpdateImgOverlay = $form.find('input[type="file"]');

        inputUpdateImgOverlay.trigger('click');
    });

    $(document.body).on('input', 'input.updateImgOverlayInput', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let $form = $(this).closest('form');
        let updateImgOverlayCont = $form.closest('.updateImgOverlay');

        if ($(this).val().length) {
            let filesize = ((this.files[0].size / 1024) / 1024).toFixed(4);

            if (filesize < 0.5) {

                busyApp(false);
                updateImgOverlayCont.find('i').replaceWith(loaderHtml());

                sendPostFiles($form).done(function (data) {
                    if (data != 'false' && data != false) {
                        updateImgOverlayCont.prev('img').attr('src', data + '?' + new Date().getTime());
                    } else {
                        alert('Impossible d\'enregistrer le fichier !');
                    }
                    updateImgOverlayCont.fadeOut().html('');
                    availableApp();
                });
            } else {
                alert('Le poids de votre fichier est de ' + filesize + 'Mo.\r\n La poids maximal de l\'image autorisé est de 0.5Mo !');
            }
        } else {
            updateImgOverlayCont.fadeOut().html('');
        }
    });

    $(document.body).on('click', '.copyContentOnClick', function (e) {
        e.preventDefault();
        e.stopPropagation();

        copyToClipboard($(this).data('src'));
        $(this).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
    });
});
