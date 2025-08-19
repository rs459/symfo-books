<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

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