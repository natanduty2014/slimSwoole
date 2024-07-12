<?php

namespace App\middleware;

use Lib\slim\getParsedBody as getParsedBody;
use Slim\Psr7\Response as slimReponse;
use Psr\Http\Message\ResponseInterface as ResponseSlim;
use Psr\Http\Message\ServerRequestInterface as RequestSlim;
use Psr\Http\Server\RequestHandlerInterface as RequestHandlerSlim;
use Lib\jwt\jwt;

class permission
{

    static public function authorization(RequestSlim $request, RequestHandlerSlim $handler): ResponseSlim
    {
        try{
            $VerifyToken = jwt::verifyToken($request->getHeader('authorization')[0] ?? null);
            if(!$VerifyToken){
                throw new \Exception ('Token inválido');
            }
            $token = jwt::decodeToken($request->getHeader('authorization')[0]);
            $user_permissions = $token->user_permissions;
            $uri = \explode('/', $request->getUri()->getPath())[3];
            $typeReq = $request->getMethod();
            foreach($user_permissions as $permission => $value){
                if($value->route->value == $uri){
                    foreach($value->actions as $action => $value2){
                        switch($value2) {
                            case 'read':
                                if($typeReq == 'GET'){
                                    return $handler->handle($request);
                                }
                                break;
                            case 'edit':
                                if($typeReq == 'PUT'){
                                    return $handler->handle($request);
                                }
                                break;
                            case 'remove':
                                if($typeReq == 'DELETE'){
                                    return $handler->handle($request);
                                }
                                break;
                            case 'insert':
                                if($typeReq == 'POST'){
                                    return $handler->handle($request);
                                }
                                break;
                        }
                    }
                }
            }
            \var_dump('sem permissão');
            $response = new slimReponse();
            $response->getBody()->write(json_encode(['error' => 'not_permission']));
            $response->withStatus(401);
            return $response;
        }catch(\Exception $e){
            $response = new slimReponse();
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            $response->withStatus(500);
            return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type')
            ->withHeader('Authorization', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
}