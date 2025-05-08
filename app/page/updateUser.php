<?php use App\Form;
use App\Users;

require('header.php');
if (!empty($_GET['id'])):
    require_once(WEB_PROCESS_PATH . 'users.php');
    $UpdateUser = new Users();
    $UpdateUser->setId($_GET['id']);
    if ($UpdateUser->show() && $UpdateUser->getRole() <= getUserRoleId()):
        echo getTitle(getAppPageName(), getAppPageSlug());
        showPostResponse(); ?>
        <div class="row">
            <div class="col-lg-12 col-xl-8 my-2">
                <form action="" method="post" id="updateUserForm">
                    <?= getTokenField(); ?>
                    <input type="hidden" name="id" value="<?= $UpdateUser->getId() ?>">
                    <div class="row">
                        <div class="col-md-12 col-lg-6 my-2">
                            <?php
                            $help = '<small id="loginHelp" class="form-text text-muted">' . trans('En changeant votre login vous serez déconnecté du logiciel') . '</small>';
                            echo Form::text('Login', 'login', 'text', $UpdateUser->getLogin(), true, 70, 'aria-describedby="loginHelp"', $UpdateUser->getId() == getUserIdSession() ? $help : '');
                            ?>
                        </div>
                        <div class="col-md-12 col-lg-6 my-2">
                            <?= Form::text('Email', 'email', 'email', $UpdateUser->getEmail()); ?>
                        </div>
                        <div class="col-md-12 col-lg-6 my-2">
                            <?= Form::text('Nom', 'nom', 'text', $UpdateUser->getNom(), true, 40); ?>
                        </div>
                        <div class="col-md-12 col-lg-6 my-2">
                            <?= Form::text('Prénom', 'prenom', 'text', $UpdateUser->getPrenom(), false, 40); ?>
                        </div>
                        <?php if ($UpdateUser->getId() != getUserIdSession() && $UpdateUser->getRole() < (getTechnicienRoleId() + 1)): ?>
                            <div class="col-12 my-2">
                                <?= Form::select('Rôle', 'role', array_map('trans', getRoles()), $UpdateUser->getRole(), true, '', getUserRoleId(), '>'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="col-12 my-3">
                            <?= Form::target('UPDATEUSER'); ?>
                            <?= Form::submit('Enregistrer', 'UPDATEUSERSUBMIT', 'btn-outline-dark'); ?>
                        </div>
                    </div>
                </form>
            </div>
            <hr class="hrStyle d-lg-none d-md-block my-4">
            <div class="col-lg-12 col-xl-4 my-2">
                <form action="" method="post" id="updatePasswordUserForm" autocomplete="off">
                    <input type="hidden" name="_token" value="<?= getToken() ?>">
                    <input type="hidden" name="id" value="<?= $UpdateUser->getId() ?>">
                    <div class="row">
                        <div class="col-12">
                            <div class="card bgColorPrimary border">
                                <div class="card-body row">
                                    <div class="col-12 my-2">
                                        <?= Form::text('Nouveau Mot de passe', 'password', 'password', 'password', true, 150, 'autocomplete="off"'); ?>
                                        <span id="seePswd"
                                              style="position: absolute;bottom: 0;right: 16px;padding: 5px 10px;font-size: 18px;cursor: pointer;color: #000;">
                                            <i class="far fa-eye"></i>
                                        </span>
                                    </div>
                                    <div class="col-12 my-2">
                                        <?= Form::text('Confirmation du Mot de passe', 'password2', 'password', '', true, 150, 'autocomplete="off"'); ?>
                                    </div>
                                    <div class="col-12 my-2">
                                        <?= Form::target('UPDATEPASSWORD'); ?>
                                        <?= Form::submit('Enregistrer', 'UPDATEPASSWORDSUBMIT', 'btn-outline-light'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script type="text/javascript" src="/APPOE/app/lib/template/js/user.js"></script>
    <?php else:
        echo getContainerErrorMsg(trans('Cet utilisateur n\'existe pas'));
    endif;
    require('footer.php');
else:
    echo trans('Cet utilisateur n\'existe pas');
endif; ?>