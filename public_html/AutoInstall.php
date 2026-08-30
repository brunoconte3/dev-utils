<?php

declare(strict_types=1);

use DevUtils\conf\Conf;
use DevUtils\resource\ComposerInstall;

$projectRoot = dirname(__DIR__);
$vendorDir = $projectRoot . DIRECTORY_SEPARATOR . 'vendor';
$autoloadFile = $vendorDir . DIRECTORY_SEPARATOR . 'autoload.php';

if (!is_dir($vendorDir) || !is_file($autoloadFile)) {
    $confFile = $projectRoot . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'Conf.php';
    $installerFile = $projectRoot . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR
        . 'resource' . DIRECTORY_SEPARATOR . 'ComposerInstall.php';
    $viewFile = __DIR__ . DIRECTORY_SEPARATOR . 'composerInstall.view.php';

    require_once $confFile; // NOSONAR - autoloader ainda indisponível
    new Conf(); // NOSONAR - o construtor define URL_HOST e URL, usadas pela view e pelo instalador

    $install = filter_input(INPUT_POST, 'instalar');
    if (!empty($install)) {
        require_once $installerFile; // NOSONAR - autoloader ainda indisponível
        (new ComposerInstall())->install();
    }

    if (!is_file($viewFile)) {
        http_response_code(500);
        exit('Houve um erro. A view composerInstall não existe!');
    }

    require_once $viewFile; // NOSONAR - carrega a view, não é carregamento de classe
    exit();
}
