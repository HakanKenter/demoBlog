<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create('fr_FR');
        // On utilise la bibliothèque FAKER qui permet d'envoyer des fausses données aléatoire
        // dans la BDD
        // On a demandé à composer d'installer cette librairie sur notre application

        // Création de 3 catégories
        for($i = 1; $i <= 3; $i++)
        {
            // Nous avons besoin d'un objet $category vide afin de renseigner de nouvelles catégories
            $category = new Category;
            // On appel les setteurs de l'entité Category
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());

            $manager->persist($category); // On garde en mémoire les objets $category

            // Création de 4 à 6 articles
            for($j = 1; $j <= mt_rand(4,6); $j++)
            {
                // Nous avons besoin d'un objet $article vide afin de créer et d'insérer de nouveaux articles en BDD
                $article = new Article();

                // On demande a FAKER de créer 5 paragraphes aléatoires pour nos nouveaux articles
                $content = '<p>' . join($faker->paragraphs(5), '<p></p>') .'</p>';

                // On renseigne tout les setteurs de la classe Article grace au méthodes de la librairie FAKER 
                // (phrase aléatoire (sentence), image aléatoire (imageUrl()) etc ...)
                $article->setTitle($faker->sentence())
                        ->setContent($content)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category); // On renseigne la clé étrangère qui permet
                        // de relier les articles au catégories

                $manager->persist($article);

                // création de 4 à 10 commentaires

                for($k = 1; $k <= mt_rand(4,10); $k++)
                {
                    $comment = new Comment;

                    $content = '<p>' . join($faker->paragraphs(5), '<p></p>') .'</p>';

                    $now = new \DateTime(); // objet dateTime avec l'heure det la date du jour
                    $interval = $now->diff($article->getCreatedAt()); // représente entre maintenant et la date de crétion de l'article (timestamp)
                    $days = $interval->days; // nombre de jour entre maintenant et la date de création de l'article
                    $minimum = '-' . $days . ' days'; // - 100 days entre la date de création de larticle et maintenant 

                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($minimum))
                            ->setArticle($article); // on relie (clé étrangère) nos commentaires aux articles
                    
                    $manager->persist($comment);
                            
                }
            }
        }

        $manager->flush();

        // for($i = 1; $i <= 10; $i++) // On boucle 10 fois afin de créer 10 articles
        // {
        //     $article = new Article; // On instancie la classe Article afin de renseigner les propriété private 

        //     // On renseigne tout les setteurs de la classe Article afin d'ajouter des titres, du contenu etcc qui seront insérés en BDD
        //     // La classe Article reflète la table SQL 'article'
        //     $article->setTitle("Titre de l'article n° $i")
        //             ->setContent("<p>Contenu de l'article n° $i</p>")
        //             ->setImage("https://picsum.photos/250")
        //             ->setCreatedAt(new \DateTime()); // objet classe DatTime() prédéfinies
            
        //     $manager->persist($article); // persist() est une méthode issue de la classe ObjectManager permettant de garder en mémoire
        //     // les objets Articles crées, il les fait persister dans le temps 
        // }

        // $manager->flush(); // flush() est une méthode issue de la classe ObjectManager qui permet véritablement de générer l'insertion en BDD
    }
}

// crtl + alt + i : Importe direct !
