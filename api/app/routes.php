<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

use App\Routes\Home\Home;

return function (App $app) {
  $app->options('/{routes:.*}', function (Request $request, Response $response) {
    // CORS Pre-Flight OPTIONS Request Handler
    return $response;
  });

  $app->get('/', function (Request $request, Response $response) {
    $d = [
      'success' => false,
      'msg' => '404 - NOT FOUND'

    ];
    $response->getBody()->write(json_encode($d));
    return $response;
  });

  $app->group('/home', function (Group $group) {
    $group->get('', Home::class);

    $group->get('/get', Home::class . ':Homeget');
    $group->post('/add', Home::class . ':Homeadd');
    $group->post('/edit', Home::class . ':Homeedit');
    $group->post('/delete', Home::class . ':Homedelete');
  });
};
