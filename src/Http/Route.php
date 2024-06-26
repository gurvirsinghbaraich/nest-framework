<?php

namespace Nest\Framework\Http;

use ReflectionClass;
use ReflectionFunction;

class Route
{
  /**
   * Stores the current HTTP request being processed by the application.
   * Provides access to request data such as input parameters, headers, and other metadata.
   */
  private static Request $request;

  /**
   * Lists the HTTP methods that the application supports for route handling.
   * Used to define which HTTP methods can be registered and processed by the routing system.
   */
  private static array $methods = ["GET", "POST"];

  /**
   * Stores routes for the application. Different HTTP methods (GET and POST)
   * are used as keys, and their corresponding routes are stored as values.
   */
  private static array $routes = [
    "GET" => [],
    "POST" => []
  ];

  /**
   * Constructor to assign a value to the $request property.
   * Allows RequestHandler (__destructor) to have a reference to the current HTTP request.
   */
  public function __construct(Request $request)
  {
    session_start();
    Route::$request = $request;
  }

  /**
   * Registers a route for the GET HTTP method.
   */
  public static function GET(string $path, $handler): void
  {
    Route::publishRoute("GET", $path, $handler);
  }

  /**
   * Registers a route for the POST HTTP method.
   */
  public static function POST(string $path, $handler): void
  {
    Route::publishRoute("POST", $path, $handler);
  }

  /**
   * Registers a route for any supported HTTP method.
   */
  public static function ANY(string $path, $handler): void
  {
    foreach (Route::$methods as $method) {
      Route::publishRoute($method, $path, $handler);
    }
  }

  /**
   * Publishes a route for the specified HTTP method.
   */
  private static function publishRoute(string $method, string $path, $handler): void
  {
    // Check if the provided HTTP method exists in the $routes array.
    if (isset(Route::$routes[$method])) {
      // Validate the handler.
      Route::validateHandler($handler);

      // Add the route path and its handler to the respective HTTP method array.
      Route::$routes[$method][] = [$path, $handler];
    }
  }

  /**
   * Validates the handler for a route.
   */
  private static function validateHandler($handler): void
  {
    // Validate if the handler is an array (likely a class method).
    if (is_array($handler)) {
      // Ensure the class and method are specified.
      if (!isset($handler[0]) || !isset($handler[1])) {
        throw new \Exception("Invalid handler format. Please provide both class and method for the handler.");
      }

      // Ensure the class exists.
      if (!class_exists($handler[0])) {
        throw new \Exception("The handler class '{$handler[0]}' does not exist.");
      }

      // Ensure the method exists in the class.
      if (!method_exists($handler[0], $handler[1])) {
        throw new \Exception("The handler method '{$handler[1]}' does not exist in class '{$handler[0]}'.");
      }
    } elseif (!is_callable($handler)) {
      // Ensure the handler is callable.
      throw new \Exception("The provided handler is not callable. Please pass a valid function or method.");
    }
  }

  private function getFunctionHandlerDependancies($handler)
  {
    $reflectionClass = new ReflectionFunction($handler);
    $parameters = $reflectionClass->getParameters();

    $arguments = [];

    foreach ($parameters as $parameter) {
      $dependancy = $parameter->getType()->getName();
      $arguments[] = new $dependancy;
    }

    return $arguments;
  }

  private function getHandlerDependancies(string $class, string $method)
  {
    $reflectionClass = new ReflectionClass($class);
    $parameters = $reflectionClass->getMethod($method)->getParameters();
    $arguments = [];

    foreach ($parameters as $parameter) {
      $dependancy = $parameter->getType()->getName();
      $arguments[] = new $dependancy;
    }

    return $arguments;
  }

  private function getHandlerResponse($handler)
  {
    if (is_callable($handler)) {
      return call_user_func($handler, ...($this->getFunctionHandlerDependancies($handler)));
    }

    $className = $handler[0];
    $methodName = $handler[1];
    $dependancies = $this->getHandlerDependancies($className, $methodName);

    return call_user_func(
      array(new $className, $methodName),
      ...$dependancies
    );
  }

  private static function getVariablesFromRequest(string $URI, string $requestURI): null | array
  {
    $mappings = [];
    $requestSections = explode("/", $URI);
    $pathSections = explode("/", $requestURI);

    if (count($requestSections) < count($pathSections)) {
      return null;
    }

    foreach ($requestSections as $key => $section) {
      if (str_contains($section, ":")) {
        $mappingName = explode(":", $section)[1];
        $mappings[$mappingName] = $pathSections[$key];
      } else if ($section !== $pathSections[$key]) return null;
    }

    return $mappings;
  }


  private static function getMatchingRoute(array $routes, string $requestURI)
  {
    // Iterate over the routes to find a match for the request URI.
    foreach ($routes as $route) {
      // Extract route variables (e.g., parameters in the URI).
      $mappings = Route::getVariablesFromRequest($route[0], $requestURI);

      // If the route contains variables, process them.
      if (is_array($mappings)) {
        Request::setParams($mappings); // Set the route parameters in the request.
        return $route;
      } else {
        // If the route has no variables and matches exactly, return.
        if ($route[0] === $requestURI) {
          return $route;
        }
      }
    }

    return null;
  }

  /**
   * Destructor to handle route resolution.
   * Matches the requested URI with registered routes.
   */
  public function __destruct()
  {
    // Get the current request URI and method.
    $requestURI = Route::$request->uri();
    $requestMethod = Route::$request->method();

    // Check if the request method is supported.
    if (!isset(Route::$routes[$requestMethod])) {
      echo view('errors.http', [
        'statusCode' => 405,
        'statusMessage' => "Method Not Allowed"
      ]);

      die();
    }

    // Get the routes for the current request method.
    $routes = Route::$routes[$requestMethod];
    $matchingRoute = Route::getMatchingRoute($routes, $requestURI);

    if ($matchingRoute) {
      echo $this->getHandlerResponse($matchingRoute[1]);
      exit;
    }
    // If no route matched the requested URI, return a 404 response.
    echo view('errors.http', [
      'statusCode' => 404,
      'statusMessage' => "Page Not Found"
    ]);

    die();
  }
}
