<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BlogController extends AbstractController
{
    // Un commentaire qui commence par un '@' est une annotation très importante, Symfony explique que lorsqu'on lancera www.monsite.com/blog, on fera appel à la méthode index()
    // Pas besoin de préciser templates/blog/index.html.twig, Symfony sait ou se trouve les fichiers template de rendu

    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
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
        

        // $repo = $this->getDoctrine()->getRepository(Article::class); // Va me chercher l'article repository de la classe Article

        $articles = $repo->findAll();

        dump($articles);

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
        // on envoi les article selectionner en bdd directement sur le navigateur dans le template index.html.twig
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

    
    /*
        On déclare une route permettant d'insérer un article '/blog/new'
        On déclare une route paramétrée '/blog/{id}/edit' permettant de modifier un article 

        Si nous envoyons un {id} dans l'URL, Symfony est capable d'aller sélectionner en BDD 
        les données de l'article, donc l'objet $article n'est plus NULL 
        Si nous n'envoyons pas d'{id} dans l'URL, à ce moment là l'objet $article est bien NULL
    */

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager)
    {
        // initalement méthode create()

        /*
            La classe Request est une classe prédéfinie en Symfony qui stockent toutes les données véhiculées par les superglobal
            ($_POST, $_GET, $_SERVER etc...) 
            La propriété 'request' représente la superglobale $_POST, les données saisies dans le formulaire sont accessible via 
            cette propriétés, ça renvoi des parameterBag (sac de parametres)
            Pour insérer un nouvel article, nous devons instancier la classe pour avoir un article vide, toutes les propriété private 
            ($title, $content, $image), ils faut donc les remplir, pour cela nous faisons appel au setter

            EntityManagerInterface est une méthode prédéfinies de Symfony qui permet de manipuler les lignes de la BDD
            (INSERT, UPDATE, DELETE)

            persist() est une méthode de la classe EntityManagerInterface qui permet de stocker et de préparer la requete SQL
            d'insertion
            flush() est une méthode de la classe EntityManagerInterface qui permet de libérer la requete d'insertion, c'est elle qui envoie 
            véritablement dans la BDD

            redirectionToRoute() méthode prédéfinies de Symfony qui permet de redirigés vers une route spécifique, 
            dans notre cas on redirige après insertion vers la route blog_show (avec le bon dernier id insérer) 
            afin de renvoyer vers le détail de l'article qui vien d'être inséré
        */


        dump($request);

        // if($request->request->count() > 0)
        // {
        //     $article = new Article;
        //     $article->setTitle($request->request->get('title'))
        //             ->setContent($request->request->get('content'))
        //             ->setImage($request->request->get('image'))
        //             ->setCreatedAt(new \DateTime());

        //     $manager->persist($article);
        //     $manager->flush();

        //     dump($article);

        //     return $this->redirectToRoute('blog_show', [
        //         'id' => $article->getId()
        //     ]);
        // }

        /*
            createFormBuilder() est une méthode prédéfinies de symfony qui permet de créer un formulaire à partir d'une entité, dans 
            notre cas de la classe Article, cela permet aussi de dire que le formulaire permettra de remplir l'objet issue de la classe
            Article $article

            add() est une méthode  qui permet de créer les différents champs du formulaire
            getForm() est une méthode qui permet de terminer et de valider le formulaire
            handleRequest() est une méthode qui permet de recuperer dans notre cas les information stockés dans $_POST et de remplir 
            notre objet $article, plus besoin d'appelé les setters de la classe Article
        */

        // Si l'objet $article n'est pas rempli, cela veut dire que nous n'avons pas envoyé d'{id} dans l'RUL, alors c'est une insertion, on crée un nouvel 
        if(!$article)
        {
            $article = new Article;
        }

        // On observe quand remplissant l'objet $article via les setters, les getteurs renvoient les données de l'article directement à l'interieur des champs du formulaire
        // $article->setTitle("Titre à la conv")
        //         ->setContent("Contenu de l'article à la convr");

        $form = $this->createFormBuilder($article)
                     ->add('title')
                     ->add('content')
                     ->add('image')
                     ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) // Si le formulaire est soumis et est valide 
        {
            // Si l'article ne possede pas d'{id} sa veut dire que ce nest pas une modif, alors on appel les setteur de la date de creation de l'article
            // Si c'est une modification, l'article possede deja un id, alors on ne modifie pasla date de creation de l'article
            if(!$article->getId())
            {
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article); // persiste récupère l'objet $article et prépare la requete d'insertion  
            $manager->flush(); // flush() libère réellement la requete SQL d'insertion

            // On redirige après insertion vers le détail de l'article qui vient d'être insérer
            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView()
        ]);


        // return $this->render('blog/create.html.twig');
    }


    // show() : méthode permettant d'afficher le détail d'un article
                   // 45
    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article) // 45
    {
        /*
            Pour selectionner un article dans la BDD, nous utilisons le principe de route paramétrées
            Dans la route, on définit un paramètre de type {id}
            Lorsque nous transmettons dans l'URL par exemple une route '/blog/9', donc on envoie un id connu en BDD dans l'URL.
            Symfony va automatiquement récupérée ce paramètre et le transmettre en argument de la méthode show()
            Cela veut dire que nous avons accès à l'{id} à l'intérieur de la méthode show()
            Le but est de selectionner les données en BDD de l'{id} récupéré en paramètre de la méthode show()
            Nous avons besoin pour cela de la classe ArticleRepository afin de pouvoir selectionner en BDD
            La méthode find() est issue de la clase ArticleRepository et permet de selectionner des données en BDD 
            à partir d'un paramètre de type {id} 
            getDcotrine() : l'ORM fait le travail pour nous, c'est à dire qu'ellle récupère la requete de selection pour l'executer en BDD
            Et Doctrine récupère le résultat de la requete de selection pour l'envoyer dans le controller

            $repo est un objet issu de la clase ArticleRepository, nous avons accès à toutes les méthodes déclarées dans cette classe
            (find, findAll, findBy, findOneBy etc...)
        */  

        // $repo = $this->getDoctrine()->getRepository(Article::class);

        // $article = $repo->find($id); // 4, on transmet en arugument de la méthode find(), le paramètre {id} récupéré dans l'URL 
        // find() : SELECT * FROM article WHERE id = .... + FETCH

        dump($article);

        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
        // On envoi dans le template show.html.twig, les données selectionnées en BDD, c'est à dire le detail d'un article
        // extract(['article' => $article]) => 'article' devient une variable TWIG dans le template show.html.twig

        /*
                    doctrine
            BDD <_____________________
            |                          |
            |                           CONTROLLER__________> libère les template + données BDD sur le navigateur
            |_________________________>|
                    doctrine

        */
    }
}

/*
    Injections de dépendances

    Dans Symfony nous avons un service container, tout ce qui est contenu dans Symfony est géré par Symfony
    Si nous observons la classe BlogController, nous ne l'avons jamais instancier, c'est Symfony lui-même qui se charge de
    l'instancier, donc il instancie des classes et appel ses fonctions

    Dans Symfony, ces objets utiles sont appelés 'services' et chaque service vit à l'intérieur d'un objet très spécial 
    appelé conteneur de service. Il vous facilite la vie, favorise une architecture solide et super rapide !!

    La fonction index() a pour rôle de nous afficher la liste des articles de la BDD et pour fonctionner, elle a donc besoin d'un repository (requete de selection),
    quand une fonction a besoin de quelque chose pour foctionnner, on appel ça une dépendance,
    la fonction dépend d'un repository pour aller chercher la liste des articles

    Donc si nous avons une dépendance, nous pouvons demander à Symfony de nous la fournir plutôt que de la fabriquer nous même

    La fonction index() ce n'est pas nous qui l'executons, c'est Symfony qui le fait pour nous

    Nous devons fournir à la méthode index() en argument, un objet issu de la classe ArticleRepository


*/
