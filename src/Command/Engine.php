<?php

namespace App\Command;

use App\Entity\Products;

class Engine
{
    private $language;
    private $discountRules = array();

    /**
     * @param Language $language custom language
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * Adding discount rule
     *
     * @param string $expression discount rule
     */
    public function addDiscountRule($expression)
    {
        $this->discountRules[] = $expression;
    }

    /**
     * Calculating price product
     *
     * @param Products $product the product
     *
     * @return float price
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