<?php

/** @var Post $post */
/** @var Router $router */

use App\Model\Post;
use App\Service\Router;

$title = "Edit Post {$post->getSubject()} ({$post->getId()})";
$bodyClass = "edit";

ob_start(); ?>
    <h1><?= $title ?></h1>
    <form action="<?= $router->generatePath('post-edit') ?>" method="post" class="edit-form">
        <?php require __DIR__ . DIRECTORY_SEPARATOR . '_form.html.php'; ?>
        <input type="hidden" name="action" value="post-edit">
        <input type="hidden" name="id" value="<?= $post->getId() ?>">
    </form>

    <ul class="action-list">
        <li>
            <a href="<?= $router->generatePath('post-index') ?>">Back to list</a></li>
        <li>
            <form action="<?= $router->generatePath('post-delete') ?>" method="post">
                <input type="submit" value="Delete" onclick="return confirm('Are you sure?')">
                <input type="hidden" name="action" value="post-delete">
                <input type="hidden" name="id" value="<?= $post->getId() ?>">
            </form>
        </li>
    </ul>

<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
