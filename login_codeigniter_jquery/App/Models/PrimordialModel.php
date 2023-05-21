<?php

namespace App\Models;

use CodeIgniter\Model;

class PrimordialModel extends Model{
  private $banco_de_dados;

  public function __construct(){
    parent::__construct();

    $this->banco_de_dados = \Config\Database::connect();
  }

  protected final function get_banco_de_dados(){
    return $this->banco_de_dados;
  }

}
