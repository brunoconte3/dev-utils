<?php

declare(strict_types=1);

namespace DevUtils\Test;

use DevUtils\conf\Conf;
use PHPUnit\Framework\TestCase;

class UnitTestCI extends TestCase
{
    public function testCi(): void
    {
        (new Conf());
        if (file_exists('coverage/index.xml')) {
            $returnCheck = shell_exec('php ./src/CI.php  coverage/index.xml 80');
            self::assertStringContainsString('[PASS]', $returnCheck);
        } else {
            $file = PATH_PROJECT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'CI.php';
            self::assertFileIsReadable($file, "filename doesn't exists");
        }
    }
}
