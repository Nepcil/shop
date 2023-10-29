<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class ContactController extends AbstractController
{
    private $contactRepository;
    private $entityManager;
    private $userRepository;
    private $mailer;

    public function __construct(
        ContactRepository $contactRepository, 
        UsersRepository $userRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ) {
        $this->contactRepository = $contactRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/contact", name="contact", methods={"POST"})
     */   
    public function sendEmailAndSave(Request $request, MailerInterface $mailer): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $nom = $data['nom'];
        $message = $data['message'];

        // Enregistrer d'abord les données en base de données
        $contact = new Contact();
        $contact->setEmail($email);
        $contact->setNom($nom);
        $contact->setMessage($message);

        $contact->setDatemessage(new \DateTime());            
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        // Ensuite, envoi d'e-mail
        $email = (new Email()) 
        ->from(new Address('arucharle@gmail.com', 'Shopinsid'))
        ->to(new Address('cilpen@hotmail.fr', $nom))
        ->subject('Réinitialiser votre mot de passe')
        ->text('Cliquez sur le lien ci-dessous pour modifier votre mot de passe.')
        ->html('<a href="' . $this->generateUrl('reset-password', [], UrlGeneratorInterface::ABSOLUTE_URL) . '" target="_blank">Réinitialiser le mot de passe</a>');

        $mailer->send($email);

        return $this->json(['success' => true, 'message' => 'Message envoyé'], 200);
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
}