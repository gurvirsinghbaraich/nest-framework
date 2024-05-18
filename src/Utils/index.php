<?php

use Nest\Framework\Views\Engine;

/**
 * Render a view using Blade template engine.
 */
function view(string $templateName, array $variables = []): string
{
  return Engine::render($templateName, $variables);
}
