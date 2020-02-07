<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductsController extends AbstractController
{
    /**
     * @Route("/products", name="products")
     */
    public function index(ProductsRepository $repo)
    {
        $products = $repo->findAll();
        return $this->render('products/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/products/add", name="add_product")
     */
    public function productAdd(Request $request, EntityManagerInterface $manager)
    {   

        $product = new Products();

        $form = $this->createForm(ProductsType::class, $product);

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            // Si l'article à déjà un ID alors c'est un ID déjà présent en BDD
            if (!$product->getId()) {
                // make initial discounted price same as base price
                $product->setDiscountedPrice($product->getPrice());
            }

            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('products');
        }

        return $this->render('products/newProduct.html.twig', [
            'form' => $form->createView(),
            'editMode' => $product->getId() !== null
        ]);
    }
}
