<?php

namespace App\Controller\Api\CreateUser\v5;

use App\Controller\Api\CreateUser\v5\Input\CreateUserDTO;
use App\Controller\Api\CreateUser\v5\Output\UserCreatedDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

class CreateUserManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function saveUser(CreateUserDTO $saveUserDTO): UserCreatedDTO
    {
        $user = new User();
        $user->setLogin($saveUserDTO->login);
        $user->setPassword($saveUserDTO->password);
        $user->setRoles($saveUserDTO->roles);
        $user->setAge($saveUserDTO->age);
        $user->setIsActive($saveUserDTO->isActive);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->debug('This is debug message');
        $this->logger->info('This is info message');
        $this->logger->notice('This is notice message');
        $this->logger->warning('This is warning message');
        $this->logger->error('This is error message');
        $this->logger->critical('This is critical message');
        $this->logger->alert('This is alert message');
        $this->logger->emergency('This is emergency message');

        $result = new UserCreatedDTO();
        $context = (new SerializationContext())->setGroups(['video-user-info', 'user-id-list']);
        $result->loadFromJsonString($this->serializer->serialize($user, 'json', $context));

        return $result;
    }
}
