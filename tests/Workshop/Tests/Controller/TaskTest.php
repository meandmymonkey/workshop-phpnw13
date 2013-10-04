<?php

namespace Workshop\Tests\Controller;

use Workshop\Tests\WebTestCase;

class TaskTest extends WebTestCase
{
    public function testIndex()
    {
        $this->client->request('GET', '/');
        $response = $this->client->getResponse();
        $crawler = $this->client->getCrawler();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertContains('My Todo List', $crawler->filter('h1')->text());
        $this->assertEquals(9, $crawler->filter('tbody tr')->count());
    }

    public function testTaskDoesNotExist()
    {
        $this->markTestSkipped('TODO: implement proper error handling');

        $this->client->request('GET', '/unknown');
        $response = $this->client->getResponse();

        $this->assertSame(404, $response->getStatusCode());
    }
}
