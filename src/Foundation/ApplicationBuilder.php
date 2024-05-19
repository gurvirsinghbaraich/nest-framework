<?php

namespace Nest\Framework\Foundation;

use Nest\Framework\Http\Request;
use Nest\Framework\Http\Route;

class ApplicationBuilder
{
  public function __construct(string $basePath)
  {
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
    new Route($request);
  }
}
