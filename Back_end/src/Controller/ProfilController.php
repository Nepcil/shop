<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Session;
use App\Repository\AvisRepository;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfilController extends AbstractController
{
    
    private $userRepository;
    private $entityManager;
    private $passwordHasher;

    public function __construct(
        UsersRepository $userRepository, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
        )
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route("/profil", name="profil_modifier", methods="PUT")
     */
    public function modifierInformations(Request $request): Response
    {
        // Récupérer les données envoyées depuis le front-end, on recupère le token depuis l'en-tête de la requête
        $data = json_decode($request->getContent(), true);
        $tokenstring = $request->headers->get('Authorization');
        $token = explode(' ', $tokenstring);
        //$token = $data['token'];

        // Vérifier si le token existe et est actif
        $activeToken = $this->entityManager->getRepository(Session::class)->findOneBy([
            'token' => $token[1],
            'statut' => true
        ]);

        if ($activeToken) {
            // L'utilisateur est bien authentifié
            $userId = $activeToken->getIdUser();
            $user = $this->userRepository->findOneBy(['id' => $userId]);
            // Mettre à jour les informations du profil avec les nouvelles données
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setPortrait($data['portrait']);
            $user->setEmail($data['email']); // Ajouter le champ email
            $user->setTel($data['tel']);
            $user->setAdresse($data['adresse']);
            // Vérifier si le nouveau mot de passe est différent de l'ancien mot de passe, si oui on change, si le mdp est vide ou identique on persist sans changer le mdp. 
            $newPassword = $data['motdepasse'];
            if (!$this->passwordHasher->isPasswordValid($user, $newPassword) && $newPassword != '') {

                // Assurer que le nouveau mot de passe est correctement haché avant d'être enregistré dans la base de données.
                $hashedNewPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setMotdepasse($hashedNewPassword);
            }
            // Enregistrer les modifications dans la base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            // Répondre avec un message de succès
            return new JsonResponse(['message' => 'Informations du profil mises à jour avec succès'], Response::HTTP_OK);
        } else {
            // L'utilisateur n'est pas authentifié
            return new JsonResponse(['message' => 'Token invalide ou introuvable'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/profil", name="profil_get", methods="GET")
     */
    public function getProfilData(Request $request): Response
    {
        // Récupérer le token depuis l'en-tête de la requête
        $authorizationHeader = $request->headers->get('Authorization');
        // Supprimer le préfixe 'Bearer ' pour obtenir le token seul
        $token = str_replace('Bearer ', '', $authorizationHeader);

        // Vérifier si le token existe et est actif
        $activeToken = $this->entityManager->getRepository(Session::class)->findOneBy([
            'token' => $token,
            'statut' => true
        ]);

        if ($activeToken) {
            $userId = $activeToken->getIdUser();
            $user = $this->userRepository->findOneBy(['id' => $userId]);

            // Vérifiez si l'utilisateur existe
            if (!$user) {
                return new JsonResponse(['message' => 'Utilisateur introuvable'], Response::HTTP_NOT_FOUND);
            }

            // Récupérez les données du profil que vous souhaitez renvoyer au frontend
            $profilData = [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'portrait' => $user->getPortrait(),
                'email' => $user->getEmail(), // Ajouter le champ email
                'tel' => $user->getTel(),
                'adresse' => $user->getAdresse(),
            ];

            return new JsonResponse($profilData, Response::HTTP_OK);
        } else {
            // L'utilisateur n'est pas authentifié
            return new JsonResponse(['message' => 'Token invalide ou introuvable'], Response::HTTP_BAD_REQUEST);
        }
    }

    //---------------------------------- Profil-public -------------------

    /**
     * @Route("/profil-public/user/{id}", name="public_profil_user", methods="GET")
     */
    public function publicProfilData(
        $id, 
        UsersRepository $userRepository, 
        AvisRepository $avisRepository, 
        ProduitsRepository $produitRepository
        ): JsonResponse
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        $avis = $avisRepository->findBy(['userid' => $id]);
        $avisList = $avisRepository->findBy(['userid' => $id]);
        $userFound = false;

        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur introuvable'], Response::HTTP_NOT_FOUND);
        }

        if (empty($avis)) {
            return new JsonResponse(['message' => 'Aucun avis trouvé pour cet utilisateur et ce produit'], Response::HTTP_NOT_FOUND);
        }

        foreach ($avisList as $avis) {
            if ($avis->getUserid() === $user->getId()) {
                $userFound = true;
                break;
            }
        }

        $profilData = [
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'portrait' => $user->getPortrait(),
        ];

        return new JsonResponse($profilData, Response::HTTP_OK);
    }
    

    /**
     * @Route("/profil-public/{id}", methods="GET")
     */
    public function getProduct(
        $id,
        ProduitsRepository $produitRepository
    ): JsonResponse
    {
        // Recherchez le produit dans le repository par son ID
        $produit = $produitRepository->find($id);

        // Vérifiez si le produit existe avant de tenter d'accéder à ses propriétés
        if (!$produit) {
            return new JsonResponse(['error' => 'Produit non trouvé'], 404);
        }

        // Créez un tableau contenant les données du produit
        $data = [
            'id' => $produit->getId(),
            'name' => $produit->getNomDuProduit(),
            'price' => $produit->getPrix(),
            'imageUrl' => $produit->getImageUrl()
        ];

        // Retournez les données sous forme de réponse JSON
        return new JsonResponse($data, 200);
    }


}