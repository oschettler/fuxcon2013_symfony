<?php

namespace FUxCon2013\ProjectsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Project
 */
class Project implements Taggable
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var string
     */
    private $about;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $modified;

    /**
     * @var User
     */
    private $user;

    private $tags;

    private $picture;

    public function __construct()
    {
        $this->setCreated(new \DateTime());
        $this->setModified(new \DateTime());
    }


    public function getTags()
    {
        $this->tags = $this->tags ?: new ArrayCollection();

        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = is_array($tags) ? new ArrayCollection($tags) : $tags;
    }

    public function getTaggableType()
    {
        return 'project';
    }

    public function getTaggableId()
    {
        return $this->getId();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Project
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Project
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    
        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Project
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    
        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set about
     *
     * @param string $about
     * @return Project
     */
    public function setAbout($about)
    {
        $this->about = $about;
    
        return $this;
    }

    /**
     * Get about
     *
     * @return string 
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Project
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Project
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    
        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set user
     *
     * @param \FUxCon2013\ProjectsBundle\Entity\User $user
     * @return Project
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \FUxCon2013\ProjectsBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    private function getPicturePath()
    {
         return __DIR__ . '/../../../../web/images/project/' . $this->getId() . '.jpg';
    }

    public function getPicture()
    {
        if (empty($this->picture)) {
            return null;
        }
        return new \Symfony\Component\HttpFoundation\File\File(
            $this->getPicturePath(), /*checkPath*/false
        );
    }

    public function setPicture($picture)
    {
        $mimeType = $picture->getMimeType();
        if (!in_array($mimeType, array('image/jpeg'))) {
            throw Exception("You may only have images of type JPEG");
        }
        $this->picture = $picture;
        error_log("SET PICTURE: " . json_encode($picture) . "\n", 3, "/tmp/symfony.log");
    }

    public function processPicture()
    {
        if (! ($this->picture instanceof UploadedFile) ) {
            return false;
        }

        $file = $this->getPicturePath();

        $this->picture->move(
            pathinfo($file, PATHINFO_DIRNAME),
            pathinfo($file, PATHINFO_BASENAME)
        );
        return true;
    }
}