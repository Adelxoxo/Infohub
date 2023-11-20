<?php
namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Post;
use App\Repository\PostRepository;
use Exception;
Use Symfony\Component\HttpFoundation\Request;

class PostService {
    private ManagerRegistry $managerRegistry;
    private PostRepository $repository;

    function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        $this->repository = $this->managerRegistry->getRepository(Post::class);
    }

    public function getAll() {
        return $this->repository->findAll();
    }

    public function findById($id) {
        return $this->repository->find($id);
    }

    public function getAllByCategory($id) {
        return $this->repository->findBy([
            "category" => $id
        ]);
    }

    /**
     * @param $category
     */
    public function store(Post $post) {
    }
}