<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/product/create', name: 'product_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_success');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager)
    {
        // Si le produit n'existe pas, on redirige vers une page d'erreur ou la liste des produits
        if (!$product) {
            throw $this->createNotFoundException('Le produit n\'existe pas');
        }

        // Créer le formulaire pour l'édition du produit
        $form = $this->createForm(ProductType::class, $product);

        // Traiter la requête HTTP et vérifier si le formulaire a été soumis et est valide
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les modifications dans la base de données
            $entityManager->flush();

            // Rediriger vers une page de succès ou de liste des produits
            return $this->redirectToRoute('product_success');
        }

        // Afficher le formulaire d'édition
        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Le produit n\'existe pas');
        }

        // Supprimer le produit
        $entityManager->remove($product);
        $entityManager->flush();  

        return $this->redirectToRoute('product_success');
    }

    #[Route('/product/success', name: 'product_success')]
    public function success(): Response
    {
        return new Response('Produit ajouté / supprimé avec succès.');
    }
}
