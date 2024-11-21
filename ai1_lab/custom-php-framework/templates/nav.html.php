<?php
/** @var $router Router */

use App\Service\Router;

?>
<ul>
    <li><a href="<?= $router->generatePath('') ?>">Home</a></li>
    <li><a href="<?= $router->generatePath('post-index') ?>">Posts</a></li>
    <li><a href="<?= $router->generatePath('car-index') ?>">Cars</a></li>
</ul>
<?php
