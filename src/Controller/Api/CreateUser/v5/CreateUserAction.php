<?php

namespace App\Controller\Api\CreateUser\v5;

use App\Controller\Api\CreateUser\v5\Input\CreateUserDTO;
use App\Controller\Api\CreateUser\v5\Output\UserCreatedDTO;
use App\Controller\Common\ErrorResponseTrait;
use App\Domain\Command\CreateUser\CreateUserCommand;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class CreateUserAction extends AbstractFOSRestController
{
    use ErrorResponseTrait;

    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    #[Rest\Post(path: '/api/v5/users')]
    /**
     * @OA\Post(
     *     operationId="addUser",
     *     tags={"Пользователи"},
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\JsonContent(ref=@Model(type=CreateUserDTO::class))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref=@Model(type=UserCreatedDTO::class))
     *     )
     * )
     */
    public function saveUserAction(CreateUserDTO $request, ConstraintViolationListInterface $validationErrors): Response
    {
        if ($validationErrors->count()) {
            $view = $this->createValidationErrorResponse(Response::HTTP_BAD_REQUEST, $validationErrors);
            return $this->handleView($view);
        }
        $this->messageBus->dispatch(CreateUserCommand::createFromRequest($request));

        return $this->handleView($this->view(['success' => true]));
    }
}
