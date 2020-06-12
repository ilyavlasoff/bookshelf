<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function displayPage()
    {
        return $this->render('main_page.html.twig', [

        ]);
    }
}