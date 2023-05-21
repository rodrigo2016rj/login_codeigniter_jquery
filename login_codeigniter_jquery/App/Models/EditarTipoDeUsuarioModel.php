<?php

namespace App\Models;

use App\Models\PrimordialModel;
use App\Models\Entidades\Usuario;

final class EditarTipoDeUsuarioModel extends PrimordialModel{

  public function __construct(){
    parent::__construct();
  }

  public function selecionar_usuario($pk_usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $builder->select('pk_usuario, nome_de_usuario, tipo');
    $builder->where('pk_usuario =', $pk_usuario);

    $query = $builder->get();
    $array_resultado = $query->getResult('array');

    if(count($array_resultado) === 0){
      $mensagem_do_model = "Nenhum usuário com ID $pk_usuario foi encontrado no banco de dados";
      $mensagem_do_model .= ' do sistema.';
      $array_resultado['mensagem_do_model'] = $mensagem_do_model;
    }else{
      $array_melhorado = array();
      foreach($array_resultado as $array_usuario){
        $usuario = new Usuario($array_usuario);
        $array_melhorado[] = $usuario;
      }
      $array_resultado = $array_melhorado;
    }

    return $array_resultado;
  }

  public function editar_tipo_de_usuario($usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $update['tipo'] = $usuario->get_tipo();
    $pk_usuario = $usuario->get_pk_usuario();
    $builder->where('pk_usuario =', $pk_usuario);

    $builder->update($update);

    $array_resultado = array();
    return $array_resultado;
  }

}
