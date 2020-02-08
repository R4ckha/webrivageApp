<?php

namespace App\Controller;

use App\Entity\Products;
use Doctrine\ORM\EntityRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RulesController extends AbstractController
{
    /**
     * @Route("/rules", name="rules")
     */
    public function index()
    {
        return $this->render('rules/index.html.twig', [
            'controller_name' => 'RulesController',
        ]);
    }

    /**
     * @Route("/rules/add", name="add_rules")
     */
    public function rulesAdd(ProductsRepository $repo, Request $request, EntityManagerInterface $manager)
    {   

        //$products = $repo->findAllType();
        //$product = new Products();

        //dd($product->getType());
        
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('startDate', TextType::class)
            ->add('endDate', TextType::class)
            ->add('type', EntityType::class, [
                'class' => Products::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->groupBy('p.type');
                },
                'choice_label' => 'type',
            ])
            ->add('compare', ChoiceType::class, [
                'choices'  => [
                    'Higher than' => ">",
                    'Lower than' => "<",
                    'Equal to' => "=",
                ],
            ])
            ->add('price', TextType::class)
            ->add('discountAmount', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            // Si l'article à déjà un ID alors c'est un ID déjà présent en BDD
            if (!$product->getId()) {
                // make initial discounted price same as base price
                // discounted_price can be null, abort mission
                // $product->setDiscountedPrice($product->getPrice());
            }

            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('products');
        }

        return $this->render('rules/newRules.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
