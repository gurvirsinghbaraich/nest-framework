<?php

namespace Nest\Framework\Foundation;

use Exception;
use Nest\Framework\Contracts\Foundation\Application as ApplicationContract;


class Application implements ApplicationContract
{
  /**
   * The installed version of the Nest Framework.
   */
  const VERSION = "0.0.12";

  /**
   * The basae path for the Nest installation.
   */
  protected string $basePath;

  /**
   * Indicates whether the application has been booted.
   */
  protected static bool $booted = false;

  /**
   * Gets the version number for the application.
   */
  public function version(): string
  {
    return static::VERSION;
  }

  /**
   * Creating configuration for the Nest application.
   */
  public static function configure(string $basePath)
  {
    return new ApplicationBuilder($basePath);
  }

  /**
   * Function to boot the application.
   */
  public static function boot(): void
  {
    // Marking the application as booted.
    self::$booted = true;
  }
}
