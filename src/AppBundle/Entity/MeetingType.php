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
 * @ORM\Table(name="meeting_type")
 */
class MeetingType {

    /**
     * @ORM\Id()
     * @ORM\Column(name="meeting_type_id", type = "integer", nullable=false)
     * @ORM\GeneratedValue(strategy = "IDENTITY")
     * @var integer
     */
    protected $meeting_type_id;

    /**
     * @ORM\Column (type = "string", length = 255)
     * @var string
     */
    protected $meetingType;

    /**
     * @ORM\Column (type = "string", length = 10)
     * @var string
     */
    protected $meetingTypeInitials;

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
        $meetingTypeInitials
    ) {

        $date = new \DateTime();
        $this->region = $region;
        $this->meetingTypeInitials = $meetingTypeInitials;
        $this->createdAt = $date;
        $this->modifiedAt = $date;
    }

    /**
     * @param string $meetingTypeInitials
     */
    public function setMeetingTypeInitials($meetingTypeInitials) {
        $this->meetingTypeInitials = $meetingTypeInitials;
    }


    public function getMeetingTypeInitials() {
        return $this->meetingTypeInitials;
    }

    /**
     *
     * @return string
     */
    public function getMeetingType() {
        return $this->meetingType;
    }


    public function setMeetingType($meetingType) {
        $this->meetingType = $meetingType;
    }

    /**
     *
     * @return int
     */
    public function getId() {
        return $this->meeting_type_id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return MeetingType
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
     * @return MeetingType
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
