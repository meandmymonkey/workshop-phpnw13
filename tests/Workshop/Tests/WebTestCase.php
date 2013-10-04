<?php

namespace Workshop\Tests;

use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class WebTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var HttpKernelInterface */
    protected $kernel;

    /** @var Client */
    protected $client;

    public function setup()
    {
        $this->kernel = require __DIR__ . '/../../../src/bootstrap.php';
        $this->client = new Client($this->kernel);
    }
}
