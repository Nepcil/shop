<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Repository\PanierRepository;
use App\Entity\Produits;
use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Repository\ProduitsRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class PanierController extends AbstractController
{
    private $panierRepository;
    private $entityManager;
    private $produitsRepository;
    private $usersRepository;

    public function __construct(
        PanierRepository $panierRepository, 
        ProduitsRepository $produitsRepository, 
        EntityManagerInterface $entityManager,
        UsersRepository $usersRepository
        )
    {
        $this->panierRepository = $panierRepository;
        $this->entityManager = $entityManager;
        $this->produitsRepository = $produitsRepository;
        $this->usersRepository = $usersRepository;
    }

    /**
     * @Route("/panier", name="panier_list", methods="GET")
     */
    public function getAllPanierItems(Request $request, PanierRepository $panierRepository, ProduitsRepository $produitsRepository): JsonResponse
    {
        $userId = $request->query->get('userId'); 
        $items = $panierRepository->findBy(['userid' => $userId]);

        if ($userId) {

            $data = [];
            foreach ($items as $product) {
                $ProductInfo = $produitsRepository->findOneBy(['id' => $product->getProduitid()]);
                $data[] = [
                    'id' => $ProductInfo->getId(),
                    'name' => $ProductInfo->getNomduproduit(),
                    'price' => $ProductInfo->getPrix(),
                    'description' => $ProductInfo->getDescription(),
                    'imageUrl' => $ProductInfo->getImageUrl(),
                    'categorie' => $ProductInfo->getCategorieid(),
                    'quantite' => $product->getQuantite(),
                ];
            }

        return new JsonResponse($data);

        }
    }

    /**
     * @Route("/panier/add/", name="panier_add", methods="POST")
     */
    public function ajouterAuPanier(Request $request, UsersRepository $usersRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        if ($data !== null && isset($data['id']) && isset($data['quantite']) && isset($data['userId'])) {

            $produitId = $data['id'];
            $quantite = $data['quantite'];
            $userId = $data['userId']; 

            $user = $usersRepository->find($userId);
            if (!$user) {
                return $this->json(['error' => 'Utilisateur introuvable'], 404);
            }

            $produit = $this->entityManager->getRepository(Produits::class)->find($produitId);
            if (!$produit) {
                return $this->json(['error' => 'Produit introuvable'], 404);
            }

            $panier = $this->entityManager->getRepository(Panier::class)->findOneBy(['produitid' => $produit->getId()]);
            if ($panier) {
                $panier->setQuantite($panier->getQuantite() + $quantite);
            } else {
                $panier = new Panier();
                $panier->setProduitid($produit->getId());
                $panier->setQuantite($quantite);
                $panier->setUserid($userId);
                $panier->setDate(new DateTime()); 

                $this->entityManager->persist($panier);
            }

            $this->entityManager->flush();

            return $this->json(['success' => true, 'message' => 'Produit ajouté au panier avec succès'], 200);
        } else {
            return $this->json(['error' => 'Données manquantes ou incorrectes'], 400);
        }
    }

    /**
     * @Route("/panier/delete/{id}", name="panier_delete", methods="DELETE")
     */
    public function supprimerDuPanier($id): Response
    {
        $panier = $this->panierRepository->findOneBy(['produitid' => $id]);

        if (!$panier) {
            throw $this->createNotFoundException('Panier non trouvé');
        }

        // Supprimer le panier de la base de données
        $this->entityManager->remove($panier);
        $this->entityManager->flush();

        return $this->json(['success' => true, 'message' => 'produit supprimé du panier!'], 200);
    }

}
