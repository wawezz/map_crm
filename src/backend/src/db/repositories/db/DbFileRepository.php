<?php

namespace backend\db\repositories\db;

use backend\db\models\File;
use backend\db\repositories\FileRepositoryInterface;
use yii\db\Connection;

class DbFileRepository extends AbstractDbRepository implements FileRepositoryInterface
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
    }

    public function findById($id): ?File
    {
        $cmd = $this->db->createCommand('SELECT * FROM app_files WHERE id = :id', [
            ':id' => $id,
        ]);

        return $cmd->queryOne([\PDO::FETCH_CLASS, File::class]) ?: null;
    }

    public function insert(File $file): int
    {
        $cmd = $this->db->createCommand('INSERT INTO app_files (name, path, type, createdAt)
                                                               VALUES (:name, :path, :type, :createdAt)', [
            ':name' => $file->name,
            ':path' => $file->path,
            ':type' => $file->type,
            ':createdAt' => $file->createdAt ? $file->createdAt->format('Y-m-d H:i:s') : null,
        ])->execute();

        return $this->db->getLastInsertID();
    }

    public function delete($id): bool
    {
        $cmd = $this->db->createCommand('DELETE FROM app_files WHERE id = :id', [
            ':id' => $id,
        ]);

        return $cmd->execute() > 0;
    }

    public function removeById($id, $path = null): bool
    {

        $cmd = $this->db->createCommand('DELETE FROM app_files WHERE id = :id', [
            ':id' => $id,
        ]);

        if ($path) {
            if (file_exists(\Yii::$app->basePath . "/web" . $path)) {
                unlink(\Yii::$app->basePath . "/web" . $path);
            }
        }

        return $cmd->execute() > 0;
    }

    public function removeByUsers($users): bool
    {

        $cmd = $this->db->createCommand('SELECT app_files.id, app_files.path, app_files.name FROM app_users LEFT JOIN app_files ON app_users.avatarId = app_files.id WHERE app_users.id IN ("' . implode('","', $users) . '") AND app_users.avatarId > 0');

        $id = array();
        $results = $cmd->queryAll();

        foreach ($results as $result) {
            $id[] = $result['id'];

            if (file_exists(\Yii::$app->basePath . "/web" . $result['path'] . $result['name'])) {
                unlink(\Yii::$app->basePath . "/web" . $result['path'] . $result['name']);
            }
        }

        $cmd = $this->db->createCommand()->delete('app_files', ['id' => $id]);

        return $cmd->execute() > 0;
    }
}
