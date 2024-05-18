<?php

namespace Nest\Framework\Http;

use Exception;
use Nest\Framework\Foundation\Application;
use PHPUnit\TextUI\XmlConfiguration\RenameForceCoversAnnotationAttribute;

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
    Route::$request = $request;
  }

  /**
   * It takes the HTTP method (e.g., GET or POST), the path of the route, 
   * and a handler that will handle the request when it matches the route. If the
   * HTTP method is defined, the the function will add the request to appropriate array.
   */
  private static function publishRoute(string $method, string $path, $handler): void
  {
    // Check if the provided HTTP method exists in the $routes array.
    if (isset(Route::$routes[$method])) {
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
      else if (!is_callable($handler)) {
        throw new \Exception("The provided handler is not callable. Please pass a valid function or method as the handler.");
      }

      // Add the route path and its handler to the respective HTTP method array.
      Route::$routes[$method][] = [$path, $handler];
    }
  }

  /**
   * This method allows the registration of a route that responds to GET requests.
   * It takes the path of the route and a handler, and delegates the task to the
   * publishRoute method with the HTTP method set to GET.
   */
  public static function GET(string $path, $handler): void
  {
    Route::publishRoute("GET", $path, $handler);
  }

  /**
   * This method allows the registration of a route that responds to POST requests.
   * It takes the path of the route and a handler, and delegates the task to the
   * publishRoute method with the HTTP method set to POST.
   */
  public static function POST(string $path, $handler): void
  {
    Route::publishRoute("POST", $path, $handler);
  }

  /**
   * This method allows the registeration of a route that responds to any HTTP requests.
   * It takes the path of the route and a handler, and deletegates the task to the
   * publishRoute method with each HTTP method listed that are supported by the 
   * routing system.
   */
  public static function ANY(string $path, $handler): void
  {
    foreach (Route::$methods as $method) {
      Route::publishRoute($method, $path, $handler);
    }
  }

  public static function getVariables(string $path)
  {
    $regex = '/:(\w+)/m';

    preg_match_all($regex, $path, $matches, PREG_SET_ORDER, 0);
    return $matches;
  }

  public static function getRouteVariables(array $route)
  {
    $path = $route[0];
    $variables = Route::getVariables($path);

    return $variables;
  }


  /**
   * A destructor is a function that is called
   * when the instance of the class is destructed
   * 
   * @docs https://www.php.net/manual/en/language.oop5.decon.php
   */
  public function __destruct()
  {
    // Get the current request URI and method.
    $requestURI = Route::$request->uri();
    $requestMethod = Route::$request->method();

    // Check if the request method is supported.
    if (!isset(Route::$routes[$requestMethod])) {
      http_response_code(405); // Set the HTTP response code to 405 (Method Not Allowed).
      echo "405 Method Not Allowed"; // Return a message for 405 Method Not Allowed.
      return;
    }

    // Get the routes for the current request method.
    $routes = Route::$routes[$requestMethod];

    // Iterate over the routes to find a match for the request URI.
    foreach ($routes as $route) {
      // Extract route variables (e.g., parameters in the URI).
      $variables = Route::getRouteVariables($route);

      // If the route contains variables, process them.
      if (count($variables) > 0) {
        // Construct a regex to match the route with variables.
        $regexBase = str_repeat('\/(\w+)', count($variables));
        $regex = "/" . $regexBase . "/m";

        // Match the regex against the request URI.
        preg_match_all($regex, $requestURI, $mappings, PREG_SET_ORDER, 0);

        // If a matching route is found, set the parameters.
        if (count($mappings) > 0) {
          foreach ($variables as $key => $variable) {
            $variableName = $variable[1];
            $variableValue = $mappings[0][$key + 1];
            Request::setParams($variableName, $variableValue); // Set the route parameters in the request.
          }
          return; // Exit the function if a match is found.
        }
      } else {
        // If the route has no variables and matches exactly, return.
        if ($route === $requestURI) {
          return; // Exit the function if an exact match is found.
        }
      }
    }

    // If no route matched the requested URI, return a 404 response.
    http_response_code(404); // Set the HTTP response code to 404 (Not Found).
    echo view('errors/404');
    die();
  }
}
