<?php

/** @var Post $post */
/** @var Router $router */

use App\Model\Post;
use App\Service\Router;

$title = 'Create Post';
$bodyClass = "edit";

ob_start(); ?>
    <h1>Create Post</h1>
    <form action="<?= $router->generatePath('post-create') ?>" method="post" class="edit-form">
        <?php require __DIR__ . DIRECTORY_SEPARATOR . '_form.html.php'; ?>
        <input type="hidden" name="action" value="post-create">
    </form>

    <a href="<?= $router->generatePath('post-index') ?>">Back to list</a>
<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
