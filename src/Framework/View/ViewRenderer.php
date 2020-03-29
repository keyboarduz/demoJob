<?php


namespace Framework\View;


class ViewRenderer
{
    public function render($view, $params = []): string
    {
        $viewFile = '../views/' . $view . '.php';

        ob_start();
        extract($params, EXTR_OVERWRITE);
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = '../views/_layout.php';
        //layout
        ob_start();
        require $layoutFile;
        return ob_get_clean();
    }
}