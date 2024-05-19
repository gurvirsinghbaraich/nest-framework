<?php

namespace Nest\Framework\Foundation;

use Exception;
use Nest\Framework\Contracts\Foundation\Application as ApplicationContract;
use Nest\Framework\Http\Request;
use Nest\Framework\Utils\UtilsLoader;

class Application implements ApplicationContract
{
  /**
   * The installed version of the Nest Framework.
   */
  const VERSION = "0.0.18";

  /**
   * The basae path for the Nest installation.
   */
  protected static string $basePath;
  protected static string $templatePath;

  /**
   * Indicates whether the application has been booted.
   */
  protected static bool $booted = false;

  public static function basePath()
  {
    return Application::$basePath;
  }

  public static function templatePath()
  {
    return Application::$templatePath;
  }

  /**
   * Gets the version number for the application.
   */
  public function version(): string
  {
    return static::VERSION;
  }

  /**
   * Registering handler for the Nest Framework.
   */
  private static function registerErrorHandler()
  {
    $handler = \Whoops\Handler\PrettyPageHandler::class;

    if (Request::method() === "POST") {
      $handler = \Whoops\Handler\JsonResponseHandler::class;
    }

    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new $handler);
    $whoops->register();
  }

  /**
   * Creating configuration for the Nest application.
   */
  public static function configure(string $basePath)
  {
    Application::registerErrorHandler();

    Application::$basePath = $basePath;
    Application::$templatePath = $basePath . '/templates';
    UtilsLoader::load();

    return new ApplicationBuilder($basePath);
  }

  /**
   * Function to boot the application.
   */
  public static function boot(): void
  {
    // Marking the application as booted.
    Application::$booted = true;
  }
}
