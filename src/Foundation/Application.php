<?php

namespace Nest\Framework\Foundation;

use Exception;
use Nest\Framework\Contracts\Application as ApplicationContract;


class Application implements ApplicationContract
{
  /**
   * The installed version of the Nest Framework.
   */
  const VERSION = "0.0.1";

  /**
   * The basae path for the Nest installation.
   */
  protected string $basePath;

  /**
   * Indicates whether the application has been booted.
   */
  protected bool $booted = false;

  /**
   * Gets the version number for the application.
   */
  public function version(): string
  {
    return static::VERSION;
  }

  /**
   * Gets the basePath for the Nest installation.
   */
  public function basePath(): string
  {
    return $this->basePath();
  }

  /**
   * Creating configuration for the Nest application.
   */
  public static function configure(string $basePath)
  {
    // Asserting to be sure of the fact that,
    // $basePath is of a string type.

    $basePath = match (true) {
      is_string($basePath) => $basePath,
      default => throw new \Exception('TypeError: Invalid type for $basePath. Expected a string, but reveived ' + gettype($basePath) + '.')
    };

    // TODO: Run the application
  }

  /**
   * Function to boot the application.
   */
  public function boot(): void
  {
    // TODO: Implement the logic, to start the application.
  }
}
