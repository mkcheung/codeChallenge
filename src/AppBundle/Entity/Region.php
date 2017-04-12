<?php
/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 4/8/17
 * Time: 10:01 PM
 */


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\DateTimeType;
/**
 * @ORM\Entity
 * @ORM\Table(name="region")
 */
class Region {

    /**
     * @ORM\Id()
     * @ORM\Column(name="region_id", type = "integer", nullable=false)
     * @ORM\GeneratedValue(strategy = "IDENTITY")
     * @var integer
     */
    protected $region_id;

    /**
     * @ORM\Column (type = "string", length = 255)
     * @var string
     */
    protected $region;

    /**
     * @ORM\Column (type = "string", length = 10)
     * @var string
     */
    protected $regionAbbrev;

    /**
     * @var string
     * @ORM\Column(name="createdAt", type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @var string
     * @ORM\Column(name="modifiedAt", type="datetime", nullable=false)
     */
    protected $modifiedAt;


    public function __construct(
        $region,
        $regionAbbrev
    ) {

        $date = new \DateTime();
        $this->region = $region;
        $this->regionAbbrev = $regionAbbrev;
        $this->createdAt = $date;
        $this->modifiedAt = $date;
    }

    /**
     * @param string $regionAbbrev
     */
    public function setRegionAbbrev($regionAbbrev) {
        $this->regionAbbrev = $regionAbbrev;
    }
    /**
     * @param string $content
     */
    public function getRegionAbbrev() {
        return $this->regionAbbrev;
    }

    /**
     *
     * @return string
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     *
     * @return string
     */
    public function setRegion($region) {
        $this->region = $region;
    }

    /**
     *
     * @return int
     */
    public function getId() {
        return $this->region_id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return MessageUser
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     * @return MessageUser
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }
}
