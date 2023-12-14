<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Avis;
use App\Entity\Produits;
use App\Entity\Users;
use App\Repository\AvisRepository;
use App\Repository\UsersRepository;
use DateTime;
use Psr\Log\LoggerInterface;

class AvisController extends AbstractController
{
    private $entityManager;
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
        )
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/produit-detail/{id}", name="avis_get", methods="GET")
     */
    public function getAvis(AvisRepository $avisRepository, Produits $produits, UsersRepository $usersRepository): JsonResponse
    {
        $produitId = $produits->getId();
        $avis = $avisRepository->findBy(['produitid' => $produitId]);

        if (!$avis) {
            return $this->json(['error' => 'Pas d\'avis trouvé !'], 404);
        }

        $data = [];
        foreach ($avis as $singleAvis) {
            $user = $usersRepository->find($singleAvis->getUserid());

            $data[] = [
                'id' => $singleAvis->getId(),
                'userid' => $singleAvis->getUserid(),
                'produitid' => $singleAvis->getProduitid(),
                'commentaire' => $singleAvis->getCommentaire(),
                'handup' => $singleAvis->getHandup(),
                'handless' => $singleAvis->getHandless(),
                'prenom' =>  $user->getPrenom(),
                'portrait' => $user->getPortrait(),
            ];
        }
            
        return $this->json($data);
    }

    /**
     * @Route("/produit-detail/{id}", name="create_avis", methods="POST")
     */
    public function createAvis(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        
        $data = json_decode($request->getContent(), true);
        
        
            $avis = new Avis();
            $avis->setCommentaire($data['commentaire'] ?? ''); 
            $avis->setUserid($data['userid'] ?? null); 
            $avis->setProduitid($data['produitid'] ?? null);  
            $avis->setDate(new DateTime());

            $entityManager->persist($avis);
            $entityManager->flush();

            return $this->json($avis);

    }

    /**
     * @Route("/produit-detail/{id}", name="new_record_action", methods="POST")
     */
    public function newRecordAction(Request $request, Avis $avis, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $produitId = $avis->getProduitid();
        $userId = $avis->getUserid();
        
        $avis = new Avis(); 
        $handup = $data['handup'] ?? null;
        $handless = $data['handless'] ?? null;


        // Vérifier si l'action est handup ou handless
        if ($handup == 1 || $handless == 1) {
            $avis->setHandup($handup);
            $avis->setHandless($handless);
        } else {
            return $this->json(['error' => 'Vous devez fournir soit handup soit handless ou aucun, mais pas les deux.'], 400);
        }

        $avis->setDate(new \DateTime());

        if (!$produitId) {
            return $this->json(['error' => 'Produit non trouvé'], 404);
        }

        if (!$userId) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        $avis->setProduitid($produitId);
        $avis->setUserid($userId);

        $entityManager->persist($avis);
        $entityManager->flush();

        return $this->json(['message' => 'Action enregistrée avec succès']);
    }


    /**
     * @Route("/produit-detail/{id}", name="record_action", methods="PUT")
     */
    public function recordAction(Request $request, Avis $avis, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
    
        $produitId = $data['produitid'];
        $userId = $data['userid'] ?? null;
        $handup = $data['handup'] ?? 0;
        $handless = $data['handless'] ?? 0;
    
        $userRepository = $entityManager->getRepository(Users::class);
        $user = $userRepository->findOneBy(['id' => $userId]);
    
        if (!$produitId || !$userId || !$user) {
            return $this->json(['error' => 'Produit ou utilisateur non trouvé'], 404);
        }
    
        // Mettre à jour les valeurs en fonction des actions
        if ($handup == 1 && $handless == 0) {
            $avis->setHandless(0);
            $avis->setHandup(1);
        } elseif ($handless == 1 && $handup == 0) {
            $avis->setHandless(1);
            $avis->setHandup(0);
        } else {
            return $this->json(['error' => 'Vous devez fournir soit handup soit handless ou aucun, mais pas les deux.'], 400);
        }
    
        $avis->setProduitid($produitId);
        $avis->setUserid($userId);
    
        $entityManager->persist($avis);
        $entityManager->flush();
    
        return $this->json(['message' => 'Action enregistrée avec succès']);
    }
    
}
