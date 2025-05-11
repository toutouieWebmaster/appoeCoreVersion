<?php

use App\Users;

if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (isset($_POST['ADDUSER'])) {

        if (!empty($_POST['login'])
            && !empty($_POST['password'])
            && !empty($_POST['password2'])
            && !empty($_POST['role'])
            && !empty($_POST['nom'])
            && isset($_POST['prenom'])
        ) {

            if ($_POST['password'] === $_POST['password2']) {

                $UserUpdate = new Users();

                //Add User
                $UserUpdate->feed($_POST);

                if (!$UserUpdate->exist()) {
                    if ($UserUpdate->save()) {

                        //Delete post data
                        unset($_POST);
                        setPostResponse(trans('Le nouvel utilisateur a été enregistré'), 'success');

                    } else {
                        setPostResponse(trans('Un problème est survenu lors de l\'enregistrement du nouvel utilisateur'));
                    }
                } else {
                    setPostResponse(trans('Ce Login est déjà utilisé par un autre utilisateur'));
                }

            } else {
                setPostResponse(trans('Le mot de passe n\'est pas confirmé correctement'));
            }
        } else {
            setPostResponse(trans('Tous les champs sont obligatoires'));
        }
    }

    if (isset($_POST['UPDATEUSER'])) {

        if (!empty($_POST['id'])
            && !empty($_POST['login'])
            && !empty($_POST['nom'])
            && isset($_POST['prenom'])
        ) {

            $UserUpdate = new Users($_POST['id']);

            $UserUpdate->feed($_POST);

            if (!$UserUpdate->exist(true)) {
                if ($UserUpdate->update()) {

                    //Delete post data
                    unset($_POST);

                    setPostResponse(trans('Les données de l\'utilisateur ont été mises à jour'), 'success');

                } else {
                    setPostResponse(trans('Un problème est survenu lors de la mise à jour de l\'utilisateur'));
                }
            } else {
                setPostResponse(trans('Ce login est déjà utilisé par un autre utilisateur'));
            }
        } else {
            setPostResponse(trans('Tous les champs sont obligatoires'));
        }
    }

    if (isset($_POST['UPDATEPASSWORD'])) {

        if (!empty($_POST['id'])
            && !empty($_POST['password'])
            && !empty($_POST['password2'])
        ) {

            if ($_POST['password'] == $_POST['password2']) {

                $UserUpdate = new Users($_POST['id']);

                if ($UserUpdate->exist()) {

                    $UserUpdate->setPassword($_POST['password']);

                    if ($UserUpdate->updatePassword()) {

                        //Delete post data
                        unset($_POST);
                        setPostResponse(trans('Le nouveau mot de passe a été enregistré'), 'success');

                    } else {
                        setPostResponse(trans('Un problème est survenu lors de la mise à jour du nouveau mot de passe'));
                    }
                } else {
                    setPostResponse(trans('Cet utilisateur n\'est pas identifié'));
                }
            } else {
                setPostResponse(trans('Le mot de passe n\'est pas confirmé correctement'));
            }
        } else {
            setPostResponse(trans('Tous les champs sont obligatoires'));
        }
    }
}