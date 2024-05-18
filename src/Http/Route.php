<?php

namespace Nest\Framework\Http;

class Route
{
  private static Request $request;

  /**
   * Stores routes for the application in a static array. This array holds
   * different HTTP methods (GET and POST) as keys, and their corresponding
   * routes as values. 
   * 
   * As routes are defined in teh application, they will be added to these arrays.
   */
  private static array $routes = [
    "GET" => [],
    "POST" => []
  ];

  /**
   * It takes the HTTP method (e.g., GET or POST), the path of the route, 
   * and a handler that will handle the request when it matches the route. If the
   * HTTP method is defined, the the function will add the request to appropriate array.
   */
  private static function publishRoute(string $method, string $path, $handler): void
  {
    // Check if the provided HTTP method exists in the $routes array.
    if (isset(self::$routes[$method])) {
      // Add the route path and its handler to the respective HTTP method array.
      self::$routes[$method][] = [$path, $handler];
    }
  }

  /**
   * This method allows the registration of a route that responds to GET requests.
   * It takes the path of the route and a handler, and delegates the task to the
   * publishRoute method with the HTTP method set to GET.
   */
  public static function GET(string $path, $handler): void
  {
    self::publishRoute("GET", $path, $handler);
  }

  /**
   * This method allows the registration of a route that responds to POST requests.
   * It takes the path of the route and a handler, and delegates the task to the
   * publishRoute method with the HTTP method set to POST.
   */
  public static function POST(string $path, $handler): void
  {
    self::publishRoute("POST", $path, $handler);
  }

  /**
   * A destructor is a function that is called
   * when the instance of the class is destructed
   * 
   * @docs https://www.php.net/manual/en/language.oop5.decon.php
   */
  public function __destruct()
  {
  }
}
