<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;

class LoadBookItemsController extends AbstractController
{
    /**
     * @Route("/ajax/books", name="load_book_items")
     */
    public function loadItems(Request $request)
    {
        if(!$request->isXmlHttpRequest())
        {
            return new Response('Error', Response::HTTP_BAD_REQUEST);
        }
        $userId = $request->request->get('user', null);
        $offset = $request->request->get('offset', null);
        $count = $request->request->get('count', null);

        if ($userId)
        {
            try {
                $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
            } catch (Exception $ex) {
                return new JsonResponse(json_encode(['error' => 'User not found']), Response::HTTP_NOT_FOUND);
            }
        }

        /* @var $entityManager EntityManager */
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('b')
            ->from('App\Entity\Book', 'b')
            ->orderBy('b.readDate')
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery();
        if (isset($user))
        {
            $queryBuilder
            ->where('b.user = :user')
            ->setParameter('user', $user);
        }

        $itemsResult = $queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return new JsonResponse(json_encode([
            'items' => $itemsResult,
            'count' => count($itemsResult)
        ]));

    }

    /**
     * @Route("/cover/delete/{id}", name="delete_cover")
     */
    public function deleteCover(string $id, ParameterBagInterface $parameterBag)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if (! $book)
        {
            return new JsonResponse(json_encode(['error' => 'Invalid item'], Response::HTTP_NOT_FOUND));
        }

        $absolutePath = $parameterBag->get('coverDataPath') . '/' . $book->getCoverPath();

        if(file_exists($absolutePath) && is_writable($absolutePath))
        {
            try {
                unlink($absolutePath);
                $book->setCoverPath(null);
                $this->getDoctrine()->getManager()->flush();
            }
            catch (\Exception $ex)
            {
                return new JsonResponse(json_encode(['error' => 'Can not delete cover'], Response::HTTP_INTERNAL_SERVER_ERROR));
            }
        }
        return new JsonResponse(json_encode(['status' => 'ok']), Response::HTTP_OK);
    }

    /**
     * @Route("/book/delete/{id}", name="delete_book")
     */
    public function deleteBookFile(string $id, ParameterBagInterface $parameterBag)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        if (! $book)
        {
            return new JsonResponse(json_encode(['error' => 'Invalid item'], Response::HTTP_NOT_FOUND));
        }

        $absolutePath = $parameterBag->get('bookDataPath') . '/' . $book->getFilePath();

        if(file_exists($absolutePath) && is_writable($absolutePath))
        {
            try
            {
                unlink($absolutePath);
                $book->setFilePath(null);
                $this->getDoctrine()->getManager()->flush();
            }
            catch (Exception $ex)
            {
                return new JsonResponse(json_encode(['error' => 'Can not delete book'], Response::HTTP_INTERNAL_SERVER_ERROR));
            }
        }
        return new JsonResponse(json_encode(['status' => 'ok']), Response::HTTP_OK);
    }
}