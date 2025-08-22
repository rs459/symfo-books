<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface as Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface as EntityManager;

final class AuthorController extends AbstractController
{
    #[Route('/api/authors', name: 'app_author', methods: ['GET'])]
    public function getAllAuthors(AuthorRepository $authorRepository, Serializer $serializer): JsonResponse
    {
        $authorlist = $authorRepository->findAll();
        $jsonAuthorList = $serializer->serialize($authorlist, 'json', ['groups' => ['getAuthors']]);
        return new JsonResponse($jsonAuthorList, Response::HTTP_OK, [], true);

    }

    #[Route('/api/author/{id}', name: 'app_author_detail', methods: ['GET'])]
    public function getAuthor(Author $author, Serializer $serializer): JsonResponse
    {
        $author = $serializer->serialize($author, 'json', ['groups' => ['getAuthors']]);
        return new JsonResponse($author, Response::HTTP_OK, [], true);
    }

    #[Route('/api/author', name: 'app_author_create', methods: ['POST'])]
    public function createAuthor(Request $request, EntityManager $em, Serializer $serializer, ValidatorInterface $validator): JsonResponse
    {
        $author = $serializer->deserialize($request->getContent(), Author::class, 'json');

        // On vérifie les erreurs
        $errors = $validator->validate($author);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize(
                $errors,
                'json'
            ), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($author);
        $em->flush();

        $jsonAuthor = $serializer->serialize($author, 'json', ['groups' => ['getAuthors']]);
        return new JsonResponse($jsonAuthor, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/author/{id}', name: 'app_author_update', methods: ['PUT'])]
    public function updateAuthor(Request $request, Author $author, EntityManager $em, Serializer $serializer, ValidatorInterface $validator): JsonResponse
    {
        $updatedAuthor = $serializer->deserialize($request->getContent(), Author::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $author]);

        // On vérifie les erreurs
        $errors = $validator->validate($updatedAuthor);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize(
                $errors,
                'json'
            ), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($updatedAuthor);
        $em->flush();

        $jsonAuthor = $serializer->serialize($updatedAuthor, 'json', ['groups' => ['getBooks']]);
        return new JsonResponse($jsonAuthor, Response::HTTP_OK, [], true);
    }

    #[Route('/api/author/{id}', name: 'app_author_delete', methods: ['DELETE'])]
    public function deleteAuthor(Author $author, EntityManager $em): JsonResponse
    {
        $em->remove($author);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}