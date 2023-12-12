<?php

namespace App\Models;

use App\Models\PrimordialModel;
use App\Models\Entidades\Usuario;

final class PaginaInicialModel extends PrimordialModel{

  public function __construct(){
    parent::__construct();
  }

  public function selecionar_usuario($pk_usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $builder->select('pk_usuario, conta_confirmada, chave_para_operacoes_via_link');
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

  public function confirmar_conta($usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $update['conta_confirmada'] = 'sim';
    $update['chave_para_operacoes_via_link'] = '';

    $pk_usuario = $usuario->get_pk_usuario();
    $chave_para_operacoes_via_link = $usuario->get_chave_para_operacoes_via_link();
    $builder->where('pk_usuario =', $pk_usuario);
    $builder->where('chave_para_operacoes_via_link =', $chave_para_operacoes_via_link);

    $builder->update($update);

    $array_resultado = array();
    return $array_resultado;
  }

}
