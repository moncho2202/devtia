<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController
{
    /**
     * @Route("/books", name="list_books")
     *
     * @param Request $request
     * @param BookRepository $repository
     * @param FormInterface $bookFilterType
     * @param FilterBuilderUpdaterInterface $filterBuilderUpdater
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function getBooks(
        Request $request,
        BookRepository $repository,
        FormInterface $bookFilterType,
        FilterBuilderUpdaterInterface $filterBuilderUpdater,
        PaginatorInterface $paginator
    ){
        $filterBuilder = $repository->createQueryBuilder('b')->orderBy('b.updatedAt', 'desc');

        if ($request->query->has($bookFilterType->getName())) {
            $bookFilterType->submit($request->query->get($bookFilterType->getName()));
            $filterBuilderUpdater->addFilterConditions($bookFilterType, $filterBuilder);
        }

        $pagination = $paginator->paginate(
            $filterBuilder, /* query NOT result */
            $request->query->get('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('book/list.html.twig', [
            'filter' => $bookFilterType->createView(),
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/books/new", name="new_book")
     *
     * @param Request $request
     * @param FormInterface $bookType
     * @return Response
     */
    public function newBook(
        Request $request,
        FormInterface $bookType
    ){
        $book = new Book();
        $bookType->setData($book);

        $bookType->handleRequest($request);

        if ($bookType->isSubmitted() && $bookType->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('list_books');
        }

        return $this->render('book/new.html.twig', [
            'form' => $bookType->createView(),
        ]);
    }

    /**
     * @Route("/books/{book}", name="edit_book")
     * @ParamConverter("book", options={"mapping": {"book": "slug"}})
     *
     * @param Request $request
     * @param Book $book
     * @param FormInterface $bookType
     * @return Response
     */
    public function editBook(
        Request $request,
        Book $book,
        FormInterface $bookType
    ){
        $bookType->setData($book);

        $bookType->handleRequest($request);

        if ($bookType->isSubmitted() && $bookType->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('list_books');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $bookType->createView(),
        ]);
    }

    /**
     * @Route("/books/{book}/delete", name="delete_book")
     * @ParamConverter("book", options={"mapping": {"book": "slug"}})
     *
     * @param Request $request
     * @param Book $book
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function deleteBook(
        Request $request,
        Book $book,
        EntityManagerInterface $entityManager
    ){
        $entityManager->remove($book);
        $entityManager->flush();

        return ($referer = $request->headers->get('referer'))
            ? $this->redirect($referer)
            : $this->redirectToRoute('list_books');
    }
}
