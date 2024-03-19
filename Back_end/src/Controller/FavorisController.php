<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Entity\Produits;
use App\Entity\Users;
use App\Repository\FavorisRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    private $favorisRepository;
    private $entityManager;

    public function __construct(
        FavorisRepository $favorisRepository, 
        EntityManagerInterface $entityManager
    ) {
        $this->favorisRepository = $favorisRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/favoris/ajouter", name="ajouter_favoris", methods="POST")
     */
    public function ajouterFavoris(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $userid = $data['userid'];
        $produitid = $data['produitid'];

        $favorisExistant = $this->favorisRepository->findOneBy(['userid' => $userid, 'produitid' => $produitid]);

        if ($favorisExistant) {
            return $this->json(['error' => 'Ce produit est déjà enregistré dans la liste de favoris de cet utilisateur'], Response::HTTP_BAD_REQUEST);
        }

        $favoris = new Favoris();
        $favoris->setUserid($userid);
        $favoris->setProduitid($produitid);
        $favoris->setDate(new DateTime());

        $this->entityManager->persist($favoris);
        $this->entityManager->flush();

        return $this->json(['message' => 'Favori ajouté avec succès'], Response::HTTP_CREATED);
    }


    /**
     * @Route("/favoris/supprimer", name="supprimer_favoris", methods="DELETE")
     */
    public function deleteFavoris(Request $request, FavorisRepository $favorisRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $userid = $data['userid'];
        $produitid = $data['produitid'];
        $favoris = $favorisRepository->findOneBy(['userid' => $userid, 'produitid' => $produitid]);

        if ($favoris) {
            $entityManager->remove($favoris);
            $entityManager->flush();

            return $this->json(['message' => 'Favori supprimé avec succès']);
        } else {
            return $this->json(['message' => 'Favori non trouvé'], 404);
        }
    }

    /**
     * @Route("/favoris/supprimer-tous/{userid}", name="supprimer_tous_favoris", methods="DELETE")
     */
    public function deleteAll(int $userid, FavorisRepository $favorisRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $favoris = $favorisRepository->findBy(['userid' => $userid]);

        foreach ($favoris as $favori) {
            $entityManager->remove($favori);
        }

        $entityManager->flush();

        return new JsonResponse(['message' => 'Tous les favoris ont été supprimés avec succès.'], JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/favoris/{id}/liste", name="get_favoris", methods="GET")
     */
    // public function getFavoris(Produits $produits, FavorisRepository $favorisRepository): JsonResponse
    // {
    //     $produitId = $produits->getId();
    //     $favoriss = $favorisRepository->findBy(['produitid' => $produitId]);

    //     $data = [];
    //     foreach ($favoriss as $favoris) {
    //         $data[] = [
    //             'id' => $favoris->getId(),
    //             'userid' => $favoris->getUserid(),
    //             'produitid' => $produitId,
    //         ];
    //     }

    //     return $this->json($data);
    // }

    public function getFavoris(FavorisRepository $favorisRepository, $id): JsonResponse
    {
        
        $favoris = $favorisRepository->findBy(['produitid' => $id]);
        $produitExists = count($favoris) > 0;

        return $this->json(['produitExists' => $produitExists]);
    }

    /**
     * @Route("/favoris/all/{userid}", name="get_all_favoris", methods="GET")
     */
    public function getAllFavorisUser(int $userid, FavorisRepository $favorisRepository): JsonResponse
    {
        $count = $favorisRepository->count(['userid' => $userid]);
        
        return $this->json(['count' => $count]);
    }
    
}