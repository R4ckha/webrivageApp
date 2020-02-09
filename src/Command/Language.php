<?php

namespace App\Command;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class Language extends ExpressionLanguage
{
    protected function registerFunctions()
    {
        // register 'date' function
        $this->register('date', function ($date) {
            return sprintf('(new \DateTime(%s))', $date);
        }, function (array $values, $date) {
            return new \DateTime($date);
        });

        // register 'date_modify' function
        $this->register('date_modify', function ($date, $modify) {
            return sprintf('%s->modify(%s)', $date, $modify);
        }, function (array $values, $date, $modify) {
            if (!$date instanceof \DateTime) {
                throw new \RuntimeException('date_modify() expects parameter 1 to be a Date');
            }
            return $date->modify($modify);
        });
    }
}