<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    // Un commentaire qui commence par un '@' est une annotation très importante, Symfony explique que lorsqu'on lancera www.monsite.com/blog, on fera appel à la méthode index()
    // Pas besoin de préciser templates/blog/index.html.twig, Symfony sait ou se trouve les fichiers template de rendu

    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        /*
            Pour selectioner des données en BDD, nous avons besoin de la classe Repository de la classe Article
            Une classe Repository permet uniquement de selectionner des données en BDD (requete SQL SELECT)
            On a besoin de l'ORM DOCTRINE pour faire la relation entre la BDD et notre application (getDoctrine())
            getRepository() : méthode issue de l'objet DOCTRINE qui permet d'importer une classe Repository (SELECT)

            $repo est un objet issue de la classe ArticleRepository, cette classe contient des méthodes prédéfinies par SYMFONY permettant de 
            selectionner des données en BDD (find, findBy, findOneBy, findAll)

            dump() : équivalent de var_dump(), permet d'observer le resultat de
            la requete de selection en bas de la page dans la barre administrative (cible à droite)
        */ 

        $repo = $this->getDoctrine()->getRepository(Article::class); // Va me chercher l'article repository de la classe Article

        $articles = $repo->findAll();

        dump($articles);

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', [
            'title' => "Bienvenue sur le blog Symfony", 
            "age" => 25
        ]);
    }

    // show() : méthode permettant d'afficher le détail d'un article

    /**
     * @Route("/blog/45", name="blog_show")
     */
    public function show()
    {
        return $this->render('blog/show.html.twig');
    } 


    /**
     * @Route("/create", name="create")
     */
    public function create()
    {
        return $this->render('blog/create.html.twig');
    }

}
