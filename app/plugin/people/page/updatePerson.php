<?php
require('header.php');

use App\Plugin\People\People;

if (!empty($_GET['id'])):

    $People = new People();
    $People->setId($_GET['id']);
    if ($People->show()) :

        require(PEOPLE_PATH . 'process/postProcess.php');
        echo getTitle(getAppPageName(), getAppPageSlug());
        showPostResponse() ?>
        <div class="container">
            <form action="" method="post" id="updatePersonForm">
                <?= getTokenField(); ?>
                <input type="hidden" name="id" value="<?= $People->getId(); ?>">
                <div class="row">
                    <div class="col-12 col-lg-4 my-2">
                        <?= \App\Form::text('Enregistrement de type', 'type', 'text', $People->getType(), true, 150, 'list="typeList" autocomplete="off"'); ?>
                        <datalist id="typeList">
                            <?php foreach (getAppTypes() as $type => $name): ?>
                                <option value="<?= $type; ?>"><?= $name; ?></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="col-12 col-lg-4 my-2">
                        <?= \App\Form::select('Nature de la personne', 'nature', getPeopleNatureName(), $People->getNature(), true); ?>
                    </div>
                    <div class="col-12 col-lg-4 my-2">
                        <?= \App\Form::text('Nom', 'name', 'text', $People->getName(), true, 150); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-4 my-2">
                        <?= \App\Form::text('Prénom', 'firstName', 'text', $People->getFirstName(), false, 150); ?>
                    </div>

                    <div class="col-12 col-lg-4 my-2">
                        <?= \App\Form::text('Date de naissance', 'birthDate', 'date', $People->getBirthDate(), false, 10); ?>
                    </div>
                    <div class="col-12 col-lg-4 my-2">
                        <?= \App\Form::text('Adresse Email', 'email', 'email', $People->getEmail(), false, 255); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-4 my-2">
                        <?= \App\Form::text('Téléphone', 'tel', 'tel', $People->getTel(), false, 10); ?>
                    </div>

                    <div class="col-12 col-lg-8 my-2">
                        <?= \App\Form::text('Adresse postale', 'address', 'text', $People->getAddress(), false, 255); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-2 my-2">
                        <?= \App\Form::text('Code postal', 'zip', 'tel', $People->getZip(), false, 7); ?>
                    </div>
                    <div class="col-12 col-lg-5 my-2">
                        <?= \App\Form::text('Ville', 'city', 'text', $People->getCity(), false, 100); ?>
                    </div>
                    <div class="col-12 col-lg-5 my-2">
                        <?= \App\Form::select('Pays', 'country', listPays(), !empty($People->getCountry()) ? $People->getCountry() : 'FR', false); ?>
                    </div>
                </div>
                <div class="my-2"></div>
                <div class="row">
                    <div class="col-12">
                        <?= \App\Form::target('UPDATEPERSON'); ?>
                        <?= \App\Form::submit('Enregistrer', 'UPDATEPERSONSUBMIT'); ?>
                    </div>
                </div>
            </form>
            <div class="my-4"></div>
        </div>
    <?php else:
        echo getContainerErrorMsg(trans('Cette page n\'existe pas'));
    endif;
else:
    echo trans('Cette page n\'existe pas');
endif;
require('footer.php'); ?>