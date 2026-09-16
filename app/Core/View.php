<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = null): string
    {
        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';
        if (!is_file($viewFile)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout !== null) {
            $layoutFile = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
            if (!is_file($layoutFile)) {
                throw new \RuntimeException("Layout not found: {$layout}");
            }
            ob_start();
            require $layoutFile;
            return ob_get_clean();
        }

        return $content;
    }
}
