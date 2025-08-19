<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface as Serializer;

final class AuthorController extends AbstractController
{
    #[Route('/api/authors', name: 'app_author')]
    public function getAllAuthors(AuthorRepository $authorRepository, Serializer $serializer): JsonResponse
    {
        $authorlist = $authorRepository->findAll();
        $jsonAuthorList = $serializer->serialize($authorlist, 'json', ['groups' => ['getBooks']]);
        return new JsonResponse($jsonAuthorList, Response::HTTP_OK, [], true);

    }

    #[Route('/api/author/{id}', name: 'app_author_detail')]
    public function getAuthor(Author $author, Serializer $serializer): JsonResponse
    {
        $author = $serializer->serialize($author, 'json', ['groups' => ['getBooks']]);
        return new JsonResponse($author, Response::HTTP_OK, [], true);
    }
}