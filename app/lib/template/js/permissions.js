$(document).ready(function () {

    var Roles = {};
    var Status = {
        0: 'Caché',
        1: 'Affiché'
    };

    $.post('/app/ajax/users.php', {GETUSERSROLES: 'OK'},
        function (data) {
            if (data) {
                Roles = JSON.parse(data);
            }
        }
    );

    $('#permissionTable').on('click', '.updatePermissionBtn', function () {

        var $btn = $(this);
        var idMenu = $btn.data('idmenu');

        $btn.removeClass('btn-warning updatePermissionBtn').addClass('btn-success checkPermissionBtn').html('<i class="fas fa-save"></i>');
        var $TR = $btn.parent('td').parent('tr');

        $TR.find('td.changeableTd').each(function () {
            var originalContent = $(this).text();

            if ($(this).data('dbname') === 'min_role_id') {
                var roleId = $.trim($(this).text());
                if (roleId > Object.keys(Roles).length) {
                    roleId = 1;
                }
                originalContent = getKeyByValueInObject(Roles, roleId);
            }

            if ($(this).data('dbname') === 'statut') {

                var html = '<select class="custom-select custom-select-sm changeableInput">';
                $.each(Status, function (val, text) {
                    html += '<option value="' + val + '" ' + (originalContent === text ? 'selected' : '') + ' >' + text + '</option>';
                });
                html += '</select>';

                $(this).html(html);

            } else {
                $(this).html('<input value="' + originalContent + '" class="form-control form-control-sm changeableInput">');
            }
        });
    });

    $('#permissionTable').on('click', '.checkPermissionBtn', function () {

        var $btn = $(this);
        $btn.html(loaderHtml());

        var idMenu = $btn.data('idmenu');
        var $TR = $btn.parent('td').parent('tr');

        var name = $TR.find('td[data-dbname="name"]').find('.changeableInput').val();
        var slug = $TR.find('td[data-dbname="slug"]').find('.changeableInput').val();
        var role = $TR.find('td[data-dbname="min_role_id"]').find('.changeableInput').val();
        var statut = $TR.find('td[data-dbname="statut"]').find('.changeableInput').val();
        var order = $TR.find('td[data-dbname="order_menu"]').find('.changeableInput').val();
        var parentId = $TR.find('td[data-dbname="parent_id"]').find('.changeableInput').val();
        var pluginName = $TR.find('td[data-dbname="pluginName"]').find('.changeableInput').val();


        $TR.find('td.changeableTd').each(function () {

            var originalContent = $(this).find('.changeableInput').val();

            if ($(this).data('dbname') === 'min_role_id') {
                originalContent = Roles[$(this).find('.changeableInput').val()];
            }

            if ($(this).data('dbname') === 'statut') {
                originalContent = Status[$(this).find('.changeableInput').val()];
            }

            $(this).html(originalContent);
        });

        if (name.length > 0 && role > 0 && order > 0) {
            busyApp(false);
            $.post(
                '/app/ajax/permissions.php',
                {
                    updatePermission: 'OK',
                    id: idMenu,
                    name: name,
                    slug: slug,
                    minRoleId: role,
                    statut: statut,
                    orderMenu: order,
                    parentId: parentId,
                    pluginName: pluginName
                },
                function (data) {
                    if (data && (data == 'true' || data === true)) {
                        $btn.removeClass('checkPermissionBtn').html('<i class="fas fa-check"></i>');

                        setTimeout(function () {
                            $btn.removeClass('btn-success').addClass('updatePermissionBtn').html('<span class="btnEdit"><i class="fas fa-wrench"></i></span>');
                        }, 2000);
                    }
                    availableApp();
                }
            )
        }
    });

    $(document.body).on('click', '#addPermissionBtn', function (event) {
        event.stopPropagation();
        event.preventDefault();

        busyApp(false);

        $.post(
            '/app/ajax/permissions.php',
            $('#addPermissionForm').serialize(),
            function (data) {
                if (data && (data == 'true' || data === true)) {
                    $('#loader').fadeIn(400);
                    location.reload();
                } else {
                    $('#permissionFormInfos')
                        .html('<p class="bg-danger text-white">Une erreur s\'est produite. Réessayer ultérieurement</p>');
                }
                availableApp();
            }
        )
    });
});