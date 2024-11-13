<?php

namespace App\Controller;

use App\Entity\SearchItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $em, CacheInterface $cache): Response
    {
        $query = $request->query->all();
        $criteria = [];

        // Apply filters based on query parameters
        if (isset($query['name'])) {
            $criteria['name'] = $query['name'];
        }
        if (isset($query['category'])) {
            $criteria['category'] = $query['category'];
        }

        // Cache the query result
        $cacheKey = md5(json_encode($query));
        $items = $cache->get($cacheKey, function() use ($em, $criteria) {
            return $em->getRepository(SearchItem::class)->findBy($criteria);
        });

        return $this->json($items);
    }
}
