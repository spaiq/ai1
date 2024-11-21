<?php
namespace App\Service;

class Templating
{
    public function render(string $template, ?array $params = []): string
    {
        ob_start();
        extract($params);
        $template = str_replace('/', DIRECTORY_SEPARATOR, $template);
        $path = __DIR__
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . 'templates'
            . DIRECTORY_SEPARATOR . $template;

        require $path;
        return ob_get_clean();
    }
}
