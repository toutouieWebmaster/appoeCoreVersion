<?php
require('header.php');
require_once(WEB_PROCESS_PATH . 'categories.php');

use App\Category;

$Category = new Category();
$allCategories = $Category->showAll();
$allTypes = getAppTypes();

$separetedCategories = [];
if ($allCategories) {
    foreach ($allCategories as $category) {
        $separetedCategories[$allTypes[$category->type]][$category->parentId][] = $category;
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
        <?php if ($separetedCategories): ?>
            <div class="row my-3 categoriesMenu">
                <?php foreach ($separetedCategories as $key => $categoryType): ?>
                    <div class="col-12 col-lg-4">
                        <h2 class="subTitle"><?= $key; ?></h2>
                        <?php foreach ($categoryType[10] as $separetedCategory): ?>
                            <div data-idcategory="<?= $separetedCategory->id; ?>"
                                 class="m-0 mt-3 py-0 px-3 jumbotron bg-warning text-white fileContent">
                                <input type="tel" class="categoryInput positionMenuSpan"
                                       data-column="position" value="<?= $separetedCategory->position; ?>">
                                <input type="text" class="categoryInput"
                                       data-column="name" value="<?= $separetedCategory->name; ?>">
                                <small class="inputInfo"><?= $separetedCategory->id; ?></small>
                                <?php if (empty($categoryType[$separetedCategory->id])): ?>
                                    <button type="button" class="close deleteCategory">
                                        <span class="fas fa-times"></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($categoryType[$separetedCategory->id])):
                                foreach ($categoryType[$separetedCategory->id] as $separetedSubCategory): ?>
                                    <div class="px-3 py-0 m-0 ml-4 mt-1 jumbotron fileContent"
                                         data-idcategory="<?= $separetedSubCategory->id; ?>">
                                        <input type="tel" class="categoryInput positionMenuSpan"
                                               data-column="position" value="<?= $separetedSubCategory->position; ?>">
                                        <input type="text" class="categoryInput"
                                               data-column="name" value="<?= $separetedSubCategory->name; ?>">
                                        <small class="inputInfo"><?= $separetedSubCategory->id; ?></small>
                                        <?php if (empty($categoryType[$separetedSubCategory->id])): ?>
                                            <button type="button" class="close deleteCategory">
                                                <span class="fas fa-times"></span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($categoryType[$separetedSubCategory->id])):
                                        foreach ($categoryType[$separetedSubCategory->id] as $separetedSubSubCategory): ?>
                                            <div class="px-3 py-0 m-0 ml-5 mt-1 jumbotron fileContent"
                                                 data-idcategory="<?= $separetedSubSubCategory->id; ?>">
                                                <input type="tel" class="categoryInput positionMenuSpan"
                                                       data-column="position"
                                                       value="<?= $separetedSubSubCategory->position; ?>">
                                                <input type="text" class="categoryInput" data-column="name"
                                                       value="<?= $separetedSubSubCategory->name; ?>">
                                                <small class="inputInfo"><?= $separetedSubSubCategory->id; ?></small>
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
                    <?= \App\Form::target('ADDCATEGORY'); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAddCategoryTitle">Ajouter une catégorie</h5>
                    </div>
                    <div class="modal-body" id="modalCategoryBody">
                        <?= getTokenField(); ?>
                        <div class="row">
                            <div class="col-12 my-2">
                                <?= \App\Form::text('Nom', 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true, 150); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?= \App\Form::select('Type de catégorie', 'type', $allTypes, '', true); ?>
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