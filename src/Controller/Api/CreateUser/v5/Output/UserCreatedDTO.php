<?php

namespace App\Controller\Api\CreateUser\v5\Output;

use App\Entity\Traits\SafeLoadFieldsTrait;

class UserCreatedDTO
{
    use SafeLoadFieldsTrait;

    public int $id;

    public string $login;

    public int $age;

    public bool $isActive;

    public function getSafeFields(): array
    {
        return ['id', 'login', 'age', 'isActive'];
    }
}
