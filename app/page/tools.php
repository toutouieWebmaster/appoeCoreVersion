<?php
require('header.php');

use App\MailLogger;
use App\Option;

$Option = new Option();
$MailLogger = new MailLogger();

//Preferences
$Option->setType('PREFERENCE');
$preferences = $Option->showByType();

//Data
$Option->setType('DATA');
$datas = $Option->showByType();

//Ip access
$Option->setType('IPACCESS');
$ipAccess = $Option->showByType();

//Logged mails
$allMails = $MailLogger->showAll();

echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="row">

        <!-- OPTIONS -->
        <div class="col-12 col-md-12 col-lg-6 col-xl-4 mb-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-2 bgColorSecondary">
                    <strong class="mb-0 py-1">Options</strong>
                </div>
                <div class="card-body d-flex align-items-start flex-column">
                    <?php
                    if ($preferences):
                        $numPreference = count($preferences);
                        $i = 0;
                        foreach ($preferences as $key => $preference): ?>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" data-config-type="PREFERENCE"
                                       class="custom-control-input updatePreference"
                                       name="<?= $preference->key; ?>" id="<?= $preference->key; ?>"
                                    <?= $preference->val === 'true' ? 'checked' : ''; ?>>
                                <label class="custom-control-label"
                                       for="<?= $preference->key; ?>"><?= $preference->description; ?></label>
                            </div>
                            <?php if (++$i !== $numPreference): ?>
                                <hr>
                            <?php endif;
                        endforeach;
                    endif; ?>
                    <div class="mt-auto">
                        <button class="btn btn-sm btn-outline-warning" id="clearFilesCache">Vider le cache des fichiers</button>
                        <button class="btn btn-sm btn-outline-danger" id="clearServerCache">Purger le cache du serveur</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- AUTORISATION ACCES -->
        <div class="col-12 col-md-12 col-lg-6 col-xl-4 mb-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-2 bgColorSecondary">
                    <strong class="mb-0 py-1">Autorisations d'accès</strong>
                </div>
                <div class="card-body">
                    <div>
                        <strong class="text-secondary">Mon IP</strong>
                        <div>
                            <span id="addMyIp" class="text-info text-break"
                                  style="cursor:pointer;"><?= getIP(); ?></span>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <strong class="text-secondary">Préconfiguré dans ini.main</strong>
                        <?php if (defined('IP_ALLOWED') && !isArrayEmpty(IP_ALLOWED)):
                            foreach (IP_ALLOWED as $ip): ?>
                                <div>
                                    <small class="text-secondary">
                                        <em><?= (false !== strpos($ip, ':')) ? 'IPV6' : 'IPV4'; ?></em>
                                    </small> <span class="text-info"><?= $ip; ?></span>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                    <hr>
                    <div>
                        <strong class="text-secondary">Ajouté manuellement</strong>
                        <div id="allPersimissions">
                            <div class="slimScroll">
                                <?php if ($ipAccess):
                                    foreach ($ipAccess as $ip): ?>
                                        <div class="ipAccess" data-ipaccess-id="<?= $ip->id; ?>"
                                             data-ip="<?= $ip->key; ?>">
                                            <?= $ip->key; ?></div>
                                    <?php endforeach;
                                endif; ?>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="position-relative">
                        <input type="text" name="addPermissionAccess" placeholder="Nouvelle autorisation"
                               maxlength="45">
                        <span id="submitAddPermissionAccess"><i class="fas fa-plus"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATA -->
        <div class="col-12 col-md-12 col-lg-6 col-xl-4 mb-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-2 bgColorSecondary">
                    <strong class="mb-0 py-1">Données</strong>
                </div>
                <div class="card-body">
                    <?php
                    if ($datas):
                        $numPreference = count($preferences);
                        $i = 0;
                        foreach ($datas as $data): ?>
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <strong><?= $data->description; ?></strong>
                                <mark data-src="<?= $data->val; ?>" class="copyContentOnClick"
                                      style="cursor: pointer;max-width: 300px;"><?= $data->val; ?></mark>
                            </div>
                            <?php if (++$i !== $numPreference): ?>
                                <hr>
                            <?php endif;
                        endforeach;
                    endif; ?>
                </div>
            </div>
        </div>

        <!-- DOCUMENTS -->
        <div class="col-12 col-md-12 col-lg-6 col-xl-4 mb-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-2 bgColorSecondary">
                    <strong class="mb-0 py-1">Documents</strong>
                </div>
                <div class="card-body">
                    <div class="media mb-3 align-items-center">
                        <div class="file-thumbnail">
                            <a target="_blank" href="https://aoe-communication.com/utils-TUTO.pdf">
                                <img class="border h-100 w-100 fit-cover"
                                     src="<?= getImgAccordingExtension('pdf'); ?>" alt="Tutoriel">
                            </a>
                        </div>
                        <div class="media-body ml-3">
                            <h6 class="mb-1">
                                <a class="font-weight-bold" target="_blank"
                                   href="https://aoe-communication.com/utils-TUTO.pdf">Tutoriel</a>
                            </h6>
                            <div>Tutoriel d'utilisation d'APPOE</div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>

        <!-- THEME -->
        <div class="col-12 col-md-12 col-lg-6 col-xl-4 mb-5" id="colorsOptions">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-2 bgColorSecondary">
                    <strong class="mb-0 py-1">Couleurs</strong>
                </div>
                <div class="card-body">
                    <?php
                    $colors = array('Primary' => 'Primaire', 'Secondary' => 'Secondaire', 'Tertiary' => 'Tertiaire');
                    foreach ($colors as $color => $name): ?>
                        <span class="d-block text-secondary text-center mb-2">Couleurs <?= $name; ?></span>
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="media mb-3 align-items-center">
                                <div class="file-thumbnail">
                                    <div id="color<?= $color; ?>" class="border colorDiv"
                                         style="background-color:<?= getOptionTheme('--color' . $color); ?>;">
                                    </div>
                                </div>
                                <div class="media-body ml-3">
                                    <strong class="mb-1">Couleur <?= $name; ?></strong>
                                    <div class="inputColorDiv">
                                        <input class="themeColorChange" type="text"
                                               data-class="--color<?= $color; ?>"
                                               data-title="color<?= $color; ?>"
                                               value="<?= getOptionTheme('--color' . $color); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="media mb-3 align-items-center">
                                <div class="file-thumbnail">
                                    <div id="textBgColor<?= $color; ?>" class="border colorDiv"
                                         style="background-color: <?= getOptionTheme('--textBgColor' . $color); ?>;"></div>
                                </div>
                                <div class="media-body ml-3">
                                    <strong class="mb-1">Texte sur fond</strong>
                                    <div class="inputColorDiv">
                                        <input type="text" class="themeColorChange"
                                               data-class="--textBgColor<?= $color; ?>"
                                               data-title="textBgColor<?= $color; ?>"
                                               value="<?= getOptionTheme('--textBgColor' . $color); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($color != 'Tertiary'): ?>
                            <hr>
                        <?php endif;
                    endforeach; ?>
                </div>
            </div>
        </div>

        <!-- MAILS LOG -->
        <div class="col-12 col-md-12 col-lg-6 col-xl-4 mb-5">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center py-2 bgColorSecondary">
                    <strong class="mb-0 py-1">Mails</strong>
                </div>
                <div class="card-body p-0" id="allLoggedMails">
                    <div class="slimScroll p-3">
                        <?php
                        if ($allMails):
                            $numMails = count($allMails);
                            $i = 0;
                            foreach ($allMails as $mail): ?>
                                <div class="media mb-3 align-items-center">
                                    <div class="file-thumbnail">
                                        <span style="font-size: 30px;"
                                              class="text-<?= $mail->sent ? 'success' : 'danger'; ?>">
                                            <i class="far fa-envelope<?= $mail->sent ? '-open' : ''; ?>"></i></span>
                                    </div>
                                    <div class="media-body ml-3">
                                        <strong class="mb-1"><?= $mail->object; ?></strong>
                                        <div><strong>Le:</strong> <?= displayCompleteDate($mail->date, true); ?></div>
                                        <div><strong>De:</strong> <?= $mail->fromName; ?></div>
                                    </div>
                                    <div class="ml-auto">
                                        <button class="btn colorPrimary seeMail" data-obj="<?= $mail->object; ?>"
                                                data-msg="<?= htmlspecialchars($mail->message); ?>"><i
                                                    class="far fa-envelope-open"></i></button>
                                    </div>
                                </div>
                                <?php if (++$i !== $numMails): ?>
                                    <hr>
                                <?php endif;
                            endforeach;
                        endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script type="text/javascript" src="/app/lib/template/js/tools.js"></script>
<?php require('footer.php'); ?>