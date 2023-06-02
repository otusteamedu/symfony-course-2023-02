<?php

namespace App\Domain\Command\CreateUser;

use App\Entity\User;
use App\Event\CreateUserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class Handler implements MessageHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $user = new User();
        $user->setLogin($command->getLogin());
        $user->setPassword($command->getPassword());
        $user->setRoles($command->getRoles());
        $user->setAge($command->getAge());
        $user->setIsActive($command->isActive());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
