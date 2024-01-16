<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;



class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordEncoder, UserRepository $userRepository, JWTTokenManagerInterface $tokenManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = $userRepository->findOneBy(['username' => $data['username']]);
            if (!$user || !$passwordEncoder->isPasswordValid($user, $data['password'])) {
                return new JsonResponse(['message' => 'Pseudo ou mot de passe incorrect'], 401);
            }
            $token = $tokenManager->create($user);
            return new JsonResponse(['token' => $token]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            return new JsonResponse(['message' => 'Erreur lors de la connection ', 'error' => $errorMessage]);
        }
    }
}
