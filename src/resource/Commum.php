<?php

declare(strict_types=1);

namespace DevUtils\resource;

final class Commum
{
    public static function buscaUltimaCamadaRecursivo($arr, $param, $ultTeste = false): bool
    {
        foreach ($arr as $value) {
            if (is_array($value)) {
                $ultTeste = Commum::buscaUltimaCamadaRecursivo($value, $param, $ultTeste);
            } else {
                if ($value === $param) {
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
