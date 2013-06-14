<?php
/**
 * @see http://symfony.com/doc/current/cookbook/form/data_transformers.html
 */
namespace FUxCon2013\ProjectsBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

class DateTransformer implements DataTransformerInterface
{
    public function transform($date)
    {
        if ($date) {
            return $date->format('Y-m-d');
        }
        else {
            return date('Y-m-d');
        }
    }

    public function reverseTransform($date)
    {
       return \DateTime::createFromFormat('Y-m-d', $date);
    }
}
