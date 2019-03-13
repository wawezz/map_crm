<?php

namespace backend\db\repositories;

use backend\db\models\File;

interface FileRepositoryInterface
{
    /**
     * @param $id
     * @return \backend\db\models\File|null
     */
    public function findById($id): ?File;

    /**
     * @param File $file
     * @return int
     */
    public function insert(File $file): int;

    /**
     * @param $id
     * @param $path
     * @return bool
     */
    public function removeById($id, $path): bool;

    /**
     * @param $users
     * @return bool
     */
    public function removeByUsers($users): bool;

    /**
     * @param $id
     * @return bool
     */
    public function delete($id): bool;
}
