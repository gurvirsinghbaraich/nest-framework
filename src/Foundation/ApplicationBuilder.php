<?php

namespace Nest\Framework\Foundation;

use Jenssegers\Blade\Blade;
use Nest\Framework\Http\Request;
use Nest\Framework\Http\Route;

class ApplicationBuilder
{
  private static $blade;

  public static function blade()
  {
    return ApplicationBuilder::$blade;
  }

  public function __construct(string $basePath)
  {
    ApplicationBuilder::$blade = new Blade(Application::templatePath(), Application::basePath() . '/cache');

    // Getting all the registered routes for the application
    if (file_exists($web_routes = $basePath . '/routes/web.php')) {
      require $web_routes;
    }

    return $this;
  }

  /**
   * Handle the incoming request and return a response.
   */
  public function handleRequest(Request $request)
  {
    $response = new Route($request);
  }
}
