<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface as EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface as Serializer;

final class BookController extends AbstractController
{
    #[Route('/api/books', name: 'book', methods: ['GET'])]
    public function getBookList(BookRepository $bookRepository, Serializer $serializer): JsonResponse
    {
        $booklist = $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($booklist, 'json', ['groups' => ['getBooks']]);

        return new JsonResponse($jsonBookList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/books/{id}', name: 'detailBook', methods: ['GET'])]
    public function getDetailBook(Book $book, Serializer $serializer): JsonResponse
    {
        $jsonBook = $serializer->serialize($book, 'json', ['groups' => ['getBooks']]);
        return new JsonResponse(
            $jsonBook,
            Response::HTTP_OK,
            [],
            true
        );

    }

    #[Route('/api/books/{id}', name: 'deleteBook', methods: ['DELETE'])]
    public function deleteBook(Book $book, EntityManager $em): JsonResponse
    {
        $em->remove($book);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/books', name: 'createBook', methods: ['POST'])]
    public function createBook(Request $request, EntityManager $em, UrlGeneratorInterface $urlGenerator, Serializer $serializer): JsonResponse
    {
        $book = $serializer->deserialize($request->getContent(), Book::class, 'json');
        $em->persist($book);
        $em->flush();

        $jsonBook = $serializer->serialize($book, 'json', ['groups' => ['getBooks']]);
        $location = $urlGenerator->generate('detailBook', ['id' =>
        $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ['Location' => $location], true);
    }
}