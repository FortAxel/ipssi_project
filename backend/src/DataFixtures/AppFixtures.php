<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\StoryContentLoader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly StoryContentLoader $storyContentLoader,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = (new User())
            ->setFirstName('Admin')
            ->setLastName('Demo')
            ->setEmail('admin@demo.local')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));

        $parent = (new User())
            ->setFirstName('Parent')
            ->setLastName('Demo')
            ->setEmail('parent@demo.local')
            ->setRoles([]);
        $parent->setPassword($this->passwordHasher->hashPassword($parent, 'parent123'));

        $manager->persist($admin);
        $manager->persist($parent);

        foreach ($this->storyContentLoader->loadAll() as $item) {
            $manager->persist($item['story']);
            foreach ($item['pages'] as $page) {
                $manager->persist($page);
            }
        }

        $manager->flush();
    }
}
