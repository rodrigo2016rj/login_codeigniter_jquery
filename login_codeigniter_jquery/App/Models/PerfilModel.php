<?php

namespace App\Models;

use App\Models\PrimordialModel;
use App\Models\Entidades\Usuario;

final class PerfilModel extends PrimordialModel{

  public function __construct(){
    parent::__construct();
  }

  public function selecionar_usuario($pk_usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $builder->select('pk_usuario, nome_de_usuario, email, momento_do_cadastro, tipo, sexo, 
exibir_sexo_no_perfil, exibir_email_no_perfil');
    $builder->where('pk_usuario =', $pk_usuario);

    $query = $builder->get();
    $array_resultado = $query->getResult('array');

    if(count($array_resultado) === 0){
      $mensagem_do_model = "Nenhum usuário com ID $pk_usuario foi encontrado no banco de dados";
      $mensagem_do_model .= ' do sistema.';
      $array_resultado['mensagem_do_model'] = $mensagem_do_model;
    }else{
      $usuario = new Usuario($array_resultado[0]);
      $array_melhorado[] = $usuario;
      $array_resultado = $array_melhorado;
    }

    return $array_resultado;
  }

}
