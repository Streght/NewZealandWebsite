<?php

namespace Uniteam\PresentationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="Uniteam\PresentationBundle\Repository\CountryRepository")
 */
class Country
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="anthem", type="string", length=255)
     */
    private $anthem;

    /**
     * @var string
     *
     * @ORM\Column(name="domainname", type="string", length=255)
     */
    private $domainname;


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
     * Set name
     *
     * @param string $name
     *
     * @return Country
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

    /**
     * Set anthem
     *
     * @param string $anthem
     *
     * @return Country
     */
    public function setAnthem($anthem)
    {
        $this->anthem = $anthem;

        return $this;
    }

    /**
     * Get anthem
     *
     * @return string
     */
    public function getAnthem()
    {
        return $this->anthem;
    }

    /**
     * Set domainname
     *
     * @param string $domainname
     *
     * @return Country
     */
    public function setDomainname($domainname)
    {
        $this->domainname = $domainname;

        return $this;
    }

    /**
     * Get domainname
     *
     * @return string
     */
    public function getDomainname()
    {
        return $this->domainname;
    }
}

