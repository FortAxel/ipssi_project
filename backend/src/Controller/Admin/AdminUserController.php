<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Dto\AdminUserInput;
use App\Dto\AdminUserUpdateInput;
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

#[Route('/api/admin/users')]
final class AdminUserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserSerializer $userSerializer,
    ) {
    }

    #[Route('', name: 'api_admin_users_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $users = $this->userRepository->findAllOrdered();

        return $this->json([
            'items' => array_map(
                fn (User $user) => $this->userSerializer->toAdminArray($user),
                $users,
            ),
        ]);
    }

    #[Route('', name: 'api_admin_users_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] AdminUserInput $input): JsonResponse
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
            ->setRoles($input->isAdmin ? ['ROLE_ADMIN'] : []);

        $user->setPassword($this->passwordHasher->hashPassword($user, $input->password));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($this->userSerializer->toAdminArray($user), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_admin_users_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function update(int $id, #[MapRequestPayload] AdminUserUpdateInput $input): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if ($user === null) {
            return $this->json(['error' => 'not_found', 'message' => 'Utilisateur introuvable.'], Response::HTTP_NOT_FOUND);
        }

        if ($input->firstName !== null) {
            $user->setFirstName($input->firstName);
        }
        if ($input->lastName !== null) {
            $user->setLastName($input->lastName);
        }
        if ($input->email !== null) {
            $existing = $this->userRepository->findOneByEmail($input->email);
            if ($existing !== null && $existing->getId() !== $user->getId()) {
                return $this->json([
                    'error' => 'email_already_used',
                    'message' => 'Cette adresse e-mail est déjà utilisée.',
                ], Response::HTTP_CONFLICT);
            }
            $user->setEmail($input->email);
        }
        if ($input->password !== null && $input->password !== '') {
            $user->setPassword($this->passwordHasher->hashPassword($user, $input->password));
        }
        if ($input->isAdmin !== null) {
            $user->setRoles($input->isAdmin ? ['ROLE_ADMIN'] : []);
        }

        $this->entityManager->flush();

        return $this->json($this->userSerializer->toAdminArray($user));
    }

    #[Route('/{id}', name: 'api_admin_users_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $current = $this->getUser();
        if ($current instanceof User && $current->getId() === $id) {
            return $this->json([
                'error' => 'cannot_delete_self',
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->find($id);
        if ($user === null) {
            return $this->json(['error' => 'not_found', 'message' => 'Utilisateur introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
