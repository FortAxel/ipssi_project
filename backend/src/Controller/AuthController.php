<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\DeleteAccountInput;
use App\Dto\RegisterInput;
use App\Dto\UpdateProfileInput;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
        private readonly UserSerializer $userSerializer,
    ) {
    }

    #[Route('/api/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(#[MapRequestPayload] RegisterInput $input): JsonResponse
    {
        if ($this->userRepository->findOneByEmail($input->email) !== null) {
            return $this->json([
                'error' => 'email_already_used',
                'message' => 'Cette adresse e-mail est déjà utilisée.',
            ], Response::HTTP_CONFLICT);
        }

        $user = (new User())
            ->setFirstName($input->firstName)
            ->setLastName($input->lastName)
            ->setEmail($input->email)
            ->setRoles([]);

        $user->setPassword($this->passwordHasher->hashPassword($user, $input->password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($this->userSerializer->toArray($user), Response::HTTP_CREATED);
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json($this->userSerializer->toArray($user));
    }

    #[Route('/api/me', name: 'api_me_update', methods: ['PATCH'])]
    public function updateProfile(#[MapRequestPayload] UpdateProfileInput $input): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $input->currentPassword)) {
            return $this->json([
                'error' => 'invalid_password',
                'message' => 'Mot de passe actuel incorrect.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if ($input->email !== null && trim($input->email) !== '') {
            $email = strtolower(trim($input->email));
            if ($email !== $user->getEmail()) {
                $existing = $this->userRepository->findOneByEmail($email);
                if ($existing !== null && $existing->getId() !== $user->getId()) {
                    return $this->json([
                        'error' => 'email_already_used',
                        'message' => 'Cette adresse e-mail est déjà utilisée.',
                    ], Response::HTTP_CONFLICT);
                }
                $user->setEmail($email);
            }
        }

        if ($input->newPassword !== null && $input->newPassword !== '') {
            $user->setPassword($this->passwordHasher->hashPassword($user, $input->newPassword));
        }

        $this->entityManager->flush();

        return $this->json($this->userSerializer->toArray($user));
    }

    #[Route('/api/me', name: 'api_me_delete', methods: ['DELETE'])]
    public function deleteAccount(#[MapRequestPayload] DeleteAccountInput $input): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $input->currentPassword)) {
            return $this->json([
                'error' => 'invalid_password',
                'message' => 'Mot de passe incorrect.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
