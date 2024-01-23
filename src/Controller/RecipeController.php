<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;

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

        return $this->json([
            'message' => 'Recette ajoutee',
        ]);
    }
}
