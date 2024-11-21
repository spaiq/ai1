<?php

use App\Controller\CarController;
use App\Controller\InfoController;
use App\Controller\PostController;
use App\Exception\NotFoundException;
use App\Service\Config;
use App\Service\Router;
use App\Service\Templating;

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'autoload.php';

$config = new Config();

$templating = new Templating();
$router = new Router();

$action = $_REQUEST['action'] ?? null;
switch ($action) {
    case 'car-index':
    case null:
        $controller = new CarController();
        $view = $controller->indexAction($templating, $router);
        break;
    case 'car-create':
        $controller = new CarController();
        $view = $controller->createAction($_REQUEST['car'] ?? null, $templating, $router);
        break;
    case 'car-edit':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = new CarController();
        try {
            $view = $controller->editAction($_REQUEST['id'], $_REQUEST['car'] ?? null, $templating, $router);
        } catch (NotFoundException $e) {
            $view = $e->getMessage();
        }
        break;
    case 'car-show':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = new CarController();
        try {
            $view = $controller->showAction($_REQUEST['id'], $templating, $router);
        } catch (NotFoundException $e) {
            $view = $e->getMessage();
        }
        break;
    case 'car-delete':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = new CarController();
        try {
            $view = $controller->deleteAction($_REQUEST['id'], $router);
        } catch (NotFoundException $e) {
            $view = $e->getMessage();
        }
        break;
    case 'post-index':
        $controller = new PostController();
        $view = $controller->indexAction($templating, $router);
        break;
    case 'post-create':
        $controller = new PostController();
        $view = $controller->createAction($_REQUEST['post'] ?? null, $templating, $router);
        break;
    case 'post-edit':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = new PostController();
        try {
            $view = $controller->editAction($_REQUEST['id'], $_REQUEST['post'] ?? null, $templating, $router);
        } catch (NotFoundException $e) {
            $view = $e->getMessage();
        }
        break;
    case 'post-show':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = new PostController();
        try {
            $view = $controller->showAction($_REQUEST['id'], $templating, $router);
        } catch (NotFoundException $e) {
            $view = $e->getMessage();
        }
        break;
    case 'post-delete':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = new PostController();
        try {
            $view = $controller->deleteAction($_REQUEST['id'], $router);
        } catch (NotFoundException $e) {
            $view = $e->getMessage();
        }
        break;
    case 'info':
        $controller = new InfoController();
        $view = $controller->infoAction();
        break;
    default:
        $view = 'Not found';
        break;
}

if ($view) {
    echo $view;
}
