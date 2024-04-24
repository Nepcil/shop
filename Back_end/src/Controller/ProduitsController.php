<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Produits;
use App\Repository\CategoriesRepository;
use App\Repository\ProduitsRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ProduitsController extends AbstractController
{

    public function __construct(){}

    //------------------------------------espace public-----------------------

    //------------------------------------gestion youtube---------------------

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
     * @Route("/", name="last_videos", methods={"GET"})
     */
    public function lastvideos(ProduitsRepository $product) 
    {
        $lastVid = $product->findBy([], ['date' => 'DESC'], 5);

        if (empty($lastVid)) {
            return new JsonResponse(['error' => 'Aucune vidéo trouvée'], 404);
        }

        $data = [];

        foreach ($lastVid as $video) {
            $youtubeUrl = $video->getVideo();
            $request = Request::create($youtubeUrl);
            $videoId = $request->query->get('v');
        
            $data[] = [
                'id' => $video->getId(),
                'name' => $video->getNomduProduit(),
                'video' => $videoId,
            ];
        }
        

        return new JsonResponse($data, 200);
    }

    //------------------------------------get products---------------------

    /**
     * @Route("/products", name="get_all_products", methods="GET")
     */
    public function getAllProducts(ProduitsRepository $produitsRepository, Produits $products): JsonResponse
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
                'imageUrl' => $product->getImageUrl(),
                'video' => $videoId,
                'imgage1' => $product->getImage1(),
                'imgage2' => $product->getImage2(),
                'imgage3' => $product->getImage3(),
                'imgage4' => $product->getImage4(),
                'imgage5' => $product->getImage5(),
                'categorieID' => $product->getCategorieid(),
            ];

        }
        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/products/{id}", methods="GET")
     */
    public function getProduct(
        Produits $product, 
        ProduitsRepository $produitsRepository, 
        ): JsonResponse
    {
        $youtubeUrl = $product->getVideo();
        $videoId = $this->getYoutubeVideoId($youtubeUrl);
        $productEntity = $produitsRepository->find($product);

        // Vérifiez si le produit existe avant de tenter d'accéder à ses propriétés
        if (!$productEntity) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }

        $data = [
            'id' => $productEntity->getId(),
            'name' => $productEntity->getNomduproduit(),
            'price' => $productEntity->getPrix(),
            'description' => $productEntity->getDescription(),
            'imageUrl' => $productEntity->getImageUrl(),
            'video' => $videoId,
            'image1' => $productEntity->getImage1(),
            'image2' => $productEntity->getImage2(),
            'image3' => $productEntity->getImage3(),
            'image4' => $productEntity->getImage4(),
            'image5' => $productEntity->getImage5(),
            'categorieID' => $productEntity->getCategorieid()
        ];

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/categories", methods="GET")
     */
    public function getCategories(CategoriesRepository $categories): JsonResponse
    {
        $category = $categories->findAll();

        foreach ($category as $categ) {
            $data[] = [
                'id' => $categ->getId(),
                'name' => $categ->getNomCategorie(),
            ];
        }
        
        if($data) 
        {
            return new JsonResponse($data, 200);
        }
            
    }

    /**
     * @Route("/sendCategories/{id}", name="send_categories", methods="GET")
     */
    public function CategoriesName(
        Categories $categories, 
        CategoriesRepository $categoriesRepository, 
        ProduitsRepository $produitsRepository
        ): JsonResponse
    {
        
    $categoryId = $categories->getId();
    $category = $categoriesRepository->find($categoryId);
    $categoryName = $category->getNomCategorie();
    $productsInCategory = $produitsRepository->findBy(['categorieid' => $categoryId]);
    $latestProducts = $produitsRepository->findBy([], ['date' => 'DESC'], 5);
    $data = [];
    

        if ($categoryName === 'Nouveautés') {
        
            foreach ($latestProducts as $prod) {
                $data[] = [
                    'id' => $prod->getId(),
                    'name' => $prod->getNomduproduit(),
                    'price' => $prod->getPrix(),
                    'description' => $prod->getDescription(),
                    'imageUrl' => $prod->getImageUrl(),
                    'categorie' => $categoryName,
                    'categorieid' => $categoryId,
                ];
            }
        } else {
            
            foreach ($productsInCategory as $prod) {
                $data[] = [
                    'id' => $prod->getId(),
                    'name' => $prod->getNomduproduit(),
                    'price' => $prod->getPrix(),
                    'description' => $prod->getDescription(),
                    'imageUrl' => $prod->getImageUrl(),
                    'categorie' => $categoryName,
                    'categorieid' => $categoryId
                ];
            }
        }
        
        if (empty($data)) {
            return new JsonResponse(['error' => 'La catégorie spécifiée n\'existe pas'], 404);
        }
        
        return new JsonResponse($data, 200);
        
    }

    
    //-------------------------------------espace vendeur--------------------------------
    

    /**
     * @Route("/vendeur", name="vendeur", name="vendeur", methods={"POST"})
     */
    public function createNewProduct(Request $request, ProduitsRepository $produitsRepository): JsonResponse
    {
        
        $data = json_decode($request->getContent(), true);

        $newProduct = new Produits();
        $newProduct->setNomduproduit($data['name'] ?? null);
        $newProduct->setPrix($data['price'] ?? null);
        $newProduct->setDescription($data['description'] ?? null);
        $newProduct->setVideo($data['video'] ?? null);
        $newProduct->setQuantity($data['quantity'] ?? null);
        $newProduct->setImageUrl($data['imageUrl'] ?? null);
        $newProduct->setImage1($data['image1'] ?? null);
        $newProduct->setImage2($data['image2'] ?? null);
        $newProduct->setImage3($data['image3'] ?? null);
        $newProduct->setImage4($data['image4'] ?? null);
        $newProduct->setImage5($data['image5'] ?? null);
        $newProduct->setCategorieid($data['categorieID'] ?? null);
        $newProduct->setDate(new DateTime());
    

        $produitsRepository->save($newProduct, true);

        return new JsonResponse($newProduct, 201);
    }

    /**
     * @Route("/vendeur-list", name="vendeur-list", name="", methods="GET")
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
                'quantity' => $product->getQuantity(),
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
     * @Route("/vendeur-modifier/{id}", name="vendeur_modifier", methods="PUT")
     */
    public function alterProduct(Produits $produit, Request $request, ProduitsRepository $produitsRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $produit->setNomduproduit($data['name'] ?? $produit->getNomduproduit());
        $produit->setPrix($data['price'] ?? $produit->getPrix());
        $produit->setDescription($data['description'] ?? $produit->getDescription());
        $produit->setImageUrl($data['imageUrl'] ?? $produit->getImageUrl());
        $produit->setQuantity($data['quantity'] ?? $produit->getQuantity());
        $produit->setVideo($data['video'] ?? $produit->getVideo());
        $produit->setImage1($data['image1'] ?? $produit->getImage1());
        $produit->setImage2($data['image2'] ?? $produit->getImage2());
        $produit->setImage3($data['image3'] ?? $produit->getImage3());
        $produit->setImage4($data['image4'] ?? $produit->getImage4());
        $produit->setImage5($data['image5'] ?? $produit->getImage5());
        $produit->setCategorieid($data['categorieID'] ?? $produit->getCategorieid());

        $produitsRepository->save($produit, true);

        return new JsonResponse(['status' => 'Produit modifié avec succès'], 200);
    }
    
    /**
     * @Route("/vendeur-delete/{id}", name="vendeur_delete", methods="DELETE")
     */
    public function deleteProduct(Produits $produit, ProduitsRepository $produitsRepository): JsonResponse
    {
        $produitsRepository->remove($produit, true);

        return new JsonResponse(['Status' => 'Produit supprimé avec succès'], 200);
    }

}
