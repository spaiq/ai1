<?php
namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\Post;
use App\Service\Router;
use App\Service\Templating;
use InvalidArgumentException;

class PostController
{
    public function indexAction(Templating $templating, Router $router): ?string
    {
        $posts = Post::findAll();
        $html = $templating->render('post/index.html.php', [
            'posts' => $posts,
            'router' => $router,
        ]);
        return $html;
    }

    public function createAction(?array $requestPost, Templating $templating, Router $router): ?string
    {
        if ($requestPost) {
            $post = Post::fromArray($requestPost);
            $this->validate($requestPost);
            $post->save();

            $path = $router->generatePath('post-index');
            $router->redirect($path);
            return null;
        } else {
            $post = new Post();
        }

        $html = $templating->render('post/create.html.php', [
            'post' => $post,
            'router' => $router,
        ]);
        return $html;
    }

    public function editAction(int $postId, ?array $requestPost, Templating $templating, Router $router): ?string
    {
        $post = Post::find($postId);
        if (!$post) {
            throw new NotFoundException("Missing post with id $postId");
        }

        if ($requestPost) {
            // Validate the post data
            $this->validate($requestPost);
            $post->save();

            $path = $router->generatePath('post-index');
            $router->redirect($path);
            return null;
        }

        $html = $templating->render('post/edit.html.php', [
            'post' => $post,
            'router' => $router,
        ]);
        return $html;
    }

    public function showAction(int $postId, Templating $templating, Router $router): ?string
    {
        $post = Post::find($postId);
        if (!$post) {
            throw new NotFoundException("Missing post with id $postId");
        }

        $html = $templating->render('post/show.html.php', [
            'post' => $post,
            'router' => $router,
        ]);
        return $html;
    }

    public function deleteAction(int $postId, Router $router): ?string
    {
        $post = Post::find($postId);
        if (!$post) {
            throw new NotFoundException("Missing post with id $postId");
        }

        $post->delete();
        $path = $router->generatePath('post-index');
        $router->redirect($path);
        return null;
    }

    private function validate(array $data): void
    {
        $errors = [];

        if (empty($data['subject'])) {
            $errors[] = 'Subject is required.';
        }

        if (empty($data['content'])) {
            $errors[] = 'Content is required.';
        }

        if ($errors) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }
    }
}