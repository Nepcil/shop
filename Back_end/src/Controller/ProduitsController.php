<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Produits;
use App\Repository\CategoriesRepository;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProduitsController extends AbstractController
{
    /**
     * @Route("/products", name="get_all_products", methods="GET")
     */
    public function getAllProducts(ProduitsRepository $produitsRepository): JsonResponse
    {
        $products = $produitsRepository->findBy([], ['id' => 'DESC']);

        // Convertir les entités en tableau
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getNomduproduit(),
                'price' => $product->getPrix(),
                'description' => $product->getDescription(),
                'imageUrl' => $product->getImageUrl(),
                'categorieID' => $product->getCategorieid(),
            ];
        }
        return new JsonResponse($data);
    }

    /**
     * @Route("/products/{id}", methods="GET")
     */
    public function getProduct(Produits $produit): JsonResponse
    {
        $data = [
            'id' => $produit->getId(),
            'name' => $produit->getNomduproduit(),
            'price' => $produit->getPrix(),
            'description' => $produit->getDescription(),
            'imageUrl' => $produit->getImageUrl(),
            'categorieID' => $produit->getCategorieid(),
        ];

        return new JsonResponse($data);
    }

    /**
     * @Route("/products", methods="POST")
     */
    public function createNewProduct(Request $request, ProduitsRepository $produitsRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $newProduct = new Produits();
        $newProduct->setNomduproduit($data['name'] ?? null);
        $newProduct->setPrix($data['price'] ?? null);
        $newProduct->setDescription($data['description'] ?? null);
        $newProduct->setImageUrl($data['imageUrl'] ?? null);
        $newProduct->setCategorieid($data['categorieID'] ?? null);
    

        $produitsRepository->save($newProduct, true);

        return new JsonResponse($newProduct, 201);
    }

    /**
     * @Route("/products/{id}", methods="DELETE")
     */
    public function deleteProduct(Produits $produit, ProduitsRepository $produitsRepository): JsonResponse
    {
        $produitsRepository->remove($produit, true);

        return new JsonResponse(['Status' => 'Produit supprimé avec succès']);
    }

    /**
     * @Route("/products/{id}", methods="PUT")
     */
    public function alterProduct(Produits $produit, Request $request, ProduitsRepository $produitsRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $produit->setNomduproduit($data['name'] ?? $produit->getNomduproduit());
        $produit->setPrix($data['price'] ?? $produit->getPrix());
        $produit->setPrix($data['categorieID'] ?? $produit->getCategorieid());
        $produit->setPrix($data['description'] ?? $produit->getDescription());


        $produitsRepository->save($produit, true);

        return new JsonResponse(['status' => 'Produit modifié avec succès']);
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
            return new JsonResponse($data);
        }
            
    }

    /**
     * @Route("/sendCategories/{id}", name="send_categories", methods="GET")
     */
    public function CategoriesName(Categories $cate, CategoriesRepository $categs, ProduitsRepository $product): JsonResponse
    {
        
    $categoryId = $cate->getId();
    $category = $categs->find($categoryId);
    $categoryId = $category->getId();
    $categoryName = $category->getNomCategorie();
    $productsInCategory = $product->findBy(['categorieid' => $categoryId]);

    foreach ($productsInCategory as $prod) {
            $data[] = [
                'id' => $prod->getId(),
                'name' => $prod->getNomduproduit(),
                'price' => $prod->getPrix(),
                'description' => $prod->getDescription(),
                'imageUrl' => $prod->getImageUrl(),
                'categorie' => $categoryName,
                'categoryId' => $categoryId
            ];
        }
        
        if (!$productsInCategory) {
            return new JsonResponse(['error' => 'La catégorie spécifiée n\'existe pas'], 404);
        }else{
            return new JsonResponse($data);
        }
        
    }
    
    /**
     * @Route("/getUrl/{id}", methods="GET")
     */
    public function getUrl(Categories $catego, CategoriesRepository $categos, ProduitsRepository $product): JsonResponse
    {
        $categoryId = $catego->getId();
        $categoryName = $catego->getNomCategorie();
        $category = $categos->find($categoryId);
        $productsInCategory = $product->findBy(['categorieid' => $category]);

        function addUrlParam($params=[]){
        $p = array_merge($_GET, $params);
        $qs = http_build_query($p);
        return basename($_SERVER['PHP_SELF']).$qs;
        } 

        $url = addUrlParam(['' => $categoryName]);
    
        if($productsInCategory){
            return new JsonResponse($url);
        }
    
    }
}
