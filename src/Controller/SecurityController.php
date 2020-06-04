<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        // UserPasswordEncoderInterface : classe qui permet d'encoder / hacher de mots de passes
        $user = new User;

        $form = $this->createForm(RegistrationType::class, $user); // Pourquoi le deuxieme argument est $user ? car Le RegistrationType
        // Va nous permettre de remplire $user
        // Va me chercher RegistrationType qui va me remplir $user

        dump($request);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            // On récupère le mot de passe du formulaire (non haché pour le moment) pour le transmettre à la méthode 
            // encodePassword() qui va se chargé d'encoder / crypter / hacher le mot de passe 
            // Pour pouvoir encoder le mot de passe il faut que notre entité User possède certaine classe (5 classes)

            $user->setPassword($hash); // on envoi le mot de passe haché dans le setteur de l'objet $user afin qu'il soit inséré
            // dans la BDD

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login'); // On redirige vers ma page de connexion après inscription
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /** 
     * @Route("/connexion", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response 
    {
        // L'importation de AuthenticationUtils et Response c'est pour envoyer un message d'erreur, si jamais
        // Il y a une erreur d'identification et envoi également la dernière valeur du _username (email)
        // Que l'utilisateur à tapper, comme sa il a pas à réécrire l'email

        // renvoi le message d'erreur en cas de mauvais identifiants au moment de la connexion
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier username (email) que l'internaute a saisie dans le formulaire de connexion
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
        // cette fonctionj ne retourne rien, il nous suffit d'avoir une route pour la deconnexion (voir security.yaml / firewalls)
    }

    /*
        security.yaml :

        providers : où se trouvent les données à contrôler
        fireWalls : quelles parties du site nous allons protéger et par quel moyen (formulaire de connexion)

        dans providers on dit il sont dans in_database es données à proteger et il sont dans l'entité et quel entité ?
        l'entité User ( qui est la database user en soi ) et quel donnée ? l'email ? 
        (pcq le mot de passse il le fait automatiquement déjà)

        dans firewalls on dit ok protege moi indatabase et c'est quoi (quel moyen) ? c'est un formulaire (par un formulaire) oui mais ou ?
        dans security_login d'accord mtn je vais checker ou ? bah au mem endroit 
    */
}
