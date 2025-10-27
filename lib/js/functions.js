const WEB_PROTOCOL_URL = window.location.protocol;
const WEB_DIR_URL = WEB_PROTOCOL_URL + '//' + window.location.hostname + '/';
const WEB_APP_URL = WEB_DIR_URL + 'app/';
const WEB_PLUGIN_URL = WEB_APP_URL + 'plugin/';

function convertToSlug(str) {
    return str
        .trim()
        .toLowerCase()
        .normalize('NFD') // décompose les caractères accentués en base + diacritiques
        .replace(/[\u0300-\u036f]/g, '') // supprime les diacritiques
        .replace(/ñ/g, 'n')
        .replace(/ç/g, 'c')
        .replace(/[^a-z0-9 -]/g, '') // supprime les caractères non valides
        .replace(/\s+/g, '-')        // remplace les espaces par des tirets
        .replace(/-+/g, '-');        // supprime les tirets en trop
}

function setLang(lang, interface_lang = false) {
    return $.post('/app/ajax/lang.php',
        {
            lang: lang,
            interfaceLang: interface_lang ? 'interface' : 'content'
        });
}

function PopupBlocked() {
    var PUtest = window.open(null, "", "width=100,height=100");
    try {
        PUtest.close();
        return false;
    } catch (e) {
        return true;
    }
}

function showExternalLink() {
    $('a').filter(function () {
        return $('img', this).length !== 1 && $(this).attr('target') === '_blank' && !$(this).hasClass('fa') && !$(this).is('[class*="icon"]');
    }).append('<img style="margin-left: 3px;width: 10px;height: 10px;vertical-align: text-top;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFAAAABQCAYAAACOEfKtAAAAAXNSR0IArs4c6QAAAoJJREFUeJzt3PFN3DAUgPGvqAPcCNmAblBGuBFugzICG5QNjk7Q6wRlA7IBbABM0P4REOFwLsbPfvaz3idFgkiJyU/JiZgQ8DzP83ppAH4D/wosj8CF3qHU6ZoyeHPEd52VPJoKbbT33xvgQXvAL9oDKjQAW9LPxh8r2/Zolq0965+D3kIxeA640BLeU2Cdd9QS3g1wFVjvzTqFBw54sjU8cMDFYvDAAYPF4kEE4NeIATfAOfo30iPwJ/M+98AusP7XwnpRG+AnZW/O15brjMfzmTPvteRLeADuFwbUXO5jf+CVUvBAAPh3YUDtJcfkQCoeJALuFgasgSf93JXgQSLgIbDRjvJzbbmT4kEi4PFnn/ocW4Zy4EEi4PEGV58ctHa58CACsLcZadXf86AvwBJ4TykbWbyEc16284aU/VkDLIX32pa3ucEhZgNLgKXxkrIC2CQe2ABsFg/aB2waD9oGbB4P2gU0gQdtAprBg/YATeFBW4Dm8KAdQJN40AagWTyoD1gK7wK4Y3pMdy/c18lqApY88+6O9vktwz6D1QIsfdkWOa5WJlTVZ5Jz1QKgWTyoD2gaD+oCmseDeoBd4EEdwG7wQB+wKzzQBbykMzzQBQw9nGQaD3QBb4Dn2ffm8SDuGelcPTDdf+5evjYxs7KWJiBMcLVnuLNW+07EfA4ozAGFOaAwBxTmgMIcUJgDCnNAYQ4ozAGFOaAwBxTmgMJiAKP+uaTxVI/hlvfPkHx46aDBLvn4bEyWl2iEJlRH4Pvs+w0T4oFpQtRaW8JPYo2lBhwIv3SrpyXn20CCtfLehBLLiNLrC7b0dyYeUH73w8B0ut9iF3NkgjP/J1TP87zW+g9xQCOTh4NeSQAAAABJRU5ErkJggg==">');
}

/**
 * Input filter results by "data-filter"
 * @param inputId
 * @param elements like "div.card"
 */
function inputFilter(inputId, elements) {
    var input, filter, element, i, txtValue;
    input = $('#' + inputId);
    filter = input.val().toUpperCase();
    element = $(elements);

    for (i = 0; i < element.length; i++) {
        txtValue = element[i].getAttribute('data-filter');
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            element[i].style.display = "";
        } else {
            element[i].style.display = "none";
        }
    }
}

/**
 * btns filter results by "data-filter"
 * @param btnsClass with data-filter
 * @param elementsToFilterClass with data-filter
 */
function btnsFilter(btnsClass, elementsToFilterClass) {

    $(document.body).on('click', '.' + btnsClass, function (e) {
        e.preventDefault();

        let elements = $('.' + elementsToFilterClass);
        let btn = $(e.target);
        let filter = e.target.getAttribute('data-filter');

        $('.' + btnsClass).removeClass('activeFilter');
        btn.addClass('activeFilter');

        if (filter === '*') {
            elements.css('display', '');
            return;
        }

        filter = convertToSlug(filter);
        let element = elements;
        for (let i = 0; i < element.length; i++) {
            let txtValue = convertToSlug(element[i].getAttribute('data-filter'));
            if (txtValue.indexOf(filter) > -1) {
                element[i].style.display = "";
            } else {
                element[i].style.display = "none";
            }
        }
    });

    $('.' + btnsClass + '.activeFilter').click();
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
}

function getCookie(name) {
    const cookies = document.cookie.split('; ').map(c => c.split('='));
    const cookie = cookies.find(([key]) => key === name);
    return cookie ? decodeURIComponent(cookie[1]) : null;
}


function eraseCookie(name) {
    document.cookie = `${name}=; Max-Age=0; path=/; SameSite=Lax`;
}

function deleteAllCookies() {
    document.cookie.split(";").forEach(cookie => {
        const name = cookie.split("=")[0].trim();
        document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax`;
    });
}


function financial(x, useSpace = true, fractionDigits = 2) {
    if (!useSpace) {
        return Number.parseFloat(x).toFixed(fractionDigits);
    }
    return numberWithSpaces(Number.parseFloat(x).toFixed(fractionDigits));
}

function numberWithSpaces(x) {
    const [intPart, decimalPart] = x.toString().split('.');
    const formattedInt = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    return decimalPart ? `${formattedInt}.${decimalPart}` : formattedInt;
}

function parseReelFloat(x) {
    return x ? Number.parseFloat(x.toString().replace(/ /g, "")) : 0;
}

function rgb2hex(rgb) {
    const result = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    if (!result) return null;

    const toHex = n => Number(n).toString(16).padStart(2, '0');
    return `#${toHex(result[1])}${toHex(result[2])}${toHex(result[3])}`;
}

function hex2Rgb(hex) {
    return hex.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i
        , (m, r, g, b) => '#' + r + r + g + g + b + b)
        .substring(1).match(/.{2}/g)
        .map(x => parseInt(x, 16))
}

function isUrlValid(url) {
    try {
        const parsed = new URL(url);
        return ['http:', 'https:', 'ftp:', 'sftp:'].includes(parsed.protocol);
    } catch (_) {
        return false;
    }
}

function getMonthsName(month = null) {

    let months = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août',
        'Septembre', 'Octobre', 'Novembre', 'Décembre'];

    return month !== null ? months[month] : months;
}

function isIP(ipVal) {
    // Vérification IPv4
    const ipv4Pattern = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;

    // Vérification IPv6
    const ipv6Pattern = /^(?:[0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$/;

    return ipv4Pattern.test(ipVal) || ipv6Pattern.test(ipVal);
}

function escapeHtml(text) {
    return String(text).replace(/[&<>"'`=\/]/g, s => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
        '/': '&#x2F;',
        '`': '&#x60;',
        '=': '&#x3D;'
    })[s] || s);
}

function decodeEscapedHtml(text) {
    return $("<div/>").html(text);
}

function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    } else {
        // http ou vieux navigateur
        let $temp = $("<textarea>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();
        return Promise.resolve();
    }
}

function empty(val) {
    return val === null || val === '';
}

function getKeyByValueInObject(object, value) {
    return Object.keys(object).find(key => object[key] === value);
}

function mediaAjaxRequest(data) {
    return $.post('/app/ajax/media.php', data);
}

function systemAjaxRequest(data) {

    //Active Loader
    $('#loader').fadeIn('fast');
    $('#loaderInfos').html('Veuillez <strong>ne pas quitter</strong> votre navigateur');

    return $.post('/app/ajax/plugin.php', data);
}

function checkUserSessionExit() {
    return $.post('/app/ajax/plugin.php', {checkUserSession: 'OK'});
}

function getHtmlLoader() {
    return '<div class="spinnerAppoe"><div class="rect1"></div><div class="rect2"></div>' +
        '<div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>';
}

function recaptcha($form, recaptchaSiteKey, _callback) {
    if (recaptchaSiteKey.length > 0) {
        $.getScript('https://www.google.com/recaptcha/api.js?render=' + recaptchaSiteKey, function () {
            if (typeof grecaptcha !== 'undefined') {
                grecaptcha.ready(function () {

                    var gaction = $form.data('gaction') ? $form.data('gaction') : 'homepage';
                    grecaptcha.execute(recaptchaSiteKey, {action: gaction}).then(function (token) {
                        $form.append('<input type="hidden" name="g-recaptcha-response" value="' + token + '">');
                        _callback();
                    });
                });
            } else {
                console.log('Not imported recaptcha library !');
            }
        });
    }
}

function sendPostFiles($form) {

    return $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        type: 'POST',
        data: $form.serializefiles(),
        processData: false,
        contentType: false,
        cache: false,
        async: false,
        dataType: "json"
    });
}

function postFormRequest($form, _callback = null) {

    //Prepare responses container
    $('#freturnMsg').remove();
    $('<div id="freturnMsg"></div>').insertAfter($form);
    let $result = $('#freturnMsg');
    $result.html(getHtmlLoader() + ' Envoi en cours...');

    //Add formType POST
    let ftype = $form.data('ftype') ? $form.data('ftype') : 'contact';
    if (!$('input[name="formType"]', $form).length) {
        $form.prepend('<input name="formType" type="hidden" value="' + ftype + '">');
    }

    //Add subject POST
    if ($form.data('fobject') && !$('input[name="object"]', $form).length) {
        $form.prepend('<input name="object" type="hidden" value="' + $form.data('fobject') + '">');
    }

    //Get form responses
    let errorMsg = $form.data('error') ? $form.data('error') : 'Une erreur s\'est produite !';
    let successMsg = $form.data('success') ? $form.data('success') : 'Votre demande a bien été envoyée !';

    //Send all form inputs
    sendPostFiles($form).done(function (data) {
        if (data == 'true' || data === true) {
            $form.trigger("reset");
            $result.html(successMsg);
            if (_callback && typeof _callback === 'function') {
                _callback();
            }
        } else {
            if (data == 'false' || data === false) {
                $result.html(errorMsg);
            } else {
                $result.html(data);
            }
        }
    }).fail(function () {
        $result.html(errorMsg);
    });
}

function processFormPostRequest($form, $recaptchaPublicKey) {

    //Prepare responses container
    $('#freturnMsg').remove();
    $('<div id="freturnMsg"></div>').insertAfter($form);
    let $result = $('#freturnMsg');
    $result.html(getHtmlLoader() + ' Envoi en cours...');

    //Add formType POST
    let ftype = $form.data('ftype') ? $form.data('ftype') : 'contact';
    if (!$('input[name="formType"]', $form).length) {
        $form.prepend('<input name="formType" type="hidden" value="' + ftype + '">');
    }

    //Add subject POST
    if ($form.data('fobject') && !$('input[name="object"]', $form).length) {
        $form.prepend('<input name="object" type="hidden" value="' + $form.data('fobject') + '">');
    }

    //Get form responses
    let errorMsg = $form.data('error') ? $form.data('error') : 'Une erreur s\'est produite !';
    let successMsg = $form.data('success') ? $form.data('success') : 'Votre demande a bien été envoyée !';

    //Check Recaptcha V3
    recaptcha($form, $recaptchaPublicKey, function () {

        //Send all form inputs
        sendPostFiles($form).done(function (data) {
            if (data === true || data === 'true') {
                $form.trigger("reset");
                $('input[name="g-recaptcha-response"]', $form).remove();
                $result.html(successMsg);
            } else {
                $result.html(errorMsg);
            }
        });
    });
}

var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

function fixeTableHeader(topAdd = 0, otherTop = 0) {

    var $fixedHeader = $('.fixed-header');
    if ($fixedHeader.length) {

        var $thead;
        var changeSize = false;
        var thSize = [];
        var top = otherTop === 0 ? $(window).scrollTop() + topAdd : otherTop;
        var tablePosition = parseInt($fixedHeader.offset().top);
        var tableHeight = parseInt($fixedHeader.height());

        //Check table's header cloned
        if (!$fixedHeader.hasClass('cloned')) {
            $fixedHeader.addClass('cloned');
            $thead = $('.fixed-header thead').clone().insertAfter('.fixed-header thead').addClass('clonedHead').hide();
        } else {
            $thead = $('.fixed-header.cloned thead.clonedHead');
        }

        //Responsive : recalculate the width
        if (!getCookie('screenDim') || getCookie('screenDim') !== $fixedHeader.width()) {
            setCookie('screenDim', $('.fixed-header').width());

            changeSize = true;

            $('.fixed-header thead th').each(function (index, val) {
                thSize[index] = $(this).width();
            });

            $('th', $thead).each(function (index, val) {
                $(this).width(thSize[index]);
            });
        }

        //Check if cloned header is needed
        if (tableHeight && top > tablePosition && (top - tablePosition < tableHeight)) {

            if (changeSize) {
                $('th', $thead).each(function (index, val) {
                    $(this).width(thSize[index]);
                });
            }

            $thead.stop().css({
                top: (top - tablePosition),
                left: 0,
                position: 'absolute'
            });

            if ($thead.is(":hidden")) {
                $thead.show();
            }

        } else {
            $thead.hide().css({top: 0, left: 0, position: 'static'});
        }
    }
}

function isMobile() {
    return navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(Android)|(PlayBook)|(BB10)|(BlackBerry)|(Opera Mini)|(IEMobile)|(webOS)|(MeeGo)/i);
}

function isTouch() {
    return isMobile() !== null || document.createTouch !== undefined || ('ontouchstart' in window) || ('onmsgesturechange' in window) || navigator.msMaxTouchPoints;
}

function supportSVG() {
    return !!document.createElementNS && !!document.createElementNS('http://www.w3.org/2000/svg', 'svg').createSVGRect;
}

function countChars($input, type) {

    let maxLengthTable = {'title': 70, 'slug': 70, 'description': 158}

    let inputLength = $input.val().length;
    let inputSeoClass;

    if (inputLength < (maxLengthTable[type] / 4)) {
        inputSeoClass = 'danger'
    } else if (inputLength < (maxLengthTable[type] / 2)) {
        inputSeoClass = 'warning'
    } else if (inputLength < (maxLengthTable[type] / 1.2)) {
        inputSeoClass = 'info'
    } else {
        inputSeoClass = 'success'
    }

    let maxLength = '<span class="text-' + inputSeoClass + '">' + $input.val().length + '</span>/' + maxLengthTable[type];
    let id = $input.attr('id');

    $('span#maxLengthCount-' + id).html(maxLength);
}

$.fn.hasAttr = function (name) {
    return this.attr(name) !== undefined;
};
(function ($) {
    $.fn.appoeForm = function (userOptions = {}) {

        $(document.body).on('submit', this.selector, function (e) {
            e.preventDefault();
            let $form = $(e.target)
            if (!userOptions.publicKey) {
                if ($form.hasAttr('data-key')) {
                    userOptions.publicKey = $form.attr('data-key');
                }
            }
            if (userOptions.publicKey) {
                processFormPostRequest($form, userOptions.publicKey);
            } else {
                return false;
            }
        });

        return this.each(function (i, el) {
            if ($(el).length) {

                let options = jQuery.extend({
                    publicKey: false,
                    recaptcha: '3',
                    method: 'POST',
                    action: '/public/mail.php'
                }, userOptions);

                if (!options.publicKey) {
                    if ($(el).hasAttr('data-key')) {
                        options.publicKey = $(el).attr('data-key');
                    }
                }
                if (!$(el).hasAttr('action')) {
                    $(el).attr('action', options.action);
                }
                if (!$(el).hasAttr('method')) {
                    $(el).attr('method', options.method);
                }
                if (!$(el).hasAttr('data-gaction')) {
                    $(el).attr('data-gaction', $(el).attr('data-ftype'));
                }
            }
        });
    }
})(jQuery);
!function (e) {
    e.fn.serializefiles = function () {
        var n = e(this), i = new FormData, a = n.serializeArray();
        return e.each(n.find('input[type="file"]'), function (n, a) {
            e.each(e(a)[0].files, function (e, n) {
                i.append(a.name, n)
            })
        }), e.each(a, function (e, n) {
            i.append(n.name, n.value)
        }), i
    }
}(jQuery);

(function ($) {
    $.fn.pagination = function (userOptions = {}) {

        let $container = $(this);
        if ($container.length) {

            let options = jQuery.extend({
                categoriesBtnData: 'data-category',
                items: 6,
                previous: '<span aria-hidden="true">&laquo;</span>',
                next: '<span aria-hidden="true">&raquo;</span>',
                defaultCategory: 'all'
            }, userOptions);

            let categoryData = '[' + options.categoriesBtnData + ']';
            let $categoriesBtn = $(categoryData);
            let items = options.items;
            let elements = $container.children();

            function pagination(elements) {

                let pagesHtml = '';
                let countElements = elements.length;

                if (countElements > items) {

                    let quotient = Math.floor(countElements / items);
                    let remainder = countElements % items;
                    let nbPages = quotient;

                    //calculate nb of pages
                    if (quotient >= 1) {
                        for (let i = 1; i <= quotient; i++) {
                            pagesHtml += '<li class="page-item"><a class="page-link" href="#' + i + '">' + i + '</a></li>';
                        }
                    }

                    if (remainder > 0) {
                        nbPages++;
                        pagesHtml += '<li class="page-item"><a class="page-link" href="#' + nbPages + '">' + nbPages + '</a></li>';
                    }

                    //Set page to each element
                    let elPages = 0;
                    elements.each(function (i, el) {
                        if (i % items === 0) {
                            elPages++;
                        }
                        $(el).attr('data-page', '#' + elPages);
                    });

                    //html render
                    let paginationHtml = '<nav aria-label="Page navigation" class="navPagination">' +
                        '<ul class="pagination justify-content-center">' +
                        '<li class="page-item"><a class="page-link" href="#prev" aria-label="Previous">' + options.previous + '</a></li> ' +
                        pagesHtml +
                        '<li class="page-item"><a class="page-link" href="#next" aria-label="Next">' + options.next + '</a></li> ' +
                        '</ul></nav>';

                    $(paginationHtml).insertAfter($container);

                    //Hide elements
                    hideAllElementBut(elements, '#1');
                } else {
                    elements.fadeIn();
                }
            }

            function hideAllElementBut(elements, page = '') {
                elements.hide();
                $('a.page-link').removeClass('active');

                if (page !== '') {
                    $('a.page-link[href="' + page + '"]').addClass('active');
                    $('[data-page="' + page + '"]', $container).fadeIn();
                }

            }

            pagination(elements);

            if ($categoriesBtn.length) {

                $(document.body).on('click', categoryData, function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    let $btn = $(this);
                    let category = $btn.attr(options.categoriesBtnData);

                    if (!$btn.hasClass('active')) {

                        $categoriesBtn.removeClass('active');
                        $btn.addClass('active');
                        $container.children().hide().removeClass('showByCategory').removeAttr('data-page');
                        $('nav.navPagination').remove();

                        if (category === 'all') {
                            pagination($container.children());
                        } else {
                            $('[data-filter*=' + category + ']', $container).addClass('showByCategory');
                            pagination($('.showByCategory', $container));
                        }

                    }
                });
            }

            $(document.body).on('click', '.navPagination a.page-link', function (e) {
                e.preventDefault();
                e.stopPropagation();

                let $btn = $(this);
                let goto = $btn.attr('href');
                if (goto === '#prev' || goto === '#next') {
                    let currentPage = parseInt($('a.page-link.active', '.navPagination').attr('href').substring(1));
                    let nextPage = 0;
                    if (goto === '#prev') {
                        nextPage = (currentPage - 1);
                    } else if (goto === '#next') {
                        nextPage = (currentPage + 1);
                    }
                    if ($('.navPagination a.page-link[href="#' + nextPage.toString() + '"]').length) {
                        hideAllElementBut(elements, '#' + nextPage.toString());
                        $('html, body').animate({scrollTop: (parseInt($($container).offset().top) - 150)}, 500);
                    }

                } else {
                    if (!$(this).hasClass('active')) {
                        hideAllElementBut(elements, goto);
                        $('html, body').animate({scrollTop: (parseInt($($container).offset().top) - 150)}, 500);
                    }
                }
            });

            if (options.defaultCategory !== 'all') {
                $('[' + options.categoriesBtnData + '="' + options.defaultCategory + '"]').trigger('click')
            }
        }

        return this;
    }
})(jQuery);
