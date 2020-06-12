<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\AddBookFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\String\Slugger\SluggerInterface;

class AddBookPageController extends AbstractController
{

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @Route("/add", name="add_book")
     */
    public function addBook(Request $request, ParameterBagInterface $parameterBag)
    {
        $newBookItem = new Book();
        $form = $this->createForm(AddBookFormType::class, $newBookItem);
        $form->handleRequest($request);

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
                    $bookFile->move($parameterBag->get('bookDataPath'), $newFileName);
                    $newBookItem->setFilePath($newFileName);
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
                    $bookCover->move($parameterBag->get('coverDataPath'), $newCoverName);
                    $newBookItem->setCoverPath($newCoverName);
                }
                catch (FileException $ex)
                {
                    $form->addError(new FormError('Can\' save uploaded cover file. Contact technical support.'));
                }
            }

            if (!count($form->getErrors()))
            {
                $newBookItem->setUser($currentUser);
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($newBookItem);
                $manager->flush();

                return new RedirectResponse($this->generateUrl('main_page'));
            }
            else
            {
                if ($newCoverName && file_exists($parameterBag->get('coverDataPath') . '/' . $newCoverName))
                {
                    unlink($parameterBag->get('coverDataPath') . '/' . $newCoverName);
                }
                if ($newFileName && file_exists($parameterBag->get('bookDataPath') . '/' . $newFileName))
                {
                    unlink($parameterBag->get('bookDataPath') . '/' . $newFileName);
                }
            }
        }

        return $this->render('book_add.html.twig', [
            'addBookForm' => $form->createView()
        ]);
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
}