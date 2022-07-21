<?php

namespace App\Helpers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\App;
use App\Model\Connection\DB;

class AuthMiddleware
{

    private $db;

    public function __construct(App $app)
    {
        $this->db = $app->getContainer()->get(DB::class);
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $token = $request->getHeaderLine('Authorization');
        $isMaintenace = false; //

        if (isset($token)) {
            if ($token != '' && $token != 'null') {
                $jwt = explode('.', $token);
                $header_encode = $jwt[0];
                $payload_encode = $jwt[1];
                $signature_encode = $jwt[2];

                $header = json_decode(base64_decode($jwt[0]));
                $payload = json_decode(base64_decode($jwt[1]));
                if (isset($payload->id)) {
                    $sql = 'SELECT * FROM user WHERE iduser=:iduser';
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute(array(':iduser' => $payload->id));

                    if ($stmt->rowCount() > 0) {
                        $result = $stmt->fetch();


                        // $key = $result['session'];
                        $password = $result['auth'];
                        $signature = hash_hmac('sha256', $header_encode . '.' . $payload_encode, $password, true);
                        $signature = base64_encode($signature);

                        if ($signature == $signature_encode) {
                            if (!$isMaintenace) {
                                //SUCCESS
                                $response = $handler->handle($request);
                            } else {
                                //BLOCK SEMENTARA
                                $error['success'] = false;
                                $error['msg'] = 'System still maintenace';

                                $responseFactory = new ResponseFactory();
                                $response = $responseFactory->createResponse();
                                $response->getBody()->write(json_encode($error));
                                $response->withStatus(500);
                            }
                        } else {
                            $error['success'] = false;
                            $error['msg'] = 'Invalid key';

                            $responseFactory = new ResponseFactory();
                            $response = $responseFactory->createResponse();
                            $response->getBody()->write(json_encode($error));
                            $response->withStatus(401);
                        }
                    } else {
                        $error['success'] = false;
                        $error['msg'] = 'Unauthorized user';

                        $responseFactory = new ResponseFactory();
                        $response = $responseFactory->createResponse();
                        $response->getBody()->write(json_encode($error));
                        $response->withStatus(401);
                    }
                } else {
                    $error['success'] = false;
                    $error['msg'] = 'Invalid API Key and ID';

                    $responseFactory = new ResponseFactory();
                    $response = $responseFactory->createResponse();
                    $response->getBody()->write(json_encode($error));
                    $response->withStatus(401);
                }
            } else {
                $error['success'] = false;
                $error['msg'] = 'API Key required';

                $responseFactory = new ResponseFactory();
                $response = $responseFactory->createResponse();
                $response->getBody()->write(json_encode($error));
                $response->withStatus(401);
            }
        } else {
            $error['success'] = false;
            $error['msg'] = 'API Key required';

            $responseFactory = new ResponseFactory();
            $response = $responseFactory->createResponse();
            $response->getBody()->write(json_encode($error));
            $response->withStatus(401);
        }

        return $response;
    }
}
