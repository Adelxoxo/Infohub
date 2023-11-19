<?php
// src/Controller/IndexController.php

namespace App\Controller;

use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class IndexController extends AbstractController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/' ,name: 'home')]
    public function home(): Response
    {
        $userData = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : null;

        $users = $this->userService->getAllUsers();
        $formattedUsers = array_map(function ($user) {
            return [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ];
        }, $users);

        return $this->render('index.html.twig', [
            'users' => $formattedUsers,
            'user_data' => $userData
        ]);
    }


    #[Route('/login', methods: ['GET'])]
    public function login(): Response
    {
        return $this->render('login.html.twig', [
            'test' => "adel saljem neki tekst iz controllera u view",
        ]);
    }

    #[Route('/login', methods: ['POST'])]
    public function loginUser(Request $request): Response
    {   
        $email = $request->get('email');
        $password = $request->get('password');

        $loggedInUser = $this->userService->findUserByEmailAndPassword($email, $password);
        $role = $loggedInUser->getRole();

        $_SESSION['user_data'] = isset($loggedInUser) ? [
            "id" => $loggedInUser->getId(),
            "username" => $loggedInUser->getUsername(),
            "email" => $loggedInUser->getEmail(),
            "role" => isset($role) ? $role->getName() : null,
            ] : null;

        return $this->redirectToRoute('home');
    }

    #[Route('/logout')]
    public function logout()
    {
        session_destroy();
        return $this->redirectToRoute('home');
    }

    #[Route('/register', methods: ['GET'])]
    public function register(): Response
    {
        return $this->render('register.html.twig');
    }

    #[Route('/register', methods: ['POST'])]
    public function storeRegisterUser(Request $request): Response
    {   
        $user = new User();
        $user->setUsername($request->get('username'));
        $user->setEmail($request->get('email'));
        $user->setPassword($request->get('password'));

        $message = null;
        
        try {
            $user = $this->userService->storeUser($user)["data"];

            // Storing user to session ...
            $_SESSION['user_data'] = $user;

        } catch(\Exception $e) {
            $message = $e->getMessage();
        }

        return $message ? new Response($message) : $this->redirectToRoute('home');
    }
}
