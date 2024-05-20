<?php

use PHPUnit\Framework\TestCase;
use Nest\Framework\Http\Route;

// Define the RouteTest class, extending the PHPUnit TestCase class to create unit tests
class RouteTest extends TestCase
{
  // Test to verify that a single variable in the URI is correctly identified and mapped
  public function testThatAbleToIdentifySingleVariable()
  {
    $URI = "/users/:userId"; // Define the route pattern with a variable
    $requestURI = "/users/5"; // Define the actual request URI
    $mappings = Route::getVariablesFromRequest($URI, $requestURI); // Extract variables from the request URI based on the pattern

    // Assert that the extracted variable matches the expected result
    $this->assertEquals(['userId' => '5'], $mappings);
  }

  // Test to verify that multiple variables in the URI are correctly identified and mapped
  public function testThatAbleToIdentifyMultipleVariables()
  {
    $URI = "/:category/:action:/:color"; // Define the route pattern with multiple variables
    $requestURI = '/shoes/update/white'; // Define the actual request URI
    $mappings = Route::getVariablesFromRequest($URI, $requestURI); // Extract variables from the request URI based on the pattern

    // Assert that the extracted variables match the expected result
    $this->assertEquals(
      $mappings,
      [
        'category' => 'shoes',
        'action' => 'update',
        'color' => 'white'
      ],
    );
  }

  // Test to verify that variables are correctly extracted from a URI with a static endpoint
  public function testThatAbleToGetVariablesWithStaticEndpointV1()
  {
    $URI = '/users/:user/invite'; // Define the route pattern with a variable and static endpoint
    $requestURI = '/users/51/invite'; // Define the actual request URI
    $mappings = Route::getVariablesFromRequest($URI, $requestURI); // Extract variables from the request URI based on the pattern

    // Assert that the extracted variable matches the expected result
    $this->assertEquals($mappings, ['user' => 51]);
  }

  // Test to verify that non-adjacent variables in the URI are correctly identified and mapped
  public function testThatAbleToGetVariablesThatArentAdjacent()
  {
    $URI = '/:user/ban/:timeout'; // Define the route pattern with non-adjacent variables
    $requestURI = '/69/ban/60'; // Define the actual request URI
    $mappings = Route::getVariablesFromRequest($URI, $requestURI); // Extract variables from the request URI based on the pattern

    // Assert that the extracted variables match the expected result
    $this->assertEquals($mappings, ['user' => 69, 'timeout' => 60]);
  }

  // Test to verify that a mismatch in the route pattern and request URI returns null
  public function testThatOnMisMatchRouteReturnNull()
  {
    $URI = '/ban/:user/:timeout'; // Define the route pattern
    $requestURI = '/unban/69'; // Define the mismatched request URI
    $mappings = Route::getVariablesFromRequest($URI, $requestURI); // Try to extract variables from the mismatched request URI

    // Assert that the result is null due to the mismatch
    $this->assertEquals($mappings, null);
  }

  // Test to verify that static routes are correctly matched
  public function testForMatchingStaticRoutes()
  {
    $routes = [
      ['/update/:user'], // Define a route pattern with a variable
      ['/ban/:user/:timeout'], // Define another route pattern with multiple variables
      ['/unban/:user'], // Define another route pattern with a variable
      ['/'], // Define a static route
    ];

    $requestURI = '/'; // Define the request URI for a static route
    $matchingRoute = Route::getMatchingRoute($routes, $requestURI); // Find the matching route for the request URI

    // Assert that the matching route is correctly identified
    $this->assertEquals($matchingRoute, ['/']);
  }

  // Test to verify that dynamic routes are correctly matched
  public function testForMatchingDynamicRoutes()
  {
    $routes = [
      ['/update/:user'], // Define a route pattern with a variable
      ['/ban/:user/:timeout'], // Define another route pattern with multiple variables
      ['/unban/:user'] // Define another route pattern with a variable
    ];

    $requestURIs = [
      '/update/8', // Define a request URI matching the first route
      '/ban/31/60', // Define a request URI matching the second route
      '/unban/31' // Define a request URI matching the third route
    ];

    // Iterate through each request URI and assert that the matching route is correctly identified
    foreach ($requestURIs as $key => $requestURI) {
      $matchingRoute = Route::getMatchingRoute($routes, $requestURI);
      $this->assertEquals($matchingRoute, $routes[$key]);
    }
  }
}
