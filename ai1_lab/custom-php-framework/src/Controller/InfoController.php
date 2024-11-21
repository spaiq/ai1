<?php
namespace App\Controller;

class InfoController
{
    public function infoAction(): ?string
    {
        ob_start();
        phpinfo();
        return ob_get_clean();
    }
}
