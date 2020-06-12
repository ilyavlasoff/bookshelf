<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
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

        /*if (! ($userId && $offset && $count))
        {
            return new JsonResponse(json_encode(['error' => 'Bad params']), Response::HTTP_BAD_REQUEST);
        }*/

        try
        {
            $user = $this->getDoctrine()->getRepository(User::class)->find($userId);
        }
        catch (Exception $ex)
        {
            return new JsonResponse(json_encode(['error' => 'User not found']), Response::HTTP_NOT_FOUND);
        }

        /* @var $entityManager EntityManager */
        $entityManager = $this->getDoctrine()->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $itemsQuery = $queryBuilder->select('b')
            ->from('App\Entity\Book', 'b')
            ->where('b.user = :user')
            ->setParameter('user', $user)
            ->orderBy('b.readDate')
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery();

        $itemsResult = $itemsQuery->getResult(Query::HYDRATE_ARRAY);

        return new JsonResponse(json_encode([
            'items' => $itemsResult,
            'count' => count($itemsResult)
        ]));

    }
}