<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Session;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Security\UserRoles;
use Symfony\Component\Validator\Validator\ValidatorInterface; // Importer la classe ValidatorInterface
use Symfony\Component\Validator\Constraints as Assert; // Importer la classe Assert pour les contraintes de validation


class UsersController extends AbstractController
{
    private $userRepository;
    private $entityManager;
    private $passwordHasher;

    // Constructeur avec injection de dépendances
    public function __construct(UsersRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    //-----------------------------------------inscription----------------------------------

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, ValidatorInterface $validator): Response
    {
        // Récupération des données du formulaire d'inscription
        $data = json_decode($request->getContent(), true);

        // Vérification de la présence de toutes les informations nécessaires
        if (!isset($data['nom']) || !isset($data['prenom']) || !isset($data['password']) || !isset($data['birthdate']) || !isset($data['email'])) {
            return $this->json(['message' => 'Informations manquantes'], 400);
        }

        $nom = $data['nom'];
        $prenom = $data['prenom'];
        $password = $data['password'];
        $birthdate = $data['birthdate'];
        $email = $data['email'];
        $tel = isset($data['tel']) ? $data['tel'] : null;
        $adresse = isset($data['adresse']) ? $data['adresse'] : null;
        $languePreferee = isset($data['languePreferee']) ? $data['languePreferee'] : null;
        $role = isset($data['role']) ? $data['role'] : null;

        // Vérification de l'unicité de l'adresse e-mail
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);
        if ($existingUser) {
            return $this->json(['message' => 'Adresse e-mail déjà utilisée'], 409);
        }

        // Création d'un nouvel utilisateur
        $user = new Users();
        $user->setNom($nom);
        $user->setPrenom($prenom);

        // Ajout d'une contrainte de validation pour le mot de passe
        $violations = $validator->validate($password, [
            new Assert\Length([
                'min' => 8,
                'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
            ]),
        ]);

        // Vérification des violations de validation
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
            return $this->json(['success' => false, 'errors' => $errors], 400);
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setMotdepasse($hashedPassword);
        $user->setDatedenaissance(new \DateTime($birthdate));
        $user->setEmail($email);
        if ($tel) $user->setTel($tel);
        if ($adresse) $user->setAdresse($adresse);
        if ($languePreferee) $user->setLanguePreferee($languePreferee);

        // Attribution du rôle à l'utilisateur
        if ($role === 'admin') {
            $user->setRoles([UserRoles::ROLE_ADMIN]);
        } else {
            $user->setRoles([UserRoles::ROLE_USER]);
        }

        // Enregistrement de l'utilisateur dans la base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json(['success' => true, 'message' => 'Inscription réussie'], 200);
    }

    //-----------------------------------------connexion----------------------------------

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        // Récupération des données du formulaire de connexion
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];
        $rememberMe = isset($data['rememberMe']) ? $data['rememberMe'] : false;

        // Vérification de la présence de toutes les informations nécessaires
        if (empty($email) || empty($password)) {
            return $this->json(['message' => 'Informations manquantes'], 400);
        }

        // Vérification de l'existence de l'utilisateur et de la validité du mot de passe
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['success' => false, 'message' => 'Informations invalides'], 401);
        }

        // Vérification de l'existence d'un token actif pour cet utilisateur
        $activeToken = $this->entityManager->getRepository(Session::class)->findOneBy([
            'iduser' => $user->getId(),
            'statut' => true
        ]);

        if (!$activeToken) {
            // Génération d'un nouveau token
            $tokenPayload = [
                'sub' => $user->getId(),
                'role' => $user->getRoles(),
                'exp' => time() + 3600
            ];
            $jwtSecretKey = '4680'; // Remplacez par votre clé secrète
            $newToken = JWT::encode($tokenPayload, $jwtSecretKey, 'HS256');

            // Ajout du nouveau token à la table session
            $session = new Session();
            $session->setToken($newToken);
            $session->setIdUser($user->getId());
            $session->setStatut(true);
            $session->setDateDebut(new \DateTime());

            // Si l'utilisateur a coché "Se souvenir de moi", on met la date de fin à 30 jours, sinon à 1 heure
            if ($rememberMe) {
                $session->setDateFin(new \DateTime('+30 days'));
            } else {
                $session->setDateFin(new \DateTime('+1 hour'));
            }

            $this->entityManager->persist($session);
            $this->entityManager->flush();
        } else {
            $newToken = $activeToken->getToken();
        }

        return $this->json(['success' => true, 'role', 'message' => 'Connexion réussie', 'token' => $newToken, 'userId' => $user->getId()], 200);
    }

    //-----------------------------------------deconnexion----------------------------------

    /**
     * @Route("/logout", name="logout", methods={"POST"})
     */
    public function logout(Request $request): Response
    {
        // Récupération du token depuis l'en-tête de la requête
        $tokenstring = $request->headers->get('Authorization');
        $token = explode(' ', $tokenstring);
        $token = $token[1];

        // Vérification de l'existence d'un token actif pour cet utilisateur
        $activeToken = $this->entityManager->getRepository(Session::class)->findOneBy([
            'token' => $token,
            'statut' => true
        ]);

        if ($activeToken) {
            // Désactivation du token
            $activeToken->setStatut(false);
            $activeToken->setDateFin(new \DateTime());
            $this->entityManager->flush();
        }

        return $this->json(['success' => true, 'message' => 'Déconnexion réussie'], 200);
    }

    //-----------------------------------------contact----------------------------------

    /**
     * @Route("/contact", name="contact", methods={"POST"})
     */
    public function contact(Request $request): Response
    {
        // Récupération des données du formulaire de contact
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $nom = $data['nom'];
        $message = $data['message'];

        // Récupération du token depuis l'en-tête de la requête
        $authorizationHeader = $request->headers->get('Authorization');
        // Suppression du préfixe 'Bearer ' pour obtenir le token seul
        $token = str_replace('Bearer ', '', $authorizationHeader);

        // Vérification de l'existence et de l'activité du token
        $activeToken = $this->entityManager->getRepository(Session::class)->findOneBy([
            'token' => $token,
            'statut' => true
        ]);

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ($activeToken) {
            // Création d'un nouveau message de contact
            $newMessage = new Contact();
            $newMessage->setEmail($email);
            $newMessage->setNom($nom);
            $newMessage->setMessage($message);
            $newMessage->setDatemessage(new \DateTime());
            $newMessage->setUsersid($user->getId());

            // Enregistrement du message de contact dans la base de données
            $this->entityManager->persist($newMessage);
            $this->entityManager->flush();
        } else {
            // Gestion de l'erreur lorsque le token est invalide ou n'existe pas
            return $this->json(['success' => false, 'message' => 'Token invalide ou introuvable'], 400);
        }

        return $this->json(['success' => true, 'message' => 'Message envoyé!'], 200);
    }

    //-----------------------------------------fin User----------------------------------
}