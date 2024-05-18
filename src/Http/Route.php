<?php

namespace Nest\Framework\Http;

use Exception;

class Route
{
  /**
   * This property is used to store the current HTTP request being processed by
   * the applicaiton. It provides access to request data, such as input parameters,
   * headers and other metadata.
   */
  private static Request $request;

  /**
   * This array lists he HTTP methods that the application supports fr router handling.
   * It is used to define which HTTP methods can be registered and processed by the
   * routing system.
   */
  private static array $methods = [
    "GET",
    "POST",
  ];

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
   * The constructor is used to assign a value to $reqeust property. This allows
   * RequestHandler (__destructor) to have reference to the current HTTP request
   * bein process, enabling it to access the data throughtout the application.
   */
  public function __construct(Request $request)
  {
    self::$request = $request;
  }

  /**
   * It takes the HTTP method (e.g., GET or POST), the path of the route, 
   * and a handler that will handle the request when it matches the route. If the
   * HTTP method is defined, the the function will add the request to appropriate array.
   */
  private static function publishRoute(string $method, string $path, $handler): void
  {
    // Check if the provided HTTP method exists in the $routes array.
    if (isset(self::$routes[$method])) {
      // Asserting whether the handler is an array, hence an anonymus function.
      if (is_array($handler)) {
        // The handler must reference a class, and method
        if (!isset($handler[0])) {
          throw new \Exception("No handler class provided for the route: '$path'. Please ensure a valid handler is specified for this route.");
        }

        // The handler must reference a method for the class provided.
        if (!isset($handler[1])) {
          throw new \Exception("The handler method '{$handler[0]}' is not provided for the route: '{$path}'. Please ensure that the handler method is correctly defined and accessible for this route.");
        }

        $handlerClass = $handler[0];
        $handlerMethod = $handler[1];

        if (!class_exists($handlerClass)) {
          throw new \Exception("The handler class '{$handlerClass}' does not exist. Please ensure that the class name is correct and the class is properly included or autoloaded.");
        }

        if (!method_exists($handlerClass, $handlerMethod)) {
          throw new \Exception("The handler method '{$handlerMethod}' does not exist. Please ensure that the method name is correct and the method is properly defined within the handler class.");
        }
      }

      // Make sure that the passed handler is callable.
      if (!is_callable($handler)) {
        throw new \Exception("The provided handler is not callable. Please pass a valid function or method as the handler.");
      }

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
   * This method allows the registeration of a route that responds to any HTTP requests.
   * It takes the path of the route and a handler, and deletegates the task to the
   * publishRoute method with each HTTP method listed that are supported by the 
   * routing system.
   */
  public static function ANY(string $path, $handler): void
  {
    foreach (self::$methods as $method) {
      self::publishRoute($method, $path, $handler);
    }
  }

  /**
   * A destructor is a function that is called
   * when the instance of the class is destructed
   * 
   * @docs https://www.php.net/manual/en/language.oop5.decon.php
   */
  public function __destruct()
  {
    // Getting the routes that are appropriate for the request type.
    $requestMethod = self::$request->method();

    if (!isset(self::$routes[$requestMethod])) {
      http_response_code(405);

      // TODO: Return HTML for 405 request.
      return;
    }

    $routes = self::$routes[$requestMethod];
  }
}
