<?php

namespace Nest\Framework\Contracts\Foundation;


interface Application
{
  /**
   * Gets the version number of the application.
   */
  public function version(): string;

  /**
   * Boot's the application.
   */
  public static function boot(): void;
}
