<?php

namespace App\Contracts\Repository;

interface UserRepositoryInterface
{
    public function findUser($id);

    public function create(array $user_record);

    public function all(? int $per_page);

    public function findMany($ids);

    public function update($id, array $records);

    public function delete($id);
}