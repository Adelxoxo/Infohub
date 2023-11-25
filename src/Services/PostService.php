<?php
namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Post;
use App\Entity\Category;
use App\Repository\PostRepository;
use DateTime;
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
        return $this->repository->findBy([], ["crdate" => 'DESC']); // Sorting posts by creation date, descending order - i.e. newest
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
    public function store(Request $request) {
        $postData = json_decode($request->getContent(), true);
        $categoryId = $postData['categoryId'];
        $postContent = $postData['content'];
        $postName = $postData['postName'];
        $postBanner = $postData['postBanner'];

        $post = new Post();
        $post->setName($postName);
        $post->setCrdate(new DateTime());
        $post->setLastModified(new DateTime());
        $post->setLongText($postContent);
        $post->setBanner($postBanner);
        $category = $this->managerRegistry->getRepository(Category::class)->find($categoryId);
        $post->setCategory($category);

        $this->managerRegistry->getManager()->persist($post);
        $this->managerRegistry->getManager()->flush();
    }
}