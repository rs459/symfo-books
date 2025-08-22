<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        //Création Users
        $user = new User();
        $user->setEmail('user@bookapi.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        // Création d'un administrateur
        $admin = new User();
        $admin->setEmail('admin@bookapi.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        // Création Auteurs
        $listAuthors = [];
        for ($i = 0; $i < 5; $i++) {
            $author = new Author();
            $author->setFirstName('Prénom ' . $i);
            $author->setLastName('Nom ' . $i);
            $listAuthors[] = $author;
            $manager->persist($author);
        }

        // Création d'une vingtaine de livres ayant pour titre
        for ($i = 0; $i < 20; $i++) {
            $book = new Book();
            $book->setTitle('Livre ' . $i);
            $book->setCoverText('Quatrième de couverture numéro :
            ' . $i);

            $book->setAuthor($listAuthors[array_rand($listAuthors)]);
            $manager->persist($book);
        }

        $manager->flush();
    }
}