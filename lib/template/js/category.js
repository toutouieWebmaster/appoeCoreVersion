$(document).ready(function () {

    $('select#type').on('change', function () {
        let categoryType = $("select#type option:selected").text();
        let $categoryTypeInput = $('#categoryTypeForm');
        $categoryTypeInput.html('<i class="fas fa-circle-notch fa-spin"></i> Chargement');
        $.post(
            '/app/ajax/categories.php',
            {
                getCategoriesByType: 'OK',
                categoryType: categoryType
            },
            function (data) {
                if (data) {
                    $categoryTypeInput.html(data);
                }
            }
        )
    });

    $('.categoryInput').on('input', function () {

        let $parent = $(this).closest('div.fileContent');
        let idCategory = $parent.data('idcategory');
        let catName = $parent.find('input[data-column="name"]').val();
        let catPos = $parent.find('input[data-column="position"]').val();
        let $inputInfo = $parent.find('small.inputInfo');
        $inputInfo.html('');

        if (catName.length > 0 && catPos > 0) {
            busyApp();
            $.post(
                '/app/ajax/categories.php',
                {
                    updateCategoryName: 'OK',
                    idCategory: idCategory,
                    catName: catName,
                    catPos: catPos
                },
                function (data) {
                    if (data && (data == 'true' || data === true)) {
                        $inputInfo.html('Enregistré');
                        setTimeout(function () {
                            $inputInfo.html(idCategory);
                        }, 1000);
                        availableApp();
                    }
                }
            )
        } else {
            $inputInfo.html('Le nom doit contenir au moins une lettre et la position doit être supérieure à 0');
        }
    });

    $('.retaureCategory').on('click', function () {

        let $btn = $(this);
        let idCategory = $btn.data('restaureid');

        $.post(
            '/app/ajax/categories.php',
            {
                restaureCategory: 'OK',
                idCategoryToRestaure: idCategory
            },
            function (data) {
                if (data && (data == 'true' || data === true)) {
                    window.location = window.location.href;
                    window.location.reload(true);
                }
            }
        )
    });

    $('.deleteCategory').on('click', function () {
        if (confirm('Vous allez supprimer cette catégorie')) {
            busyApp();

            let $parent = $(this).closest('div.fileContent');
            let idCategory = $parent.data('idcategory');
            let $btn = $(this);

            $.post(
                '/app/ajax/categories.php',
                {
                    deleteCategory: 'OK',
                    idCategory: idCategory
                },
                function (data) {
                    if (data && (data == 'true' || data === true)) {
                        $btn.parent('div').fadeOut('fast');
                        availableApp();
                    }
                }
            )
        }
    });

});