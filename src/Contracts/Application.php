<?php

namespace Nest\Framework\Contracts;

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
