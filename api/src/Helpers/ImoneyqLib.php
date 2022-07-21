<?php

namespace App\Helpers;

use App\Model\Connection\DB;

class ImoneyqLib
{
  private $db;

  public function __construct(
    DB $db
  ) {
    $this->status = 200;
    $this->db = $db;
  }

  public function getIDAmil($key_auth)
  {
    $db = $this->db;

    $stmt = $db->prepare('SELECT * FROM imoneyq_key WHERE key_auth = :key_auth');
    $stmt->execute(array(
      'key_auth' => $key_auth
    ));
    $cek = $stmt->rowCount();

    if ($cek > 0) {
      $data = $stmt->fetch();
      return $data["idamil"];
    } else {
      return 0;
    }
  }
}
