<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ProductService
{
    private EntityManagerInterface $entityManager;

    // Le service nécessite l'EntityManager pour interagir avec la base de données.
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Crée un nouveau produit.
     *
     * @param string $name Le nom du produit.
     * @param float $price Le prix du produit.
     * @return Product Le produit créé.
     * @throws BadRequestException Si le nom ou le prix sont invalides.
     */
    public function createProduct(string $name, float $price): Product
    {
        // Validation basique des données
        if (empty($name)) {
            throw new BadRequestException('Le nom du produit est requis.');
        }
        if ($price <= 0) {
            throw new BadRequestException('Le prix du produit doit être un nombre positif.');
        }

        // Création du produit
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);

        // Persister et sauver dans la base de données
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    /**
     * Met à jour un produit existant.
     *
     * @param int $productId L'identifiant du produit à mettre à jour.
     * @param string $name Le nom du produit.
     * @param float $price Le prix du produit.
     * @return Product Le produit mis à jour.
     * @throws \Exception Si le produit n'existe pas.
     */
    public function updateProduct(int $productId, string $name, float $price): Product
    {
        // Récupérer le produit
        $product = $this->entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            throw new \Exception('Produit introuvable.');
        }

        // Mise à jour des données
        $product->setName($name);
        $product->setPrice($price);

        // Sauvegarder les modifications dans la base de données
        $this->entityManager->flush();

        return $product;
    }

    /**
     * Supprime un produit.
     *
     * @param int $productId L'identifiant du produit à supprimer.
     * @throws \Exception Si le produit n'existe pas.
     */
    public function deleteProduct(int $productId): void
    {
        // Récupérer le produit
        $product = $this->entityManager->getRepository(Product::class)->find($productId);

        if (!$product) {
            throw new \Exception('Produit introuvable.');
        }

        // Supprimer le produit
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    /**
     * Récupère tous les produits.
     *
     * @return Product[] Tableau des produits.
     */
    public function getAllProducts(): array
    {
        return $this->entityManager->getRepository(Product::class)->findAll();
    }

    /**
     * Récupère un produit par son ID.
     *
     * @param int $productId L'identifiant du produit.
     * @return Product|null Le produit ou null si non trouvé.
     */
    public function getProductById(int $productId): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->find($productId);
    }
}
