$(document).ready(function () {

    if ($('#allMediaModalContainer').length) {
        $('#allMediaModalContainer').load('/app/ajax/media.php?getAllMedia');

        $(document.body).on('submit', 'form#mediaLibraryForm', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var $form = $(this);
            var $btn = $('button[type="submit"]', $form);

            $btn.html(loaderHtml());

            busyApp(false);

            sendPostFiles($form).done(function (data) {
                if (data) {
                    $btn.html(data);
                    getMedia('#chooseFileLibrary');
                }
                availableApp();
            });

        });

        $(document.body).on('submit', 'form#galleryForm', function () {
            $('#loader').fadeIn('fast');
        });

        shortAccessLibrary();

        $(window).on('click', function (e) {
            const $target = $(e.target);
            const $mediaDetails = $('#mediaDetails');
            const $overlay = $('#overlay');

            if (!$mediaDetails.is($target) && $mediaDetails.has($target).length === 0 &&
                !$overlay.is($target) && $overlay.has($target).length === 0) {
                closeMediaDetails();
            }
        });

        $(document.body).on('click', '.closeMediaDetails', function () {
            closeMediaDetails();
        });

        $(document.body).on('click', '.renameMediaFile', function () {

            let $filenameForm = $('#filenameInputForm');
            let $detailsForm = $('#mediaDetailsForm');

            if ($filenameForm.is(':visible')) {
                $filenameForm.slideUp('slow', function () {
                    $detailsForm.slideDown();
                });
            } else {
                $detailsForm.slideUp('slow', function () {
                    $filenameForm.slideDown();
                });
            }
        });

        $(document.body).on('click', 'a.getMediaDetails', function (event) {
            event.preventDefault();
            event.stopPropagation();

            let $btn = $(this);
            let $mediaDetails = $('#mediaDetails');
            let id = $btn.attr('data-file-id');

            if ($mediaDetails.attr('data-file-id') != id) {

                $('#mediaDetails').animate({
                    left: $(window).width() - 300
                }, function () {
                    $('#content-area div#content-size').removeClass().addClass('col-12 col-sm-7 col-lg-8 col-xl-10');
                });

                $mediaDetails.html('<div class="p-3">' + loaderHtml() + '</div>');
                $mediaDetails.load('/app/ajax/getMediaDetails.php?fileId=' + id);
                $mediaDetails.attr('data-file-id', id);
            }
        });

        var mediaGridPreference = getCookie('mediaGridPreferences');
        if (mediaGridPreference) {
            $('div.mediaContainer div.card-columns').css('column-count', mediaGridPreference);
            $('#mediaGridPreferences').val(mediaGridPreference);
        }

        $(document.body).on('change', '#mediaGridPreferences', function () {
            $('div.mediaContainer div.card-columns').css('column-count', $(this).val());
            setCookie('mediaGridPreferences', $(this).val(), 365);
        });

        $(document.body).on('click', '.listView', function () {

            var $btn = $(this);
            $btn.removeClass('listView').addClass('gridView');
            $btn.html('<i class="fas fa-th"></i>');

            $('.fileFormInput').css({
                transform: 'scale(1)',
                position: 'relative'
            });
        });

        $(document.body).on('click', '.gridView', function () {

            var $btn = $(this);
            $btn.removeClass('gridView').addClass('listView');
            $btn.html('<i class="fas fa-th-list"></i>');

            $('.fileFormInput').css({
                transform: 'scale(0)',
                position: 'absolute'
            });
        });


        $(document.body).on('click', '#shortAccessBtns button', function (event) {
            event.preventDefault();

            var libraryId = $(this).data('library-parent-id');

            if (libraryId !== 'all') {
                $('div.mediaContainer').hide();
                $('div.mediaContainer[data-library-parent-id="' + libraryId + '"]').show();
            } else {
                $('div.mediaContainer').show();
            }

            return false;
        });

        $(document.body).on('click', '.seeMediaCaption', function (){
            let $btn = $(this);
            let $container = $btn.closest('div#mediaDetails');
            let $caption = $container.find('.mediaCaption');

            if($caption.hasClass('show')){
                $caption.removeClass('show').stop().fadeOut();
            } else {
                $caption.addClass('show').stop().fadeIn();
            }
        });

        $(document.body).on('submit', 'form#filenameInputForm', function (e) {
            e.preventDefault();

            let $form = $(this);
            let $btn = $form.find('button[type="submit"]');
            let $container = $form.closest('div#mediaDetails');

            $btn.html(loaderHtml());
            busyApp();

            let id = $form.find('input[name="id"]').val();
            let oldName = $form.find('input[name="oldName"]').val();
            let newName = $form.find('input[name="filename"]').val();

            $.post(
                '/app/ajax/media.php',
                {
                    renameMediaFile: 'OK',
                    idImage: id,
                    oldName: oldName,
                    newName: newName
                },
                function (data) {
                    if (data == 'true' || data === true) {
                        $btn.html('Le fichier à été renommé').addClass('btn-success');

                        setTimeout(function () {
                            $('#filenameInputForm').slideUp('slow', function () {
                                $btn.html('Enregistrer').removeClass('btn-success').addClass('btn-info');
                                $('#mediaDetailsForm').slideDown();
                            });
                        }, 600);

                        $form.find('input[name="oldName"]').val(newName);

                        let $img = $('img, audio, video source', $container);
                        //let $imgLibrary = $('div.card[data-file-id="' + id + '"] img');
                        let $copyLink = $('.copyLinkOnClick', $container);
                        let $externalLink = $copyLink.next('a');

                        $img.attr('data-filename', newName);
                        $img.attr('data-originsrc', $img.data('originsrc').replace(oldName, newName));
                        $img.attr('src', $img.attr('src').replace(oldName, newName));
                        //$imgLibrary.attr('src', $imgLibrary.attr('src').replace(oldName, newName));
                        $copyLink.attr('data-src', $copyLink.data('src').replace(oldName, newName));
                        $('.mediaCaption > small').html(newName);
                        $externalLink.attr('href', $externalLink.attr('href').replace(oldName, newName));

                    } else {
                        $btn.html(data).addClass('btn-danger');
                    }
                    availableApp();
                }
            );
        });

        $(document.body).on('input', '.upImgForm', function () {

            busyApp();

            let $info = $('#infosMedia');
            $info.hide().html('');

            let $form = $('form#mediaDetailsForm');

            let idImage = $form.find('input[name="id"]').val();
            let title = $form.find('input.imageTitle').val();
            let description = $form.find('textarea.imageDescription').val();
            let link = $form.find('input.imagelink').val();
            let position = $form.find('input.imagePosition').val();
            let type = $form.find('input[name="imageType"]').val();
            let typeId = $form.find('.imageTypeId').val();

            let options = null;
            let oldTypeId = null;

            if (type === "MEDIA") {
                oldTypeId = $form.find('.imageTypeId').attr('data-old-type');
            } else {
                options = $form.find('select.templatePosition').val();
            }

            let $container = $('div.card[data-file-id="' + idImage + '"]').clone();
            let $imageLink = $('a.getMediaDetails', $container);

            $imageLink.find('h2').text(title);
            $imageLink.find('p').text(description);

            $('#mediaTitle').html(title);

            delay(function () {
                $.post(
                    '/app/ajax/media.php',
                    {
                        updateDetailsImg: 'OK',
                        idImage: idImage,
                        title: title,
                        description: description,
                        link: link,
                        position: position,
                        typeId: typeId,
                        templatePosition: options
                    },
                    function (data) {
                        if (data && (data == 'true' || data === true)) {
                            $info.html('Enregistré').show();
                            availableApp();

                            if (oldTypeId && oldTypeId != typeId) {
                                $('div.card[data-file-id="' + idImage + '"]').fadeOut('fast').remove();
                                $('div.mediaContainer[data-library-id="' + typeId + '"] div.card-columns').append($container.hide().fadeIn('fast'));
                                $form.find('.imageTypeId').attr('data-old-type', typeId);
                            }
                        }
                    }
                );
            }, 300);
        });

        $(document.body).off('click', 'button.deleteImage').on('click', 'button.deleteImage', function (event) {
            event.preventDefault();
            event.stopPropagation();

            if (confirm('Vous allez supprimer cette image')) {
                busyApp();
                var $btn = $(this);
                var idImage = $btn.data('imageid');
                var thumbWidth = $btn.data('thumbwidth');

                $.post(
                    '/app/ajax/media.php',
                    {
                        deleteImage: 'OK',
                        idImage: idImage,
                        thumbWidth: thumbWidth
                    },
                    function (data) {
                        if (data && (data == 'true' || data === true)) {
                            $('.card[data-file-id="' + idImage + '"]').fadeOut().remove();
                            //closeMediaDetails();
                            availableApp();
                        }
                    }
                )
            }
        });

        $(document.body).on('click', '.copyLinkOnClick', function (e) {
            e.preventDefault();
            copyToClipboard($(this).data('src'));
            $(this).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
        });
    }
});