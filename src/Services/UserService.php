<?php
namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\Role;
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
    public function storeUser(User $user): array
    {
        // Check if user already exists with this email
        $existingUser = $this->managerRegistry->getRepository(User::class)
            ->findOneBy(['email' => $user->getEmail()]);
        if ($existingUser) {
            throw new \Exception("User with this email already exists");
        }

        // Hash the password before storing
        $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));

        // Set a default role (assuming role ID 2 is "user")
        $role = $this->managerRegistry->getRepository(Role::class)->find(2);
        $user->setRole($role);

        // Save the user
        $manager = $this->managerRegistry->getManager();
        $manager->persist($user);
        $manager->flush();

        return [
            "data" => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'role' => $user->getRole()->getName(),
            ],
            "message" => "User registered successfully"
        ];
    }

    /**
     * @param $email
     * @param $password
     */
    public function findUserByEmailAndPassword(string $email, string $password): ?User
    {
        $user = $this->managerRegistry->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            return null;
        }

        // Use password_verify for checking hashed passwords
        if (password_verify($password, $user->getPassword())) {
            return $user;
        }

        return null;
    }
}