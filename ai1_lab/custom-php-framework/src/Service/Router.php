<?php
namespace App\Service;

class Router
{
    public function generatePath(string $action, ?array $params = []): string
    {
        $query = $action ? http_build_query(array_merge(['action' => $action], $params)) : null;
        return "/index.php" . ($query ? "?$query" : null);
    }

    public function redirect($path): void
    {
        header("Location: $path");
    }
}
