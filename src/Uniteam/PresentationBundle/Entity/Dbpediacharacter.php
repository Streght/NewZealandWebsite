<?php

namespace Uniteam\PresentationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dbpediacharacter
 *
 * @ORM\Table(name="dbpediacharacter")
 * @ORM\Entity(repositoryClass="Uniteam\PresentationBundle\Repository\DbpediacharacterRepository")
 */
class Dbpediacharacter
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
     * @var int
     *
     * @ORM\Column(name="nbnaissennz", type="integer")
     */
    private $nbnaissennz;

    /**
     * @var int
     *
     * @ORM\Column(name="nbdecesennz", type="integer")
     */
    private $nbdecesennz;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


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
     * Set nbnaissennz
     *
     * @param integer $nbnaissennz
     *
     * @return Dbpediacharacter
     */
    public function setNbnaissennz($nbnaissennz)
    {
        $this->nbnaissennz = $nbnaissennz;

        return $this;
    }

    /**
     * Get nbnaissennz
     *
     * @return int
     */
    public function getNbnaissennz()
    {
        return $this->nbnaissennz;
    }

    /**
     * Set nbdecesennz
     *
     * @param integer $nbdecesennz
     *
     * @return Dbpediacharacter
     */
    public function setNbdecesennz($nbdecesennz)
    {
        $this->nbdecesennz = $nbdecesennz;

        return $this;
    }

    /**
     * Get nbdecesennz
     *
     * @return int
     */
    public function getNbdecesennz()
    {
        return $this->nbdecesennz;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Dbpediacharacter
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}

