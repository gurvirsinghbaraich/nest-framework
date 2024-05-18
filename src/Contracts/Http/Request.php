<?php

namespace Nest\Framework\Contracts\Http;

interface Request
{
  /**
   * Gets the HTTP request method.
   */
  public static function method();
}
