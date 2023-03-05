<?php

namespace App\Controller;

use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class WorldController extends AbstractController
{
    public function __construct(
        readonly private UserManager $manager,
    ) {
    }

    public function hello(): Response
    {
        $user = $this->manager->create('My user');

        return $this->json($user->toArray());
    }
}
