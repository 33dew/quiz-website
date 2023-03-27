<?php

namespace Helper;
class Functions {
    static function view($path, $variables = []){
        return include "./views/index.layout.php";
    }

    static function renderPage($path, $variables = []) {
        extract($variables);
        return include "./views/".$path.".view.php";
    }

    static function redirect($path): void {
        header("Location: ".$path);
    }
}