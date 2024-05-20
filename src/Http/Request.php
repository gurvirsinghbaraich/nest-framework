<?php

namespace Nest\Framework\Http;

use Nest\Framework\Contracts\Http\Request as RequestContract;

class Request implements RequestContract
{
  private static array $params = [];

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
   * Gets the request URI.
   */
  public static function uri()
  {
    return $_SERVER['REQUEST_URI'];
  }

  /**
   * Returns an instance of itself.
   */
  public static function capture()
  {
    return new self();
  }

  public static function getParams()
  {
    return Request::$params;
  }

  public static  function setParams(array $params)
  {
    foreach ($params as $key => $value) {
      Request::setParam($key, $value);
    }
  }

  public static function setParam(string $key, string $value)
  {
    Request::$params[$key] = $value;
  }

  public function __set($name, $value)
  {
    Request::$params[$name] = $value;
  }
}
