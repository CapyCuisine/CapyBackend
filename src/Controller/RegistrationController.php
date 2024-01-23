<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = new User();
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);
            $password = $passwordEncoder->hashPassword($user, $data['password']);
            $user->setPassword($password);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json(['message' => 'Inscription réussie!']);
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(['error' => 'Adresse e-mail ou/et username deja utilisee.'], 400);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur s est produite lors de l inscription.'], 500);
        }
    }
}
