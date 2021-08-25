<?php

declare(strict_types=1);

use DevUtils\resource\ComposerInstall;

if (!file_exists('../vendor/autoload.php') && !is_dir('../vendor')) {
    $instalar = filter_input(INPUT_POST, 'instalar');
    if (!empty($instalar)) {
        include_once '../src/resource/ComposerInstall.php';
        $composer = new ComposerInstall();
        $composer->instalar();
    }
    $arquivo = str_replace([
        '\\',
        '\\\\',
        '/',
        '//',
    ], DIRECTORY_SEPARATOR, ('composerInstall.view.php'));
    if (!file_exists($arquivo)) {
        echo 'Houve um erro. A view composerInstall não existe!';
    }
    if (!require_once($arquivo)) {
        echo 'Houve um erro ao carregar a view composerInstall!';
    }
    exit();
}
