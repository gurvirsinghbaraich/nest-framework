<?php

use PHPUnit\Framework\TestCase;
use Nest\Framework\Http\Route;

class RouteTest extends TestCase
{
  public function testThatAbleToIdentifySingleVariable()
  {
    $URI = "/users/:userId";
    $requestURI = "/users/5";
    $mappings = Route::getVariablesFromRequest($URI, $requestURI);

    $this->assertEquals(['userId' => '5'], $mappings);
  }

  public function testThatAbleToIdentifyMultipleVariables()
  {
    $URI = "/:category/:action:/:color";
    $requestURI = '/shoes/update/white';
    $mappings = Route::getVariablesFromRequest($URI, $requestURI);

    $this->assertEquals(
      $mappings,
      [
        'category' => 'shoes',
        'action' => 'update',
        'color' => 'white'
      ],
    );
  }

  public function testThatAbleToGetVariablesWithStaticEndpointV1()
  {
    $URI = '/users/:user/invite';
    $requestURI = '/users/51/invite';
    $mappings = Route::getVariablesFromRequest($URI, $requestURI);

    $this->assertEquals($mappings, ['user' => 51]);
  }

  public function testThatAbleToGetVariablesThatArentAdjacent()
  {
    $URI = '/:user/ban/:timeout';
    $requestURI = '/69/ban/60';
    $mappings = Route::getVariablesFromRequest($URI, $requestURI);

    $this->assertEquals($mappings, ['user' => 69, 'timeout' => 60]);
  }

  public function testThatOnMisMatchRouteReturnNull()
  {
    $URI = '/ban/:user/:timeout';
    $requestURI = '/unban/69';
    $mappings = Route::getVariablesFromRequest($URI, $requestURI);

    $this->assertEquals($mappings, null);
  }

  public function testForMatchingStaticRoutes()
  {
    $routes = [
      ['/update/:user'],
      ['/ban/:user/:timeout'],
      ['/unban/:user'],
      ['/'],
    ];

    $requestURI = '/';
    $matchingRoute = Route::getMatchingRoute($routes, $requestURI);

    $this->assertEquals($matchingRoute, ['/']);
  }

  public function testForMatchingDynamicRoutes()
  {
    $routes = [
      ['/update/:user'],
      ['/ban/:user/:timeout'],
      ['/unban/:user']
    ];

    $requestURIs = [
      '/update/8',
      '/ban/31/60',
      '/unban/31'
    ];

    foreach ($requestURIs as $key => $requestURI) {
      $matchingRoute = Route::getMatchingRoute($routes, $requestURI);
      $this->assertEquals($matchingRoute, $routes[$key]);
    }
  }
}
