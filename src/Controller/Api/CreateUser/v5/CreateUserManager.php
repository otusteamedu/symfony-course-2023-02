<?php

namespace App\Controller\Api\CreateUser\v5;

use App\Controller\Api\CreateUser\v5\Input\CreateUserDTO;
use App\Controller\Api\CreateUser\v5\Output\UserCreatedDTO;
use App\Entity\User;
use App\Event\CreateUserEvent;
use StatsdBundle\Client\StatsdAPIClient;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateUserManager implements CreateUserManagerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface $serializer,
        private readonly StatsdAPIClient $statsdAPIClient,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function saveUser(CreateUserDTO $saveUserDTO): UserCreatedDTO
    {
        $this->statsdAPIClient->increment('save_user_v5_attempt');

        $user = new User();
        $user->setLogin($saveUserDTO->login);
        $user->setPassword($saveUserDTO->password);
        $user->setRoles($saveUserDTO->roles);
        $user->setAge($saveUserDTO->age);
        $user->setIsActive($saveUserDTO->isActive);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new CreateUserEvent($user->getLogin()));

        $result = new UserCreatedDTO();
        $context = (new SerializationContext())->setGroups(['video-user-info', 'user-id-list']);
        $result->loadFromJsonString($this->serializer->serialize($user, 'json', $context));

        return $result;
    }
}
