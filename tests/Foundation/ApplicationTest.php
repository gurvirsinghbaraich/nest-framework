<?php

use Nest\Framework\Foundation\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
  public function testApplicationHasAVersionNumber()
  {
    $app = new Application;
    $version = $app->version();

    $this->assertEquals(gettype($version), "string");
  }
}
