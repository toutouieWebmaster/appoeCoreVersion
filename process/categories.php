<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (isset($_POST['ADDCATEGORY'])) {

        if (!empty($_POST['name'])
            && !empty($_POST['type'])
            && !empty($_POST['parentId'])
        ) {

            $Category = new \App\Category();

            //Add Page
            $Category->feed($_POST);
            if ($Category->notExist()) {
                if ($Category->save()) {

                    //Delete post data
                    unset($_POST);

                    setPostResponse('La catégorie a été enregistrée', 'success');

                } else {
                    setPostResponse('Un problème est survenu lors de l\'enregistrement de la catégorie');
                }
            } else {

                if ($Category->getStatus() == 0) {
                    setPostResponse('Cette catégorie est archivée. Voulez-vous la restaurer ?', 'warning', '<button type="button" data-restaureid="' . $Category->getId() . '" class="btn btn-link retaureCategory">Oui</button>');
                } else {
                    setPostResponse('Cette catégorie existe déjà');
                }
            }
        } else {
            setPostResponse('Tous les champs sont obligatoires');
        }
    }
}