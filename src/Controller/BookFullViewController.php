<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookFullViewController extends AbstractController
{
    /**
     * @Route("/book/{id}", name="book_full_page")
     */
    public function displayPage(string $id, Request $request)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)
            ->find($id);
        return $this->render('book_full_page.html.twig', [
            'book' => $book
        ]);
    }
}