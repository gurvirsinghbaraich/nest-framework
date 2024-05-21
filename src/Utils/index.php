<?php

use Nest\Framework\Http\Session;
use Nest\Framework\Views\Engine;

/**
 * Render a view using Blade template engine.
 */
function view(string $templateName, array $variables = []): string
{
  return Engine::render($templateName, $variables);
}


function session(mixed $value)
{
  if (is_array($value)) {
    foreach ($value as $key => $val) {
      return Session::put($key, $val);
    }
  }


  return Session::get(str($value));
}
