<?php

declare(strict_types=1);

namespace DevUtils\resource;

final class ComposerInstall
{
    public function __construct()
    {
        if ($this->hasAutoload()) {
            header('Location: ' . URL_HOST);
            exit();
        }
    }

    private function runComposerInstall(): string | bool
    {
        putenv('COMPOSER_HOME=/root/.composer');
        return shell_exec('cd .. && composer install 2>&1') ?? '';
    }

    private function runDumpAutoload(): string | bool
    {
        putenv('COMPOSER_HOME=/root/.composer');
        return shell_exec('cd .. && composer dump-autoload 2>&1') ?? false;
    }

    private function hasAutoload(): bool
    {
        return file_exists('./vendor/autoload.php') && is_dir('./vendor');
    }

    public function install(): void
    {
        $log = '';
        if (!$this->hasAutoload()) {
            $log .= $this->runComposerInstall();
        } else {
            $this->runDumpAutoload();
        }

        header('Content-Type: application/json');

        echo json_encode([
            'error' => !$this->hasAutoload(),
            'log' => $log,
        ]);
        exit();
    }
}
