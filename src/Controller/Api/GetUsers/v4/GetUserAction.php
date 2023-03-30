<?php

namespace App\Controller\Api\GetUsers\v4;

use App\Manager\UserManager;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetUserAction extends AbstractFOSRestController
{
    public function __construct(private readonly UserManager $userManager)
    {
    }

    #[Rest\Get(path: '/api/v4/users')]
    public function __invoke(Request $request): Response
    {
        $perPage = $request->request->get('perPage');
        $page = $request->request->get('page');
        $users = $this->userManager->getUsers($page ?? 0, $perPage ?? 20);
        $code = empty($users) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;
        $context = (new Context())->setGroups(['video-user-info']);

        return $this->handleView(
            $this->view(['users' => $users], $code)->setContext($context),
        );
    }
}
