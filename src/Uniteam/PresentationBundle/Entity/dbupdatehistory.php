<?php

namespace Uniteam\PresentationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * dbupdatehistory
 *
 * @ORM\Table(name="dbupdatehistory")
 * @ORM\Entity(repositoryClass="Uniteam\PresentationBundle\Repository\dbupdatehistoryRepository")
 */
class dbupdatehistory
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
     * @var \DateTime
     *
     * @ORM\Column(name="lastUpdateTime", type="datetime")
     */
    private $lastUpdateTime;


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
     * Set lastUpdateTime
     *
     * @param \DateTime $lastUpdateTime
     *
     * @return dbupdatehistory
     */
    public function setLastUpdateTime($lastUpdateTime)
    {
        $this->lastUpdateTime = $lastUpdateTime;

        return $this;
    }

    /**
     * Get lastUpdateTime
     *
     * @return \DateTime
     */
    public function getLastUpdateTime()
    {
        return $this->lastUpdateTime;
    }
}

