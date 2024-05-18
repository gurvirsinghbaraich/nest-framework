<?php

use Nest\Framework\Foundation\Application;
use Nest\Framework\Foundation\ApplicationBuilder;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
  public function testApplicationHasAVersionNumber()
  {
    $app = new Application;
    $version = $app->version();

    $this->assertEquals(gettype($version), "string");
  }

  public function testApplicationReturnsValidConfiguration()
  {
    $application = Application::configure(__DIR__);
    $this->assertInstanceOf(ApplicationBuilder::class, $application);
  }
}
