<?php

namespace Nest\Framework\Foundation;

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
}
