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
use App\Repository\AvisRepository;
use App\Repository\AvisRepositoryAvisRepository;
use DateTime;

class AvisController extends AbstractController
{
    /**
     * @Route("/produit-detail/{id}", methods={"GET"})
     */
    public function getAvisForProduct(AvisRepository $avisRepository): JsonResponse
    {
        $avis = $avisRepository->findAll();

        if (!$avis) {
            return $this->json(['error' => 'Avis not found'], 404);
        }

        $data = [];
        foreach ($avis as $singleAvis) {
            $data[] = [
                'id' => $singleAvis->getId(),
                'userid' => $singleAvis->getUserid(),
                'produitid' => $singleAvis->getProduitid(),
                'note' => $singleAvis->getNote(),
                'commentaire' => $singleAvis->getCommentaire(),
                'handup' => $singleAvis->getHandup(),
                'handless' => $singleAvis->getHandless(),
            ];
        }
        
        return $this->json($data);
    }


    /**
     * @Route("/produit-detail/{id}/avis-create", methods={"POST"})
     */
    public function createAvis(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        
            $data = json_decode($request->getContent(), true);

            if (
                !isset($data['commentaire'], $data['userid'], $data['produitid'])
            ) {
                return $this->json(['error' => 'Invalid JSON format'], 400);
            }

            $avis = new Avis();
            $avis->setCommentaire($data['commentaire']);
            $avis->setUserid($data['userid']);
            $avis->setProduitid($data['produitid']);
            $avis->setNote($data['note']);
            $avis->setHandup($data['handup']);
            $avis->setHandless($data['handless']);
            $avis->setDate(new DateTime()); 

            $entityManager->persist($avis);
            $entityManager->flush();

            $responseData = [
                'id' => $avis->getId(),
                'userid' => $avis->getUserid(),
                'produitid' => $avis->getProduitid(),
                'commentaire' => $avis->getCommentaire(),
                'note' => $avis->getNote(),
                'handup' => $avis->getHandup(),
                'handless' => $avis->getHandless(),
            ];

            return $this->json($responseData);
            
    }

    /**
     * @Route("/produit-detail/{id}/record-action", name="avis_record_action", methods={"POST"})
     */
    public function recordAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $avis = $data['handup'];
        $avis = $entityManager->getRepository(Avis::class)->find($avis);

        $entityManager->persist($avis);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }

    /**
     * @Route("/produit-detail/{id}/avis-note", name="avis_note", methods={"POST"})
     */
    public function saveNote(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $productId = $data['productId'];
        $note = $data['note'];

        $avis = new Avis();
        $avis->setProduitid($productId);
        $avis->setNote($note);

        $entityManager->persist($avis);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Note enregistrée avec succès'], JsonResponse::HTTP_OK);
    }

}







