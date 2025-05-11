<?php

use App\Category;
use App\Form;

require_once('header.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        if (!empty($_POST['updateCategoryName']) && !empty($_POST['idCategory'])
            && !empty($_POST['catName']) && !empty($_POST['catPos'])) {

            $Category = new Category($_POST['idCategory']);
            $Category->setName($_POST['catName']);
            $Category->setPosition($_POST['catPos']);

            if ($Category->update()) {
                echo 'true';
            }
            exit();
        }

        if (isset($_POST['getCategoriesByType']) && !empty($_POST['categoryType'])) {
            $Category = new Category();
            $Category->setType($_POST['categoryType']);
            $allCatgories = extractFromObjToArrForList($Category->showByType(), 'id');

            $allCatgories[10] = trans('Aucun parent');
            if ($allCatgories) {
                echo Form::select(trans('Catégorie parente'), 'parentId', $allCatgories, '', true);
            }
            exit();
        }

        if (isset($_POST['deleteCategory']) && !empty($_POST['idCategory'])) {

            $Category = new Category($_POST['idCategory']);

            if ($Category->delete()) {
                echo 'true';
            }
            exit();
        }

        if (isset($_POST['restaureCategory']) && !empty($_POST['idCategoryToRestaure'])) {

            $Category = new Category($_POST['idCategoryToRestaure']);
            $Category->setStatus(1);

            if ($Category->update()) {
                echo 'true';
            }
            exit();
        }
    }
}