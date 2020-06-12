<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DownloadBookFileController extends AbstractController
{
    /**
     * @Route("/download/{id}", name="download_book")
     */
    public function downloadFile(string $id, ParameterBagInterface $parameterBag)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);
        if (! $book)
        {
            return new Response('Book not found', Response::HTTP_NOT_FOUND);
        }
        $fileAbsolutePath = $parameterBag->get('bookDataPath') . '/' . $book->getFilePath();
        if (! (file_exists($fileAbsolutePath) && is_readable($fileAbsolutePath)))
        {
            return new Response('File not found', Response::HTTP_NOT_FOUND);
        }
        $fileContent = file_get_contents($fileAbsolutePath);
        $response = new Response();
        $response->headers->set('Content-type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $book->getFilePath() ));
        $response->setContent($fileContent);
        $response->setStatusCode(200);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }
}