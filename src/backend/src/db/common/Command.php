<?php

namespace backend\db\common;

class Command extends \yii\db\Command
{
    /**
     * @param string $className
     * @return object by $className
     * @throws \yii\db\Exception
     */
    public function queryObject(string $className)
    {
        return $this->queryInternal('fetchObject', $className);
    }

    /**
     * @param string $className
     * @return array of object by $className
     * @throws \yii\db\Exception
     */
    public function queryObjects(string $className): array
    {
        return $this->queryAll([\PDO::FETCH_CLASS, $className]);
    }
}
