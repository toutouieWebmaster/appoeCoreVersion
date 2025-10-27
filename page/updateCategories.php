<?php
require('header.php');
require_once(WEB_PROCESS_PATH . 'categories.php');

use App\Category;
use App\Form;

$Category = new Category();
$allCategories = $Category->showAll();
$allTypes = getAppTypes();

$separatedCategories = [];
if ($allCategories) {
    foreach ($allCategories as $category) {
        $separatedCategories[$allTypes[$category->type]][$category->parentId][] = $category;
    }
}
echo getTitle(getAppPageName(), getAppPageSlug());
showPostResponse(getDataPostResponse()); ?>
    <div class="container-fluid">
        <button id="addCategory" type="button" class="btn btn-primary mb-4" data-toggle="modal"
                data-target="#modalAddCategory">
            <?= trans('Nouvelle Catégorie'); ?>
        </button>
        <div class="my-4"></div>
        <?php if ($separatedCategories): ?>
            <div class="row my-3 categoriesMenu">
                <?php foreach ($separatedCategories as $key => $categoryType): ?>
                    <div class="col-12 col-lg-4">
                        <h2 class="subTitle"><?= $key; ?></h2>
                        <?php foreach ($categoryType[10] as $separatedCategory): ?>
                            <div data-idcategory="<?= $separatedCategory->id; ?>"
                                 class="m-0 mt-3 py-0 px-3 jumbotron bg-warning text-white fileContent">
                                <input type="tel" class="categoryInput positionMenuSpan"
                                       data-column="position" value="<?= $separatedCategory->position; ?>">
                                <input type="text" class="categoryInput"
                                       data-column="name" value="<?= $separatedCategory->name; ?>">
                                <small class="inputInfo"><?= $separatedCategory->id; ?></small>
                                <?php if (empty($categoryType[$separatedCategory->id])): ?>
                                    <button type="button" class="close deleteCategory">
                                        <span class="fas fa-times"></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($categoryType[$separatedCategory->id])):
                                foreach ($categoryType[$separatedCategory->id] as $separatedSubCategory): ?>
                                    <div class="px-3 py-0 m-0 ml-4 mt-1 jumbotron fileContent"
                                         data-idcategory="<?= $separatedSubCategory->id; ?>">
                                        <input type="tel" class="categoryInput positionMenuSpan"
                                               data-column="position" value="<?= $separatedSubCategory->position; ?>">
                                        <input type="text" class="categoryInput"
                                               data-column="name" value="<?= $separatedSubCategory->name; ?>">
                                        <small class="inputInfo"><?= $separatedSubCategory->id; ?></small>
                                        <?php if (empty($categoryType[$separatedSubCategory->id])): ?>
                                            <button type="button" class="close deleteCategory">
                                                <span class="fas fa-times"></span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($categoryType[$separatedSubCategory->id])):
                                        foreach ($categoryType[$separatedSubCategory->id] as $separatedSubSubCategory): ?>
                                            <div class="px-3 py-0 m-0 ml-5 mt-1 jumbotron fileContent"
                                                 data-idcategory="<?= $separatedSubSubCategory->id; ?>">
                                                <input type="tel" class="categoryInput positionMenuSpan"
                                                       data-column="position"
                                                       value="<?= $separatedSubSubCategory->position; ?>">
                                                <input type="text" class="categoryInput" data-column="name"
                                                       value="<?= $separatedSubSubCategory->name; ?>">
                                                <small class="inputInfo"><?= $separatedSubSubCategory->id; ?></small>
                                                <button type="button" class="close deleteCategory">
                                                    <span class="fas fa-times"></span>
                                                </button>
                                            </div>
                                        <?php endforeach;
                                    endif;
                                endforeach;
                            endif;
                        endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="modal fade" id="modalAddCategory" tabindex="-1" role="dialog" aria-labelledby="modalAddCategoryTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="addCategoryForm">
                    <?= Form::target('ADDCATEGORY'); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAddCategoryTitle">Ajouter une catégorie</h5>
                    </div>
                    <div class="modal-body" id="modalCategoryBody">
                        <?= getTokenField(); ?>
                        <div class="row">
                            <div class="col-12 my-2">
                                <?= Form::text('Nom', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true, 150); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?= Form::select('Type de catégorie', 'type', $allTypes, '', true); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2" id="categoryTypeForm"></div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalCategoryFooter">
                        <button type="submit" name="ADDCATEGORY"
                                class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/app/lib/template/js/category.js"></script>
<?php require('footer.php'); ?>