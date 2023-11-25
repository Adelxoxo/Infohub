<?php
// src/Controller/IndexController.php

namespace App\Controller;

use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Services\CategoryService;
use App\Services\PostService;
use DateTime;

class IndexController extends AbstractController
{
    private UserService $userService;
    private CategoryService $categoryService;
    private PostService $postService;

    public function __construct(
        UserService $userService,
        CategoryService $categoryService,
        PostService $postService
    )
    {
        $this->userService = $userService;
        $this->categoryService = $categoryService;
        $this->postService = $postService;
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
            'user_data' => $userData,
            'categories' => $this->categoryService->categories,
            'posts' => $this->postService->getAll(),
        ]);
    }

    #[Route('/login', methods: ['GET'])]
    public function login(): Response
    {
        return $this->render('login.html.twig', [
            'test' => "adel saljem neki tekst iz controllera u view",
            'categories' => $this->categoryService->categories
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
        return $this->render('register.html.twig', ['categories' => $this->categoryService->categories]);
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

    #[Route('/admin' ,name: 'admin')]
    public function admin(): Response
    {
        $userData = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : null;
        $users = $this->userService->getAllUsers();

        $formattedUsers = array_map(function ($user) {
            return [
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ];
        }, $users);

        return $this->render('admin.html.twig', [
            'users' => $formattedUsers,
            'user_data' => $userData,
            'categories' => $this->categoryService->categories,
        ]);
    }

    #[Route('/category/{id}' ,name: 'category')]
    public function category($id): Response
    {
        $userData = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : null;
        $posts = $this->postService->getAllByCategory($id);

        return $this->render('category.html.twig', [
            'user_data' => $userData,
            'posts' => $posts,
            'categories' => $this->categoryService->categories,
            'category' => $this->categoryService->findById($id)
        ]);
    }

    #[Route('/post/add', name: 'post-add')]
    public function addPost(Request $request): Response
    {
        $userData = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : null;
    
        // Get the post date and format it
        return $this->render('addpost.html.twig', [
            'user_data' => $userData,
            'categories' => $this->categoryService->categories,
        ]);
    }

    #[Route('/post/{id}', name: 'post')]
    public function post($id): Response
    {
        $userData = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : null;
        $post = $this->postService->findById($id);
    
        // Get the post date and format it
        $postDate = $post->getCrdate(); // Assuming 'createdAt' is the property for the article date
        $postDate = isset($postDate) ? $postDate : new DateTime();
        $formattedPostDate = $postDate->format('F j, Y H:i:s');

    
        // Create a DateTime object representing the formatted date
        $dateTimeObject = new DateTime($formattedPostDate);
    
        return $this->render('post.html.twig', [
            'user_data' => $userData,
            'post' => $post,
            'formatted_date' => $dateTimeObject, // Pass the DateTime object to the Twig template
            'categories' => $this->categoryService->categories,
            'category' => $this->categoryService->findById($post->getCategory()->getId())
        ]);
    }
}
