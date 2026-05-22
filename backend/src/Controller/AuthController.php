<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\RegisterInput;
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
}
