<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as ResponseSlim;
use Psr\Http\Message\ServerRequestInterface as RequestSlim;
use Lib\slim\getParsedBody as getParsedBody;
use App\Models\swoole as swooleModel;

class swoole
{


    public ?array $data = array(
        "nome" => "string",
        "idade" => 0,
        "ativo" => true
    );
    public ?array $pagination = array(
        "pag" => 1,
        "limit" => 10
    );


    static public function create(RequestSlim $request, ResponseSlim $response, $args, $swooleServer): ResponseSlim
    {
        $getParsedBody = new getParsedBody;
        try {
            $data = $getParsedBody
                ->nullParams($_POST)
                ->filter($_POST)
                ->JsonToArray($_POST)
                ->validInputEmpty('nome')
                ->removeEmptySpaces('nome')
                ->isStringInput('nome')
                ->strlen('nome', 255)
                ->getData();

            $result = swooleModel::create($data);
            if ($result['status'] == 201) {
                $response = $response->withStatus(201);
                $response = $response->withHeader('Location', 'http://localhost:9999/swoole/' . $result['id']);
            } else {
                $response = $response->withStatus(400);
            }
            return $response;
        } catch (\Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(422)
                ->withHeader('Content-Type', 'application/json');
        }
    }

    static public function searchID(RequestSlim $request, ResponseSlim $response, $args): ResponseSlim
    {
        $result = swooleModel::searchID($args['id']);
        $response->getBody()->write(json_encode($result));
        return $response->withStatus($result['status'])->withHeader('Content-Type', 'application/json');
    }

    static public function listAll(RequestSlim $request, ResponseSlim $response, $args): ResponseSlim
    {
        $pag = $args['pag'] ?? 1;
        $result = swooleModel::listAll($pag);
        $response->getBody()->write(json_encode($result));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    static public function delete(RequestSlim $request, ResponseSlim $response, $args): ResponseSlim
    {
        $result = swooleModel::deleted($args['id']);
            $response->getBody()->write(json_encode($result['message']));
            return $response->withStatus($result['status'])->withHeader('Content-Type', 'application/json');
    }
}