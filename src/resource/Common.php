<?php

declare(strict_types=1);

namespace DevUtils\resource;

final class Common
{
    public static function searchLastLayerRecursive(array $arr, int $param, bool $ultTeste = false): bool
    {
        foreach ($arr as $value) {
            if (is_array($value)) {
                $ultTeste = Common::searchLastLayerRecursive($value, $param, $ultTeste);
            } else {
                if (intval($value) === $param) {
                    $ultTeste = true;
                }
            }
            if ($ultTeste) {
                break;
            }
        }
        return $ultTeste;
    }
}
