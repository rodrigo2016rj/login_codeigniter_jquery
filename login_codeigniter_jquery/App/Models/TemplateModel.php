<?php

namespace App\Models;

use App\Models\PrimordialModel;
use App\Models\Entidades\Usuario;

final class TemplateModel extends PrimordialModel{

  public function __construct(){
    parent::__construct();
  }

  public function seleciona_usuario_pelo_nome_de_usuario($nome_de_usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $builder->where('nome_de_usuario =', $nome_de_usuario);

    $query = $builder->get();
    $array_resultado = $query->getResult('array');

    if(count($array_resultado) === 0){
      $mensagem_do_model = "O usuário \"$nome_de_usuario\" não foi encontrado no";
      $mensagem_do_model .= ' banco de dados do sistema.';
      $array_resultado['mensagem_do_model'] = $mensagem_do_model;
    }else{
      $array_resultado[0] = new Usuario($array_resultado[0]);
    }

    return $array_resultado;
  }

  public function seleciona_senha_do_usuario_pelo_nome_de_usuario($nome_de_usuario){
    $sql = <<<'MySQL'
SELECT senha FROM usuario WHERE nome_de_usuario=?
MySQL;

    $query = $this->get_banco_de_dados()->query($sql, $nome_de_usuario);
    $array_resultado = $query->getResult('array');

    if(count($array_resultado) === 0){
      $mensagem_do_model = 'Não existe um usuário cadastrado com esse nome de usuário.';
      $array_resultado['mensagem_do_model'] = $mensagem_do_model;
    }else{
      $array_resultado[0] = new Usuario($array_resultado[0]);
    }

    return $array_resultado;
  }

}
