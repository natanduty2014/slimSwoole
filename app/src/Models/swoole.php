<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Lib\database\eloquent\config;
use Ramsey\Uuid\Uuid;
use Lib\database\redis\redis;

/*
CREATE TABLE swoole (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
);

*/

class swoole extends Model
{
    protected $table = 'swoole';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    static public function create($data) : array
    {
        try {
            $capsule = config::conn();
            $conection = $capsule->getConnection();
            $conection->beginTransaction();

            $swoole = new swoole();
            $swoole->nome = $data['nome'];
            $swoole->save();
            $conection->commit();
            return (array('status' => 201, 'id' => $swoole->id));
        } catch (\Throwable $th) {
            var_dump($th);
            return (array('status' => 500));
        }
    }

    static public function searchID($id): array
    {
        config::conn();
        $swoole = swoole::where('id', $id)->first();
        if (!$swoole) {
            return (array('status' => 404, 'message' => 'Registro não encontrado'));
        }

        return (array('status' => 200, 'data' => $swoole));
    }


    static public function listall() : array
    {
        config::conn();
        $swoole = swoole::all();
        return (array('status' => 200, 'data' => $swoole));
    }

    static public function deleted($id) : array
    {
        config::conn();
        $swoole = swoole::where('id', $id)->first();
        if (!$swoole) {
            return (array('status' => 404, 'message' => 'Registro não encontrado'));
        }
        $swoole->delete();
        return (array('status' => 200, 'message' => 'Registro deletado com sucesso'));
    }
}


