<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mockery;

class DefaultControllerTest extends WebTestCase
{

	protected $mockChallengeController;
	protected $mockRequest;

    public function testIndex()
    {
        // $client = static::createClient();

        // $crawler = $client->request('GET', '/');

        // $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
}
