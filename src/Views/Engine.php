<?php

namespace Nest\Framework\Views;

use Jenssegers\Blade\Blade;
use Nest\Framework\Foundation\Application;

class Engine
{
  private static ?Blade $blade = null;

  /**
   * Get the Blade instance.
   */
  private static function blade(): Blade
  {
    if (self::$blade === null) {
      self::$blade = new Blade(Application::templatePath(), Application::basePath() . '/cache');
    }
    return self::$blade;
  }

  /**
   * Render a view using Blade template engine.
   */
  public static function render(string $templateName, array $variables = []): string
  {
    return self::blade()->render($templateName, $variables);
  }
}
