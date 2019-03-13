<?php


namespace backend\db\common\generator;

use backend\db\common\IdGeneratorInterface;

/**
 * Class SecretGenerator
 * @package backend\db\common\generator
 */
class SecretGenerator implements IdGeneratorInterface
{
    public function generate($length = 12): string
    {
        $source = '0123456789abcdef';

        $random = str_shuffle(str_repeat($source, 8));

        return mb_substr($random, 0, $length);
    }
}
