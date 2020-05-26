<?php

namespace App\DataFixtures;

use App\Entity\Article;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++) // On boucle 10 fois afin de créer 10 articles
        {
            $article = new Article; // On instancie la classe Article afin de renseigner les propriété private 

            // On renseigne tout les setteurs de la classe Article afin d'ajouter des titres, du contenu etcc qui seront insérés en BDD
            // La classe Article reflète la table SQL 'article'
            $article->setTitle("Titre de l'article n° $i")
                    ->setContent("<p>Contenu de l'article n° $i</p>")
                    ->setImage("https://picsum.photos/250")
                    ->setCreatedAt(new DateTime());
            
            $manager->persist($article); // persist() est une méthode issue de la classe ObjectManager permettant de garder en mémoire
            // les objets Articles crées, il les fait persister dans le temps 
        }

        $manager->flush(); // flush() est une méthode issue de la classe ObjectManager qui permet véritablement de générer l'insertion en BDD
    }
}

// crtl + alt + i : Importe direct !
