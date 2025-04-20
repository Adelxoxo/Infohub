<?php
namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Post;
use App\Entity\Category;
use App\Repository\PostRepository;
use DateTime;
use Exception;
Use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function findById($id)
    {
        $post = $this->repository->find($id);
        if (!$post) {
            throw new \Exception("Post not found for ID $id");
        }
        return $post;
    }

    public function getAllByCategory($id) {
        return $this->repository->findBy([
            "category" => $id
        ]);
    }

    /**
     * @param $category
     */
    public function store(Request $request): int {
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

        return $post->getId();
    }
    public function update(Request $request, int $id): int
    {
        $post = $this->repository->find($id);
        if (!$post) {
            throw new \Exception("Post not found for ID $id");
        }
    
        $postData = json_decode($request->getContent(), true);
    
        // Check if required fields are present in the request data
        if (!isset($postData['categoryId'], $postData['content'], $postData['postName'], $postData['postBanner'])) {
            throw new \Exception("Required fields are missing in the request data");
        }
    
        $categoryId = $postData['categoryId'];
        $postContent = $postData['content'];
        $postName = $postData['postName'];
        $postBanner = $postData['postBanner'];
    
        $category = $this->managerRegistry->getRepository(Category::class)->find($categoryId);
        if (!$category) {
            throw new \Exception("Category not found for ID $categoryId");
        }
    
        $post->setName($postName);
        $post->setLongText($postContent);
        $post->setBanner($postBanner);
        $post->setCategory($category);
        $post->setLastModified(new DateTime());
    
        $this->managerRegistry->getManager()->flush();
    
        return $post->getId();
    }

    public function updatePost(int $id, string $postName, string $postBanner, string $postContent, int $categoryId): void
    {
        $post = $this->repository->find($id);
        if (!$post) {
            throw new \Exception("Post not found for ID $id");
        }
    
        $category = $this->managerRegistry->getRepository(Category::class)->find($categoryId);
        if (!$category) {
            throw new \Exception("Category not found for ID $categoryId");
        }
    
        $post->setName($postName);
        $post->setBanner($postBanner);
        $post->setLongText($postContent);
        $post->setCategory($category);
        $post->setLastModified(new \DateTime());
    
        $this->managerRegistry->getManager()->flush();
    }

    public function save(Post $post): void
    {
        $manager = $this->managerRegistry->getManager();
        $manager->persist($post);
        $manager->flush();
    }

    public function getFeaturedPosts(): array
    {
        return $this->repository->findBy(['featured' => true], ['crdate' => 'DESC']);
    }

    public function getLatestPosts(int $limit): array
    {
        return $this->repository->findBy([], ['crdate' => 'DESC'], $limit);
    }
}
