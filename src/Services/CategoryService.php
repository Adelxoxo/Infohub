<?php
namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Exception;
Use Symfony\Component\HttpFoundation\Request;

class CategoryService {
    private ManagerRegistry $managerRegistry;
    private CategoryRepository $categoryRepository;
    public $categories;

    function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        $this->categoryRepository = $this->managerRegistry->getRepository(Category::class);
        $formatedCategories = array_map(function ($category) {
            return [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'url' => '/category/' . $category->getId(),
            ];
        }, $this->getAll());
        $this->categories = $formatedCategories;
    }

    public function getAll() {
        $categories = $this->categoryRepository->findAll();
        return $categories;
    }

    public function findById($id) {
        $category = $this->categoryRepository->find($id);
        return $category;
    }

    /**
     * @param $category
     */
    public function store($category) {
        // Check if category already exists in DB
        $existing = $this->categoryRepository->findOneBy([
            "name" => $category
        ]);

        if(!isset($existing)) {
            $manager = $this->managerRegistry->getManager();

            // Crate new category
            $obj = new Category();
            $obj->setName($category);
            // Store category to db
            $manager->persist($obj);
            $manager->flush();
        } else {
            // Throw exception
            throw new Exception("Category already exists.", 400);
        }

        return [
            "message" => "Category created successfully.",
            "data" => [
                "id" => $category->getId(),
                "name" => $category->getName()
            ]
        ];
    }
}