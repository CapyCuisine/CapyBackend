<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Name;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'app_recipe')]
    public function index(): Response
    {
        return $this->render('recipe/index.html.twig', [
            'controller_name' => 'RecipeController',
        ]);
    }

    #[Route('/recipe/create', name: 'app_recipe_create', methods: ['POST'])]
    public function createRecipe(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jsonData = json_decode($request->getContent(), true);
        if ($jsonData === null) {
            return new Response('Invalid JSON data', Response::HTTP_BAD_REQUEST);
        }

        $recipe = new Recipe();
        $recipe->setName($jsonData['name']);
        $recipe->setIngredients($jsonData['ingredients']);
        $recipe->setCategory($jsonData['category']);
        $recipe->setDescription($jsonData['desc']);
        $recipe->setImage("void");
        $recipe->setStep($jsonData['step']);
        $recipe->setNumberPortions($jsonData['number_portions']);
        $recipe->setCookingTime($jsonData['cooking_time']);
        $recipe->setUserID($jsonData['userID']);

        $entityManager->persist($recipe);
        $entityManager->flush();


        // You can customize the response based on success or failure
        return $this->json(['message' => 'Recipe created successfully']);
    }

    #[Route('/recipe/show/all', name: "app_recipe_show_all", methods: ['GET'])]
    public function showAllRecipe(RecipeRepository $recipeRepository): Response
    {

        $recipes = $recipeRepository->findAll();



        foreach ($recipes as $recipe) {
            $data[] = [
                'id' => $recipe->getId(),
                'name' => $recipe->getName(),
                'ingredients' => $recipe->getIngredients(),
                'category' => $recipe->getCategory(),
                'desc' => $recipe->getDescription(),
                'step' => $recipe->getStep(),
                'number_portions' => $recipe->getNumberPortions(),
                'cooking_time' => $recipe->getCookingTime(),
                'userID' => $recipe->getUserId()
            ];
        }

        return $this->json($data, Response::HTTP_OK);
    }


    #[Route('/recipe/show/{id}', name: "app_recipe_show_by_id", methods: ['GET'])]
    public function showRecipeById(int $id, RecipeRepository $recipeRepository)
    {
        $recipe = $recipeRepository->find($id);
        if ($recipe == null) {
            return $this->json(['message' => 'Pas de recette avec cet id']);
        }

        $data = [
            'id' => $recipe->getId(),
            'name' => $recipe->getName(),
            'ingredients' => $recipe->getIngredients(),
            'category' => $recipe->getCategory(),
            'desc' => $recipe->getDescription(),
            'step' => $recipe->getStep(),
            'number_portions' => $recipe->getNumberPortions(),
            'cooking_time' => $recipe->getCookingTime(),
            'userID' => $recipe->getUserID()
        ];

        return $this->json($data);
    }
}
