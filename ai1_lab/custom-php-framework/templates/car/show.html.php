<?php

/** @var Car $car */
/** @var Router $router */

use App\Model\Car;
use App\Service\Router;

$title = "{$car->getBrand()} ({$car->getId()})";
$bodyClass = 'show';

ob_start(); ?>
    <h1><?= $car->getBrand() ?></h1>
    <article>
        <?= $car->getModel();?>
    </article>

    <ul class="action-list">
        <li> <a href="<?= $router->generatePath('car-index') ?>">Back to list</a></li>
        <li><a href="<?= $router->generatePath('car-edit', ['id'=> $car->getId()]) ?>">Edit</a></li>
    </ul>
<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
