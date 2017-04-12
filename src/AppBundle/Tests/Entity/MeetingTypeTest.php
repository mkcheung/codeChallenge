<?php

namespace AppBundle\Tests\Entity;

use Mockery;
use AppBundle\Entity\MeetingType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Constraints\DateTime;

class MeetingTypeTest extends WebTestCase
{
    protected $meetingType;

    public function setUp()
    {
        $this->meetingType = new MeetingType('Alcoholics Anonymous', 'AA');
    }

    public function testPostConstructor()
    {
        $this->assertEquals('Alcoholics Anonymous', $this->meetingType->getMeetingType());
        $this->assertEquals('AA', $this->meetingType->getMeetingTypeInitials());
    }

    public function testSetRegion()
    {
        $this->meetingType->setMeetingType('Narcotics Anonymous');
        $this->assertEquals('Narcotics Anonymous', $this->meetingType->getMeetingType());
    }

    public function testSetRegionAbbrev()
    {
        $this->meetingType->setMeetingTypeInitials('NA');
        $this->assertEquals('NA', $this->meetingType->getMeetingTypeInitials());
    }

    public function testSetCreatedAt()
    {
        $date = new DateTime();
        $this->meetingType->setCreatedAt($date);
        $this->assertEquals($date, $this->meetingType->getCreatedAt());
    }

    public function testSetModifiedAt()
    {
        $date = new DateTime();
        $this->meetingType->setModifiedAt($date);
        $this->assertEquals($date, $this->meetingType->getModifiedAt());
    }
}
