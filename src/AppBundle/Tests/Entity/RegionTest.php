<?php

namespace AppBundle\Tests\Entity;

use Mockery;
use AppBundle\Entity\Region;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Constraints\DateTime;

class RegionTest extends WebTestCase
{
    protected $region;

    public function setUp()
    {
        $this->region = new Region('California', 'CA');
    }

    public function testPostConstructor()
    {
        $this->assertEquals('California', $this->region->getRegion());
        $this->assertEquals('CA', $this->region->getRegionAbbrev());
    }

    public function testSetRegion()
    {
        $this->region->setRegion('Arizona');
        $this->assertEquals('Arizona', $this->region->getRegion());
    }

    public function testSetRegionAbbrev()
    {
        $this->region->setRegionAbbrev('AZ');
        $this->assertEquals('AZ', $this->region->getRegionAbbrev());
    }

    public function testSetCreatedAt()
    {
        $date = new DateTime();
        $this->region->setCreatedAt($date);
        $this->assertEquals($date, $this->region->getCreatedAt());
    }

    public function testSetModifiedAt()
    {
        $date = new DateTime();
        $this->region->setModifiedAt($date);
        $this->assertEquals($date, $this->region->getModifiedAt());
    }
}
