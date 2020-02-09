<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\DiscountRules;
use Doctrine\ORM\EntityRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DiscountRulesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RulesController extends AbstractController
{
    /**
     * @Route("/rules", name="rules")
     */
    public function index(DiscountRulesRepository $ruleRepo)
    {
        $rules = $ruleRepo->findAll();
        
        return $this->render('rules/index.html.twig', [
            'rules' => $rules,
        ]);
    }

    /**
     * @Route("/rules/add", name="add_rules")
     */
    public function rulesAdd(Request $request, EntityManagerInterface $manager)
    {   
        
        $rule = new DiscountRules();

        $defaultData = [];
        $form = $this->createFormBuilder($defaultData)
            ->add('startDate', DateType::class, [
                'required'   => false,
                'format' => 'yyyy-MM-dd',
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                ]
            ])
            ->add('endDate', DateType::class, [
                'required'   => false,
                'format' => 'yyyy-MM-dd',
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                ]
            ])
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
                    'Higher than or equal' => ">=",
                    'Lower than' => "<",
                    'Lower than or equal' => "<=",
                    'Equal to' => "=",
                ],
            ])
            ->add('price', NumberType::class, [
                'invalid_message' => "Price must be a valid number (like 100 or 100.56)",
                'scale' => 2
            ])
            ->add('discountAmount', IntegerType::class, [
                'invalid_message' => "Discount amount must be a valid number between 1 and 50",
                'attr' => [
                    'min' => 1,
                    'max' => 50,
                    'step' => 1
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            
            $data = $form->getData();
            
            $expression = "";

            if ($data["price"]) {
                $expression .= "product.price {$data["compare"]} {$data['price']}";
            }

            if ($data["startDate"]) {
                $expression .= " and date('now') >= date('{$data['startDate']->format('Y-m-d')}')";
            }

            if ($data["endDate"]) {
                $expression .= " and date('now') <= date('{$data['endDate']->format('Y-m-d')}')";
            }

            if ($expression) {
                $expression = "and {$expression}";
            }

            $discountAmount = $data["discountAmount"] / 100;
            $type = strToLower(self::normalize($data['type']->getType()));

            $expression = "product.type === '$type' {$expression} ? $discountAmount : 0";
            
            $rule->setRuleExpression($expression);
            $rule->setDiscountPercent($data['discountAmount']);

            $manager->persist($rule);
            $manager->flush();

            return $this->redirectToRoute('rules');
        }

        return $this->render('rules/newRules.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function normalize ($string) {
        $table = array(
            'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj', 'd'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'C'=>'C', 'c'=>'c', 'C'=>'C', 'c'=>'c',
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
            'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
            'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
            'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
            'ÿ'=>'y', 'R'=>'R', 'r'=>'r', ' ' => '_',
        );
       
        return strtr($string, $table);
    }
}
