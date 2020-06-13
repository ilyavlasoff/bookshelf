<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\AddBookFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\String\Slugger\SluggerInterface;

class AddBookPageController extends AbstractController
{

    private $slugger;
    private $parameterBag;

    public function __construct(SluggerInterface $slugger, ParameterBagInterface $parameterBag)
    {
        $this->slugger = $slugger;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @Route("/edit/{id}", name="edit_book")
     */
    public function editBook(string $id, Request $request)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if(! $book)
        {
            return new Response('Book not found', Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(AddBookFormType::class, $book);
        $form->handleRequest($request);
        return $this->creationBook($form, $book);
    }

    /**
     * @Route("/add", name="add_book")
     */
    public function addBook(Request $request)
    {
        $newBookItem = new Book();
        $form = $this->createForm(AddBookFormType::class, $newBookItem);
        $form->handleRequest($request);

        return $this->creationBook($form, $newBookItem, true);
    }

    /**
     * @Route("/delete/{id}", name="delete_book")
     */
    public function deleteBook(string $id)
    {
        $doctrine = $this->getDoctrine();
        $book = $doctrine->getRepository(Book::class)->find($id);
        if ($book)
        {
            $doctrine->getManager()->remove($book);
            $doctrine->getManager()->flush();
        }
        return new RedirectResponse($this->generateUrl("main_page"));
    }

    /**
     * @param File $file
     * @return string
     */
    private function createNewFilename(File $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        return $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
    }

    private function creationBook(FormInterface $form, Book $book, bool $addNew = false)
    {
        if ($form->isSubmitted() && $form->isValid())
        {
            $currentUser = $this->getUser();
            if(!$currentUser)
            {
                throw new Exception('Unauthorized');
            }
            $bookCover = $form->get('cover')->getData();
            $bookFile = $form->get('bookFile')->getData();

            if ($bookFile)
            {
                $newFileName = $this->createNewFilename($bookFile);

                try
                {
                    $bookFile->move($this->parameterBag->get('bookDataPath'), $newFileName);
                    $book->setFilePath($newFileName);
                }
                catch (FileException $ex)
                {
                    $form->addError(new FormError('Can\' save uploaded book file. Contact technical support.'));
                }
            }

            if ($bookCover)
            {
                $newCoverName = $this->createNewFilename($bookCover);

                try
                {
                    $bookCover->move($this->parameterBag->get('coverDataPath'), $newCoverName);
                    $book->setCoverPath($newCoverName);
                }
                catch (FileException $ex)
                {
                    $form->addError(new FormError('Can\' save uploaded cover file. Contact technical support.'));
                }
            }

            if (!count($form->getErrors()))
            {
                $book->setUser($currentUser);
                $manager = $this->getDoctrine()->getManager();
                if ($addNew) {
                    $manager->persist($book);
                }
                $manager->flush();

                return new RedirectResponse($this->generateUrl('main_page'));
            }
            else
            {
                if ($newCoverName && file_exists($this->parameterBag->get('coverDataPath') . '/' . $newCoverName))
                {
                    unlink($this->parameterBag->get('coverDataPath') . '/' . $newCoverName);
                }
                if ($newFileName && file_exists($this->parameterBag->get('bookDataPath') . '/' . $newFileName))
                {
                    unlink($this->parameterBag->get('bookDataPath') . '/' . $newFileName);
                }
            }
        }

        return $this->render('book_add.html.twig', [
            'addBookForm' => $form->createView(),
            'editMode' => !$addNew,
            'book' => $book
        ]);
    }
}