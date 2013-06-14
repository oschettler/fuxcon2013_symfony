<?php
/**
 * @see http://symfony.com/doc/current/cookbook/form/data_transformers.html
 */
namespace FUxCon2013\ProjectsBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

class TagsTransformer implements DataTransformerInterface
{
    private $tagManager;

    public function __construct($tagManager)
    {
        $this->tagManager = $tagManager;
    }

    public function transform($tags)
    {
        return join(', ', $tags->toArray());
    }

    public function reverseTransform($tags)
    {
        return $this->tagManager->loadOrCreateTags(
            $this->tagManager->splitTagNames($tags)
        );
    }
}
