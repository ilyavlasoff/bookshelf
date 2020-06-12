<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class UserBooksPageController extends AbstractController
{
    /**
     * @Route("/p/{nickname}",name="user_books_page")
     */
    public function displayUserBooks($nickname)
    {
        $doctrine = $this->getDoctrine();
        $users = $doctrine->getRepository(User::class)->findBy(['nickname' => $nickname]);
        $user = null;
        if (!empty($users))
        {
            $user = $users[0];
        }

        return $this->render('user_book_list.html.twig', [
            'user' => $user
        ]);
    }
}