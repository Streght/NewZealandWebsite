<?php

namespace Uniteam\PresentationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dbupdatehistory
 *
 * @ORM\Table(name="dbupdatehistory")
 * @ORM\Entity(repositoryClass="Uniteam\PresentationBundle\Repository\DbupdatehistoryRepository")
 */
class Dbupdatehistory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="page", type="string", length=255)
     */
    private $page;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastupdatetime", type="datetime")
     */
    private $lastupdatetime;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set page
     *
     * @param string $page
     *
     * @return Dbupdatehistory
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set lastupdatetime
     *
     * @param \DateTime $lastupdatetime
     *
     * @return Dbupdatehistory
     */
    public function setLastupdatetime($lastupdatetime)
    {
        $this->lastupdatetime = $lastupdatetime;

        return $this;
    }

    /**
     * Get lastupdatetime
     *
     * @return \DateTime
     */
    public function getLastupdatetime()
    {
        return $this->lastupdatetime;
    }
}

