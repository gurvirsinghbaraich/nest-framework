<?php

use Nest\Framework\Foundation\ApplicationBuilder;

function view(string $templateName, array $variables = [])
{
  ApplicationBuilder::blade()->render($templateName, $variables);
}
