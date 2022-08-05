<?php

declare(strict_types=1);

namespace DevUtils\resource;

final class ComposerInstall
{
    public function __construct()
    {
        if ($this->verificarAutoload()) {
            header('Location: ' . URL_HOST);
            exit();
        }
    }

    private function executarComposer(): string
    {
        putenv('COMPOSER_HOME=/root/.composer');
        return shell_exec('cd .. && composer install 2>&1') ?? '';
    }

    private function executarDump(): string
    {
        putenv('COMPOSER_HOME=/root/.composer');
        return shell_exec('cd .. && composer dump-autoload 2>&1') ?? false;
    }

    private function verificarAutoload(): bool
    {
        return file_exists('./vendor/autoload.php') && is_dir('./vendor');
    }

    public function instalar(): void
    {
        $retorno = '';
        if (!$this->verificarAutoload()) {
            $retorno .= $this->executarComposer();
        } else {
            $this->executarDump();
        }

        header('Content-Type: application/json');

        echo json_encode([
            'erro' => !$this->verificarAutoload(),
            'log' => $retorno,
        ]);
        exit();
    }
}
