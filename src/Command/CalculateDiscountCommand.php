<?php

namespace App\Command;

use App\Command\Engine;
use App\Entity\Products;
use App\Command\Language;
use Symfony\Component\Mailer\Mailer;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DiscountRulesRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CalculateDiscountCommand extends Command
{
    protected static $defaultName = 'calculate-discount';

    private $language;
    private $engine;

    private $manager;
    private $productRepo;
    private $ruleRepo;
    private $expressions;

    public function __construct(ProductsRepository $productRepo, DiscountRulesRepository $discountRepo, EntityManagerInterface $manager)
    {
        $this->language = new Language();
        $this->engine = new Engine($this->language);
        
        $this->manager = $manager;
        $this->productRepo = $productRepo;
        $this->ruleRepo = $discountRepo;
        $this->expressions = new ExpressionLanguage();
        parent::__construct();
    }
    
    protected function configure()
    {
        $this
            ->setDescription('command calculate the product\'s discounted price')
            //->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        
        $productRepo = $this->productRepo->findAll();
        $ruleRepo = $this->ruleRepo->findAll();

        $engine = $this->engine;
        $manager = $this->manager;

        foreach ($ruleRepo as $discountRule) {
            //dump($discountRule);
            $engine->addDiscountRule($discountRule->getRuleExpression());
        }

        // Array to send every morning
        $emailArray = [];
        foreach ($productRepo as $product) {
            $discountPrice = number_format($engine->calculatePrice($product), 2, '.', '');
            if ($product->getPrice() != $discountPrice && $product->getDiscountedPrice() != $discountPrice) {
                $product->setDiscountedPrice($discountPrice);
                $manager->persist($product);
                $manager->flush();
                array_push($emailArray, [
                    "id"=>$product->getId(),
                    "name"=>$product->getName(),
                    "price"=>$product->getPrice(),
                    "discounted_price"=>$product->getdiscountedPrice(),
                    "type"=>$product->getType(),
                ]);
            }
        }
        if (!empty($emailArray)) {
            $message = (new \Swift_Message('Hello Email'))
            ->setFrom('gratien.therond@gmail.com')
            ->setTo('gratien.therond@gmail.com')
            ->setBody(
                $this->renderView('emails/updateDiscount.html.twig', [
                    'products' => $emailArray
                ]),
                'text/html'
            );

            $mailer->send($message);
        }

        $io->success('Loop have been executed successfully, you can check the updated table!');

        return 0;
    }
}
