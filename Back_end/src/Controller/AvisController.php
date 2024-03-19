<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Avis;
use App\Entity\AvisLike;
use App\Entity\Produits;
use App\Entity\Users;
use App\Repository\AvisLikeRepository;
use App\Repository\AvisRepository;
use App\Repository\UsersRepository;
use DateTime;
use Psr\Log\LoggerInterface;

class AvisController extends AbstractController
{

    public function __construct(
        )
    {}

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
            $currentUser = $usersRepository->find($singleAvis->getUserid());

            $data[] = [
                'id' => $singleAvis->getId(),
                'userid' => $singleAvis->getUserid(),
                'produitid' => $singleAvis->getProduitid(),
                'commentaire' => $singleAvis->getCommentaire(),
                'prenom' =>  $currentUser->getPrenom(),
                'portrait' => $currentUser->getPortrait()
            ];

        }

        return $this->json($data);
    }

    /**
     * @Route("/produit-detail/{id}/like", name="avis_like", methods={"GET"})
     */
    public function getLike(Produits $produits, AvisLikeRepository $likeRepository): JsonResponse
    {
        $produitId = $produits->getId();
        $likes = $likeRepository->findBy(['produitid' => $produitId]);

        if (!$likes) {
            return $this->json(['error' => 'Pas de like trouvé !'], 404);
        }

        $data = [];
        foreach ($likes as $like) {
            $data[] = [
                'id' => $like->getId(),
                'userid' => $like->getUserid(),
                'produitid' => $produitId,
                'handup' =>  $like->getHandup(),
                'handless' => $like->gethandless()
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
            $avis->setUserid($data['userid'] ?? null); 
            $avis->setProduitid($data['produitid'] ?? null);
            $avis->setCommentaire($data['commentaire'] ?? null);
            $avis->setDate(new DateTime());

            $entityManager->persist($avis);
            $entityManager->flush();

            return $this->json($avis, 201);

    }

    /**
     * @Route("/produit-detail/{id}/like", name="create_avis", methods="POST")
     */
    public function createLike(Request $request, AvisLikeRepository $likeRepository, Produits $produit, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $data['userid'];

        if (!$userId) {
            return $this->json(['error' => 'ID utilisateur non fourni'], 400);
        }

        // Vérifier si le like existe déjà
        $likeAvis = $likeRepository->findOneBy(['produitid' => $produit->getId(), 'userid' => $userId]);

        if ($likeAvis) {
            // Mettre à jour le like existant
            $likeAvis->setHandup($data['handup'] ?? $likeAvis->getHandup());
            $likeAvis->setHandless($data['handless'] ?? $likeAvis->getHandless());
            $likeAvis->setDate(new \DateTime());

            $entityManager->persist($likeAvis);
            $entityManager->flush();

            return $this->json(['message' => 'Vote mis à jour avec succès']);
        }

        // Créer un nouveau like
        $like = new AvisLike();

        $handup = $data['handup'] ?? 0;
        $handless = $data['handless'] ?? 0;

        // Vérifier si l'action est handup ou handless
        if ($handup == 1 && $handless == 0) {
            $like->setHandup(1);
        } elseif ($handless == 1 && $handup == 0) {
            $like->setHandless(1);
        } else {
            return $this->json(['error' => 'Vous devez fournir soit handup soit handless ou aucun'], 400);
        }

        $like->setDate(new \DateTime());
        $like->setProduitid($produit->getId());
        $like->setUserid($userId);

        $entityManager->persist($like);
        $entityManager->flush();

        return $this->json(['message' => 'Vote enregistré avec succès']);
    }


    /**
     * @Route("/produit-detail/{id}", name="record_action", methods="PUT")
     */
    public function recordAction(
        Request $request, 
        AvisLikeRepository $likeRepository, 
        EntityManagerInterface $entityManager
        ): Response
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json(['error' => 'Données JSON non valides'], 400);
        }

        if (!isset($data['produitid'], $data['userid'], $data['handup'], $data['handless'])) {
            return $this->json(['error' => 'Données JSON incomplètes'], 400);
        }

        $produitId = $data['produitid'];
        $userId = $data['userid'];
        $handup = $data['handup'] ?? 0;
        $handless = $data['handless'] ?? 0;

        $userRepository = $entityManager->getRepository(Users::class);
        $user = $userRepository->find($userId);

        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        if (!in_array($handup, [0, 1]) || !in_array($handless, [0, 1])) {
            return $this->json(['error' => 'Vous devez fournir soit handup soit handless ou aucun, mais pas les deux.'], 400);
        }

        $existingLike = $likeRepository->findOneBy(['userid' => $user, 'produitid' => $produitId]);

        if ($existingLike) {
            
            $existingLike->setHandup($handup);
            $existingLike->setHandless($handless);
        }

        $entityManager->flush();

        return $this->json(['message' => 'Action enregistrée avec succès']);

    }
    
}
