<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Entity\Panier;
use App\Repository\PanierRepository;
use App\Entity\Produits;
use App\Repository\UsersRepository;
use App\Repository\ProduitsRepository;
use App\Repository\CommandesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;

class PanierController extends AbstractController
{
    private $panierRepository;
    private $entityManager;
    private $commandesRepository;

    public function __construct(
        PanierRepository $panierRepository, 
        EntityManagerInterface $entityManager,
        CommandesRepository $commandesRepository,
    )
    {
        $this->panierRepository = $panierRepository;
        $this->entityManager = $entityManager;
        $this->commandesRepository = $commandesRepository;
    }

    /**
     * @Route("/panier", name="panier_list", methods="GET")
     */
    public function getAllPanierItems(Request $request, PanierRepository $panierRepository, ProduitsRepository $produitsRepository): JsonResponse
    {
        $userId = $request->query->get('userId'); 
        $items = $panierRepository->findBy(['userid' => $userId]);

        if (!$userId || !$items) {
            return $this->json(['error' => 'ID utilisateur non spécifié ou panier vide'], Response::HTTP_BAD_REQUEST);
        }

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
     * @Route("/panier/{userid}", name="panier_num", methods="GET")
     */
    public function getQuantity(int $userid, PanierRepository $panierRepository): JsonResponse
    {
        
        $userId = $panierRepository->findOneBy(['userid' => $userid]); 
    
        if (!$userId) {
            return new JsonResponse(['error' => 'ID utilisateur non spécifié'], Response::HTTP_BAD_REQUEST);
        }

        $items = $panierRepository->findAll();
        $totalQuantity = 0;

        foreach ($items as $item) {
            $totalQuantity += $item->getQuantite();
        }

        return new JsonResponse(['totalQuantity' => $totalQuantity]);
        
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
            $price = $data['price'];

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
                $panier->setPrice($price);
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
     * @Route("/panier/modifier", name="panier_update", methods="PUT")
     */
    public function updatePanier(
        Request $request,
        PanierRepository $panierRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
    
        if ($data === null) {
            return $this->json(['error' => 'Données JSON non valides'], 400);
        }
    
        if (!isset($data['userid'], $data['produits'])) {
            return $this->json(['error' => 'Données JSON incomplètes'], 400);
        }
    
        $userId = $data['userid'];
        $produits = $data['produits'];
    
        foreach ($produits as $produit) {
            $productId = $produit['produitid'];
            $quantite = $produit['quantite'];
            $price = $produit['price'];
    
            $panier = $panierRepository->findOneBy(['produitid' => $productId, 'userid' => $userId]);
    
            if (!$panier) {
                return new JsonResponse(['error' => 'Panier non trouvé pour ce produit et cet utilisateur'], 400);
            }
    
            $panier->setQuantite($quantite);
            $panier->setPrice($price);
            $panier->setDate(new \DateTime()); 
    
            $this->entityManager->persist($panier);
        }
        
        $this->entityManager->flush();
    
        return new JsonResponse(['success' => true, 'message' => 'Vos produits ont bien été modifiés dans le panier'], 200);
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
        
        $this->entityManager->remove($panier);
        $this->entityManager->flush();

        return $this->json(['success' => true, 'message' => 'produit supprimé du panier!'], 200);
    }

    //-----------------------command---------------

    /**
     * @Route("/command", name="command", methods="POST")
     */
    public function Command(Request $request): Response
    {
    
        $data = json_decode($request->getContent(), true);

        if (!isset($data['userid'], $data['produitid'], $data['quantite'], $data['price'])) {
            return $this->json(['error' => 'Données JSON incomplètes'], 400);
        }

        $userId = $data['userid'];
        $produitId = $data['produitid'];
        $quantite = $data['quantite'];
        $price = $data['price'];

        $commande = $this->entityManager->getRepository(Commandes::class)->findOneBy(['userid' => $userId, 'produitid' => $produitId]);

        if ($commande) {
            
            $commande->setQuantite($quantite);
            $commande->setTotalPrice($price);
        } else {
            
            $commande = new Commandes();
            $commande->setUserid($userId);
            $commande->setProduitid($produitId);
            $commande->setQuantite($quantite);
            $commande->setPrice($price);
            $commande->setDate(new DateTime());

            $this->entityManager->persist($commande);
        }

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Commande ajoutée avec succès'], Response::HTTP_CREATED);

    }


    /**
     * @Route("/getCommand", name="getCommand", methods="GET")
     */
    public function getCommand(): Response
    {
        $commandes = $this->commandesRepository->findAll();
        $data = [];

        foreach ($commandes as $commande) {
            $data[] = [
                'id' => $commande->getId(),
                'produitid' => $commande->getProduitid(),
                'userid' => $commande->getUserid(),
                'quantite' => $commande->getQuantite(),
                'totalPrice'=> $commande->getPrice()
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/command/modifier", name="updateCommand", methods="PUT")
     */
    public function updateCommand(Request $request, CommandesRepository $commandesRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json(['error' => 'Données JSON non valides'], 400);
        }
        
        if (!isset($data['userid'], $data['produits'])) {
            return $this->json(['error' => 'Données JSON incomplètes'], 400);
        }
        
        $userId = $data['userid'];
        $produits = $data['produits'];
        
        foreach ($produits as $produit) {
            $productId = $produit['produitid'];
            $price = $produit['price']; 
            $quantite = $produit['quantite'];
            
            $commande = $commandesRepository->findOneBy(['produitid' => $productId, 'userid' => $userId]);
            
            if (!$commande) {
                
                $commande = new Commandes();
                $commande->setProduitid($productId);
                $commande->setUserid($userId);
            }
            
            $commande->setPrice($price);
            $commande->setQuantite($quantite);
            $commande->setDate(new DateTime()); 
            
            $commandesRepository->save($commande, true);
        }
        
        return new JsonResponse(['status' => 'Commande modifiée avec succès'], 200);
    }

    /**
     * @Route("/command/{id}", name="command_delete", methods="DELETE")
     */
    public function deleteCommand(int $id): Response
    {
        $commande = $this->commandesRepository->findOneBy(['produitid' => $id]);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        if ($commande) {
            $this->entityManager->remove($commande);
            $this->entityManager->flush();

            return $this->json(['message' => 'Commande annulée avec succès']);
        } else {
            throw $this->createNotFoundException('Commande non trouvée');
        }
    }



}
