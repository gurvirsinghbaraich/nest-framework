<?php

namespace Nest\Framework\Contracts;

interface Application
{
  /**
   * Gets the version number of the application.
   */
  public function version(): string;

  /**
   * Gets the basePath for the Nest installation.
   */
  public function basePath(): string;

  /**
   * Boot's the application.
   */
  public function boot(): void;
}
