<?php

declare(strict_types=1);

namespace App\Routes\Home;

use App\Model\Connection\DB;

class Home
{
    private $status;
    private $db;

    public function __construct(
        DB $db
    ) {
        $this->status = 200;
        $this->db = $db;
    }

    public function __invoke($request, $response, $args)
    {
        $callback['success'] = false;
        $callback['msg'] = '404 - NOT FOUND';
        $response->getBody()->write(json_encode($callback));
        return $response->withStatus($this->status);
    }

    public function Homeget($request, $response, $args)
    {
        try {
            $param = $request->getParsedBody();
            $db = $this->db;

            $stmt = $db->prepare('SELECT * FROM user');
            $stmt->execute();
            $data = $stmt->fetchAll();

            $callback['success'] = true;
            $callback['data'] = $data;
        } catch (Exception $err) {
            $callback['success'] = false;
            $callback['msg'] = $err;
        }
        $response->getBody()->write(json_encode($callback));
        return $response->withStatus($this->status);
    }

    public function Homeadd($request, $response, $args)
    {
        try {
            $param = $request->getParsedBody();
            $db = $this->db;

            $stmt = $db->prepare('INSERT INTO user (nama, nomor) VALUES (:nama, :nomor)');
            $stmt->execute(array(
                'nama' => $param['nama'],
                'nomor' => $param['nomor'],
            ));

            $callback['success'] = true;
            $callback['msg'] = 'Sukses menambah data';
        } catch (Exception $err) {
            $callback['success'] = false;
            $callback['msg'] = $err;
        }
        $response->getBody()->write(json_encode($callback));
        return $response->withStatus($this->status);
    }

    public function Homeedit($request, $response, $args)
    {
        try {
            $param = $request->getParsedBody();
            $db = $this->db;

            $stmt = $db->prepare('UPDATE user SET nama = :nama, nomor = :nomor 
            WHERE iduser = :iduser');
            $stmt->execute(array(
                'nama'      => $param['nama'],
                'nomor'           => $param['nomor'],
                'iduser' => $param['iduser']
            ));

            $callback['success'] = true;
            $callback['msg'] = 'Sukses Mengubah Data';
        } catch (Exception $err) {
            $callback['success'] = false;
            $callback['msg'] = $err;
        }
        $response->getBody()->write(json_encode($callback));
        return $response->withStatus($this->status);
    }

    public function Homedelete($request, $response, $args)
    {
        try {
            $param = $request->getParsedBody();
            $db = $this->db;

            $stmt = $db->prepare('DELETE FROM user WHERE iduser = :iduser');
            $stmt->execute(array(
                'iduser'      => $param['iduser'],
            ));

            $callback['success'] = true;
            $callback['msg'] = 'Sukses Menghapus Data';
        } catch (Exception $err) {
            $callback['success'] = false;
            $callback['msg'] = $err;
        }
        $response->getBody()->write(json_encode($callback));
        return $response->withStatus($this->status);
    }
}
