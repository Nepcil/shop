<?php

namespace App\Controller;

use App\Repository\PromosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; 

class PromosController extends AbstractController 
{
    public function __construct(){}

    /**
     * @Route("/promos/{id}", name="get_promos", methods={"GET"}) 
     */
    public function getPromos($id, PromosRepository $promosRepository): JsonResponse
    {
        $promosProduct = $promosRepository->find($id); 

        if (!$promosProduct) {
            return new JsonResponse(['error' => 'Aucune promo trouvée'], Response::HTTP_NOT_FOUND);
        }

        $promosData = [
            'id' => $promosProduct->getId(),
            'produitid' => $promosProduct->getProduitid(),
            'pourcent' => $promosProduct->getPourcent(),
            'promotitle' => $promosProduct->getPromoTitle(), 
            'datein' => $promosProduct->getDateIn()->format('Y-m-d H:i:s'), 
            'dateout' => $promosProduct->getDateOut()->format('Y-m-d H:i:s') 
        ];

        return new JsonResponse($promosData, Response::HTTP_OK);
    }
}
