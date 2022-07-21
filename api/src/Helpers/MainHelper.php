<?php

namespace App\Helpers;

use App\Model\Connection\DB;

class MainHelper
{

    private $status;
    private $db;

    public function __construct(
        DB $db
    ) {
        $this->status = 200;
        $this->db = $db;
    }

    public function getUserID($request)
    {
        $token = $request->getHeaderLine('Authorization');
        if (isset($token)) {
            $jwt = explode('.', $token);
            $header_encode = $jwt[0];
            $payload_encode = $jwt[1];
            $signature_encode = $jwt[2];

            $payload = json_decode(base64_decode($jwt[1]));
            if (isset($payload->id)) {
                return $payload->id;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function getUserLevel($iduser)
    {
        $stmt = $this->db->prepare('SELECT * FROM user WHERE iduser=:iduser');
        $stmt->execute(array('iduser' => $iduser));

        if ($stmt->rowCount() > 0) {
            $res = $stmt->fetch();
            return $res['level'];
        } else {
            return '';
        }
    }

    public function getUserKokab($iduser)
    {
        $stmt = $this->db->prepare('SELECT * FROM user_kokab WHERE iduser=:iduser');
        $stmt->execute(array('iduser' => $iduser));

        if ($stmt->rowCount() > 0) {
            $res = $stmt->fetch();
            return $res['kode_kokab'];
        } else {
            return '';
        }
    }

    public function getUserKlaster($iduser)
    {
        $stmt = $this->db->prepare('SELECT * FROM user_kluster WHERE iduser=:iduser');
        $stmt->execute(array('iduser' => $iduser));

        if ($stmt->rowCount() > 0) {
            $res = $stmt->fetch();
            return $res['idkluster'];
        } else {
            return '';
        }
    }

    public function getKlaster2Kokab($idkluster)
    {
        $stmt = $this->db->prepare('SELECT * FROM kluster WHERE idkluster=:idkluster');
        $stmt->execute(array('idkluster' => $idkluster));

        if ($stmt->rowCount() > 0) {
            $res = $stmt->fetch();
            return $res['kode_kokab'];
        } else {
            return '';
        }
    }

    public function getUser2Laz($iduser)
    {
        $stmt = $this->db->prepare('SELECT * FROM user_laz WHERE iduser=:iduser');
        $stmt->execute(array('iduser' => $iduser));

        if ($stmt->rowCount() > 0) {
            $res = $stmt->fetch();
            return $res['kode_laz'];
        } else {
            return '';
        }
    }

    public function Encrypt($plain_text, $passphrase)
    {
        $salt = openssl_random_pseudo_bytes(256);
        $iv = openssl_random_pseudo_bytes(16);
        //on PHP7 can use random_bytes() istead openssl_random_pseudo_bytes()
        //or PHP5x see : https://github.com/paragonie/random_compat

        $iterations = 100;
        $key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);

        $encrypted_data = openssl_encrypt($plain_text, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);

        $data = array("ciphertext" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "salt" => bin2hex($salt));
        return json_encode($data);
    }

    public function Decrypt($jsonString, $passphrase)
    {
        $jsondata = json_decode($jsonString, true);

        try {
            $salt = hex2bin($jsondata["salt"]);
            $iv  = hex2bin($jsondata["iv"]);
        } catch (Exception $e) {
            return null;
        }

        $ciphertext = base64_decode($jsondata["ciphertext"]);
        $iterations = 100; //same as js encrypting

        $key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);

        $decrypted = openssl_decrypt($ciphertext, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);

        return $decrypted;
    }
}
