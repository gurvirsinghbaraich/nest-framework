<?php

namespace Nest\Framework\Http;

use Nest\Framework\Contracts\Http\Request as RequestContract;

class Request implements RequestContract
{
  /**
   * Gets whether the request has been made 
   * from http or https origin.
   */
  public static function scheme()
  {
    return $_SERVER['REQUEST_SCHEME'];
  }

  /**
   * Gets the request HTTP method.
   */
  public static function method()
  {
    return $_SERVER['REQUEST_METHOD'];
  }

  /**
   * Returns an instance of itself.
   */
  public static function capture()
  {
    return new self();
  }
}
