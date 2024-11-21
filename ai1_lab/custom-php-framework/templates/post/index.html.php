<?php

/** @var Post[] $posts */
/** @var Router $router */

use App\Model\Post;
use App\Service\Router;

$title = 'Post List';
$bodyClass = 'index';

ob_start(); ?>
    <h1>Posts List</h1>

    <a href="<?= $router->generatePath('post-create') ?>">Create new</a>

    <ul class="index-list">
        <?php foreach ($posts as $post): ?>
            <li><h3><?= $post->getSubject() ?></h3>
                <ul class="action-list">
                    <li><a href="<?= $router->generatePath('post-show', ['id' => $post->getId()]) ?>">Details</a></li>
                    <li><a href="<?= $router->generatePath('post-edit', ['id' => $post->getId()]) ?>">Edit</a></li>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>

<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
