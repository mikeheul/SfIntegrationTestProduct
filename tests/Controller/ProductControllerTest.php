<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

class ProductControllerTest extends WebTestCase
{
    public function testProductCreation()
    {
        // Test Fonctionnel : On simule l'action d'un utilisateur sur l'interface
        $client = static::createClient();
        
        // Charger la page du formulaire
        $crawler = $client->request('GET', '/product/create');
        
        // Assertion 1 : Vérifie que la requête a réussi (code 200)
        $this->assertResponseIsSuccessful(); // Test Fonctionnel

        // Sélectionner le formulaire et remplir les champs
        $form = $crawler->selectButton('Ajouter')->form([
            'product[name]' => 'Ordinateur Gaming',
            'product[price]' => 1500
        ]);

        // Soumettre le formulaire
        $client->submit($form);
        
        // Assertion 2 : Vérifie que le client a bien été redirigé vers la page de succès après soumission du formulaire
        $this->assertResponseRedirects('/product/success'); // Test Fonctionnel

        // Vérifier que le produit a été ajouté à la base de données
        // Test d'Intégration : Vérification de l'intégration avec la base de données
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $productRepository = $entityManager->getRepository(Product::class);
        $product = $productRepository->findOneBy(['name' => 'Ordinateur Gaming']);

        // Assertion 3 : Vérifie que le produit a bien été ajouté dans la base de données (il ne doit pas être nul)
        $this->assertNotNull($product); // Test d'Intégration
        
        // Assertion 4 : Vérifie que le prix du produit ajouté est correct (1500)
        $this->assertEquals(1500, $product->getPrice()); // Test d'Intégration

        // Assertion 5 : Vérifie que le nom du produit est bien "Ordinateur Gaming"
        $this->assertEquals('Ordinateur Gaming', $product->getName()); // Test d'Intégration
    }

    public function testProductUpdate()
    {
        // Test Fonctionnel : On simule l'action d'un utilisateur pour la mise à jour
        $client = static::createClient();
        
        // Création d'un produit
        $product = new Product();
        $product->setName('Ordinateur Gaming');
        $product->setPrice(1500);

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $entityManager->persist($product);
        $entityManager->flush();

        // Charger la page de modification du produit
        $crawler = $client->request('GET', '/product/edit/' . $product->getId());

        // Vérifier que la page se charge correctement
        $this->assertResponseIsSuccessful(); // Test Fonctionnel

        // Sélectionner le formulaire et remplir les champs de mise à jour
        $form = $crawler->selectButton('Update')->form([
            'product[name]' => 'Ordinateur Gaming Pro',
            'product[price]' => 1800,
        ]);

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifier la redirection après mise à jour
        $this->assertResponseRedirects('/product/success'); // Test Fonctionnel

        // Vérifier la mise à jour dans la base de données
        // Test d'Intégration : Vérification de la mise à jour dans la base de données
        $updatedProduct = $entityManager->getRepository(Product::class)->find($product->getId());
        $this->assertEquals('Ordinateur Gaming Pro', $updatedProduct->getName()); // Test d'Intégration
        $this->assertEquals(1800, $updatedProduct->getPrice()); // Test d'Intégration
    }

    public function testProductDeletion()
    {
        // Test Fonctionnel : On simule l'action de suppression d'un produit
        $client = static::createClient();
        
        // Créer un produit à supprimer
        $product = new Product();
        $product->setName('Ordinateur Gaming');
        $product->setPrice(1500);

        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $entityManager->persist($product);
        $entityManager->flush();

        // S'assurer que le produit a bien été créé
        $this->assertNotNull($entityManager->getRepository(Product::class)->find($product->getId())); // Test d'Intégration

        // Compter le nombre total de produits avant suppression
        $initialProductCount = $entityManager->getRepository(Product::class)->count([]); // Test d'Intégration

        // Accéder à la page de suppression du produit
        $client->request('GET', '/product/delete/' . $product->getId());

        // Vérifier que la redirection se produit
        $this->assertResponseRedirects('/product/success'); // Test Fonctionnel

        // Compter le nombre total de produits après suppression
        $finalProductCount = $entityManager->getRepository(Product::class)->count([]); // Test d'Intégration

        // Vérifier que le nombre de produits a diminué de 1 après suppression
        $this->assertEquals($initialProductCount - 1, $finalProductCount); // Test d'Intégration
    }

    // public function testProductCreationWithInvalidData()
    // {
    //     // Test Fonctionnel : On simule un envoi de formulaire avec des données invalides
    //     $client = static::createClient();

    //     // Accéder à la page de création du produit
    //     $crawler = $client->request('GET', '/product/create');

    //     // Vérifier que la page se charge correctement
    //     $this->assertResponseIsSuccessful(); // Test Fonctionnel

    //     // Sélectionner le formulaire et soumettre des données invalides
    //     $form = $crawler->selectButton('Ajouter')->form([
    //         'product[name]' => '', // Nom vide
    //         'product[price]' => -1500, // Prix négatif
    //     ]);

    //     // Soumettre le formulaire
    //     $client->submit($form);

    //     // Vérifier qu'il y a une erreur de validation sur le formulaire
    //     $this->assertSelectorTextContains('.form-error', 'Le nom ne peut pas être vide'); // Test Fonctionnel
    //     $this->assertSelectorTextContains('.form-error', 'Le prix doit être un nombre positif'); // Test Fonctionnel
    // }

    // public function testProductCreationAccessWithoutAuthentication()
    // {
    //     // Test Fonctionnel : On vérifie l'accès sans authentification
    //     $client = static::createClient();

    //     // Essayer d'accéder à la page de création sans être connecté
    //     $client->request('GET', '/product/create');

    //     // Vérifier qu'une redirection vers la page de connexion se produit
    //     $this->assertResponseRedirects('/login'); // Test Fonctionnel
    // }
}
