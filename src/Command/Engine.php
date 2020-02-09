<?php

namespace App\Command;

use App\Entity\Products;

class Engine
{
    private $language;
    private $discountRules = array();

    /**
     * Crée un nouveau moteur de tarification.
     *
     * @param Language $language Notre language personnalisé
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * Ajoute une règle de remise.
     *
     * @param string $expression L'expression de remise
     */
    public function addDiscountRule($expression)
    {
        $this->discountRules[] = $expression;
    }

    /**
     * Calcule le prix du produit.
     *
     * @param Products $product Le produit
     *
     * @return float Le prix
     */
    public function calculatePrice(Products $product)
    {
        $price = $product->getPrice();
        foreach ($this->discountRules as $discountRule) {
            $price -= $price * $this->language->evaluate($discountRule, array('product' => $product));
        }

        return $price;
    }
}