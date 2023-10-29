<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VendeurController extends AbstractController
{
    private $produitsRepository;
    private $entityManager;

    public function __construct(
        ProduitsRepository $produitsRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->produitsRepository = $produitsRepository;
        $this->entityManager = $entityManager;
    }

    private function getYoutubeVideoId(string $youtubeUrl): ?string
    {
        $urlComponents = parse_url($youtubeUrl);

        if ($urlComponents && isset($urlComponents['query'])) {
            parse_str($urlComponents['query'], $queryParameters);
            return $queryParameters['v'] ?? null;
        }

        return null;
    }

    /**
     * @Route("/vendeur", name="vendeur", methods={"POST"})
     */
    public function vendeurCreate(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        // Vérification de la présence de toutes les informations nécessaires
        if (
            !isset($data['NomDuProduit']) || 
            !isset($data['Description']) || 
            !isset($data['Prix']) || 
            !isset($data['imageUrl']) || 
            !isset($data['categorieId']) || 
            !isset($data['quantity']) 
        ) {
            return $this->json(['message' => 'Informations manquantes'], 400);
        }

        $nomProduit = $data['NomDuProduit'];
        $description = $data['Description'];
        $categorie = $data['categorieId'];
        $imageUrl = $data['imageUrl'];
        $quantity = $data['quantity'];
        $price = $data['Prix'];
        $video = isset($data['Video']) ? $data['Video'] : null;
        $image1 = isset($data['image1']) ? $data['image1'] : null;
        $image2 = isset($data['image2']) ? $data['image2'] : null;
        $image3 = isset($data['Image3']) ? $data['Image3'] : null;
        $image4 = isset($data['Image4']) ? $data['Image4'] : null;
        $image5 = isset($data['Image5']) ? $data['Image5'] : null; 

        // Création d'un nouveau produit
        $produit = new Produits();
        $produit->setNomduproduit($nomProduit);
        $produit->setDescription($description);
        $produit->setCategorieid($categorie);
        $produit->setImageurl($imageUrl);
        $produit->setQuantity($quantity);
        $produit->setPrix($price);
        $produit->setVideo($video);
        $produit->setImage1($image1);
        $produit->setImage2($image2);
        $produit->setImage3($image3);
        $produit->setImage4($image4);
        $produit->setImage5($image5);

        // Enregistrement de l'utilisateur dans la base de données
        $this->entityManager->persist($produit);
        $this->entityManager->flush();

        return $this->json(['success' => true, 'message' => 'L\'ajout du produit a bien réussi'], 200);
    }

    /**
     * @Route("/vendeur-list", name="vendeur-list", methods="GET")
     */
    public function getProductBuyer(ProduitsRepository $produitsRepository): JsonResponse
    {
        $products = $produitsRepository->findBy([], ['id' => 'DESC']);
        $data = [];

        foreach ($products as $product) {

            $youtubeUrl = $product->getVideo();
            $videoId = $this->getYoutubeVideoId($youtubeUrl);
            
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getNomduproduit(),
                'price' => $product->getPrix(),
                'description' => $product->getDescription(),
                'imageUrl' => $product->getImageurl(), // Corrected property name
                'video' => $videoId,
                'image1' => $product->getImage1(),
                'image2' => $product->getImage2(),
                'image3' => $product->getImage3(),
                'image4' => $product->getImage4(),
                'image5' => $product->getImage5(),
                'categorieId' => $product->getCategorieid()
            ];
        }
        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/vendeur-modifier/{id}", name="vendeur-modifier", methods="PUT")
     */
    public function productModifier(Request $request, Produits $produit): Response
    {
        $data = json_decode($request->getContent(), true);

        $produit->setNomduproduit($data['name'] ?? null);
        $produit->setDescription($data['description'] ?? null);
        $produit->setCategorieid($data['categorieId'] ?? null);
        $produit->setImageurl($data['imageUrl'] ?? null);
        $produit->setQuantity($data['quantity'] ?? null);
        $produit->setPrix($data['price'] ?? null);
        $produit->setVideo($data['video'] ?? null);
        $produit->setImage1($data['image1'] ?? null);
        $produit->setImage2($data['image2'] ?? null);
        $produit->setImage3($data['image3'] ?? null);
        $produit->setImage4($data['image4'] ?? null);
        $produit->setImage5($data['image5'] ?? null);
        $produit->setDate(new DateTime()); 

        $this->entityManager->persist($produit);
        $this->entityManager->flush();

        return $this->json(['success' => true, 'message' => 'Votre produit est bien modifié'], 200);
    }

    /**
     * @Route("/vendeur-delete/{id}", name="vendeur-delete", methods="DELETE")
     */
    public function ProductDelete(Produits $produit, ProduitsRepository $produitsRepository): JsonResponse
    {
        $this->entityManager->remove($produit);
        $this->entityManager->flush();

        return $this->json(['success' => true, 'message' => 'Votre produit a bien été supprimé'], 200);
    }
}
