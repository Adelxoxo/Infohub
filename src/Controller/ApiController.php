<?php
// src/Controller/ApiController.php
namespace App\Controller;

use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Entity\User;

#[Route('/api')]
class ApiController extends AbstractController
{
    private UserService $userService;
    private SerializerInterface $serializer;

    public function __construct(UserService $userService, SerializerInterface $serializer)
    {
        $this->userService = $userService;
        $this->serializer = $serializer;
    }

    #[Route('/users', methods: ['GET'])]
    public function apiPublicUsers(): JsonResponse
    {
        $users = $this->userService->getAllUsers();

        // Serialize the User entities with the specified group 'usersApi'
        $serializedUsers = $this->serializer->serialize(
            $users,
            'json',
            [AbstractNormalizer::GROUPS => 'publicUsersApi']
        );

        return new JsonResponse($serializedUsers, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/private/users', methods: ['GET'])]
    public function apiPrivateUsers(): JsonResponse
    {
        $users = $this->userService->getAllUsers();

        // Serialize the User entities with the specified group 'usersApi'
        $serializedUsers = $this->serializer->serialize(
            $users,
            'json',
            [AbstractNormalizer::GROUPS => 'privateUsersApi']
        );

        return new JsonResponse($serializedUsers, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/users', methods: ['POST'])]
    public function storeUser(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $userData = $request->getContent();

        try {
            return new JsonResponse($this->userService->storeUser($serializer->deserialize($userData, User::class, 'json')));
        } catch(\Exception $e) {
            return new JsonResponse([
                "code" => $e->getCode(),
                "message" => $e->getMessage()
            ]);
        }
    }
}
