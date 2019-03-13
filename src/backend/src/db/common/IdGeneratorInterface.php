<?php
/**
 * IdGeneratorInterface.php.
 * @created 2018-03-24
 */

namespace backend\db\common;

interface IdGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}
