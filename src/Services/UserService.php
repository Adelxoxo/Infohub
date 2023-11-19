<?php
namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Exception;
Use Symfony\Component\HttpFoundation\Request;

class UserService {
    private ManagerRegistry $managerRegistry;

    function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getAllUsers() {
        $userRepository = $this->managerRegistry->getRepository(User::class);
        $users = $userRepository->findAll();
        return $users;
    }

    public function findUser($id) {
        $userRepository = $this->managerRegistry->getRepository(User::class);
        $user = $userRepository->find($id);
        return $user;
    }

    public function findUserByEmail($email) {
        $userRepository = $this->managerRegistry->getRepository(User::class);
        $user = $userRepository->findOneBy([
            "email" => $email
        ]);
        
        return $user;
    }

    /**
     * @param $email
     * @param $password
     */
    public function storeUser(User $user) {
        $userRepository = $this->managerRegistry->getRepository(User::class);

        // Check if user already exists in DB
        $existingUser = $userRepository->findOneBy([
            "email" => $user->getemail()
        ]);

        if(!isset($existingUser)) {
            $manager = $this->managerRegistry->getManager();
            // Store user to db
            $manager->persist($user);
            $manager->flush();
        } else {
            // Throw exception
            throw new Exception("User already exists.", 400);
        }

        return [
            "message" => "User created successfully.",
            "data" => [
                "id" => $user->getId(),
                "username" => $user->getUsername(),
                "email" => $user->getEmail()
            ]
        ];
    }

    /**
     * @param $email
     * @param $password
     */
    public function findUserByEmailAndPassword($email, $password) {
        // ToDo if encription is used for stoing passeords ...
        // $password = md5($password) // just example ...

        $userRepository = $this->managerRegistry->getRepository(User::class);
        $user = $userRepository->findOneBy([
            "email" => $email,
            "password" => $password
        ]);
        
        return $user;
    }
}