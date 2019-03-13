<?php

namespace backend\commands;

use yii\console\Controller;

class DatabaseController extends Controller
{
    public function actionCreate(string $id)
    {
        $database = getenv('MYSQL_DATABASE') . '-' . $id;
        $user = getenv('MYSQL_USER');
        $pass = getenv('MYSQL_ROOT_PASSWORD');

        $sql = <<<SQL
CREATE DATABASE `$database`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

GRANT ALL PRIVILEGES ON `$database`.* TO '$user'@'%';

FLUSH PRIVILEGES;
SQL;

        $file = \Yii::getAlias('@runtime') . '/db.sql';
        $fp = fopen($file, 'w+');

        fwrite($fp, $sql);

        passthru("mysql -uroot -p$pass -hmysql < $file", $result);

        fclose($fp);

        unlink($file);

        if ($result === 0) {
            $this->stdout("Database `$database` created.\n");
        }
    }

    public function actionUpdateMigrations()
    {
        $db = \Yii::$app->db;

        $migrations = $db->createCommand('SELECT version FROM app_migration')->queryColumn();

        $next = [];

        foreach ($migrations as $migration) {
            $next[$migration] = str_replace('app\\db\\', 'backend\\db\\', $migration);
        }

        foreach ($next as $oldMigration => $nextMigration) {
            $result = $db->createCommand('UPDATE app_migration SET version = :nextMigration WHERE version = :oldMigration', [
                ':nextMigration' => $nextMigration,
                ':oldMigration' => $oldMigration,
            ])->execute();

            if ($result > 0) {
                $this->stdout("$oldMigration => $nextMigration\n");
            } else {
                $this->stderr("$oldMigration !=> $nextMigration\n");
            }
        }
    }
}
