<?php

namespace FUxCon2013\ProjectsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FPN\TagBundle\Entity\Tag as BaseTag;

/**
 * Tag
 */
class Tag extends BaseTag
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $tagging;

    /**
     * Constructor
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->tagging = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add tagging
     *
     * @param \FUxCon2013\ProjectsBundle\Entity\Tagging $tagging
     * @return Tag
     */
    public function addTagging(\FUxCon2013\ProjectsBundle\Entity\Tagging $tagging)
    {
        $this->tagging[] = $tagging;
    
        return $this;
    }

    /**
     * Remove tagging
     *
     * @param \FUxCon2013\ProjectsBundle\Entity\Tagging $tagging
     */
    public function removeTagging(\FUxCon2013\ProjectsBundle\Entity\Tagging $tagging)
    {
        $this->tagging->removeElement($tagging);
    }

    /**
     * Get tagging
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTagging()
    {
        return $this->tagging;
    }
 }