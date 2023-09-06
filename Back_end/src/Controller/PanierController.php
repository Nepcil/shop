<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Repository\PanierRepository;
use App\Entity\Produits;
use App\Repository\ProduitsRepository;
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

    public function __construct(PanierRepository $panierRepository, ProduitsRepository $produitsRepository, EntityManagerInterface $entityManager)
    {
        $this->panierRepository = $panierRepository;
        $this->entityManager = $entityManager;
        $this->produitsRepository = $produitsRepository;
    }

    /**
     * @Route("/panier", name="panier_list", methods={"GET"})
     */
    public function getAllPanierItems(PanierRepository $panierRepository, ProduitsRepository $produitsRepository): JsonResponse
    {
        $items = $panierRepository->findAll();

        // Convertir les entités en tableau
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


    /**
     * @Route("/panier/add/", name="panier_add", methods={"POST"})
     */
    public function ajouterAuPanier(Request $request): Response
    {
        // Récupérer les données envoyées depuis le front-end, on recupère le token depuis l'en-tête de la requête
        $data = json_decode($request->getContent(), true);
        // $tokenstring = $request->headers->get('Authorization');
        // $token = explode(' ', $tokenstring);

        $produitId = $data['id'];
        //return new JsonResponse($produitId);
        $quantite = $data['quantite'];

        // Récupérer le produit depuis la base de données
        $produit = $this->entityManager->getRepository(Produits::class)->find($produitId);

        // Rechercher si le produit existe déjà dans le panier
        $panier = $this->entityManager->getRepository(Panier::class)->findOneBy(['produitid' => $produit->getId()]);

        if ($panier) {
            // Si le produit existe déjà dans le panier, ajouter la quantité spécifiée
            $panier->setQuantite($panier->getQuantite() + $quantite);
        } else {
            // Si le produit n'existe pas dans le panier, créer un nouvel enregistrement
            $panier = new Panier();
            $panier->setProduitid($produit->getId());
            $panier->setQuantite($quantite);

            // Sauvegarder le panier dans la base de données
            $this->entityManager->persist($panier);
        }

        $this->entityManager->flush();

        return $this->json(['success' => true, 'message' => 'produit ajouté au panier!'], 200);
    }

    /**
     * @Route("/panier/delete/{id}", name="panier_delete", methods={"DELETE"})
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
