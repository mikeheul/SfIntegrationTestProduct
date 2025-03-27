<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityRepository;

class ProductServiceTest extends TestCase
{
    private $entityManager;
    private $productRepository;
    private $productService;

    protected function setUp(): void
    {
        // Mock de l'EntityManager
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Mock du repository associé à l'entité Product
        $this->productRepository = $this->createMock(EntityRepository::class);

        // Configurer le mock de l'EntityManager pour renvoyer le mock du repository
        $this->entityManager->method('getRepository')->willReturn($this->productRepository);

        // Création de l'instance de ProductService avec l'EntityManager mocké
        $this->productService = new ProductService($this->entityManager);
    }

    public function testCreateProduct()
    {
        // Configuration du mock de repository pour "persist"
        $product = new Product();
        $product->setName("Test Product");
        $product->setPrice(100);

        // Nous utilisons expect() pour simuler un appel à la méthode "persist" et "flush"
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($product));

        $this->entityManager->expects($this->once())
            ->method('flush');

        // Appel à la méthode du service
        $createdProduct = $this->productService->createProduct("Test Product", 100);

        // Vérification des valeurs
        $this->assertEquals("Test Product", $createdProduct->getName());
        $this->assertEquals(100, $createdProduct->getPrice());
    }

    // Autres tests unitaires pour update, delete, etc.
}
