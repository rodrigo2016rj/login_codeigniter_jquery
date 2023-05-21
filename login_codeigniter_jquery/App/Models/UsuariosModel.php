<?php

namespace App\Models;

use App\Models\PrimordialModel;
use App\Models\Entidades\Usuario;

final class UsuariosModel extends PrimordialModel{

  public function __construct(){
    parent::__construct();
  }

  public function selecionar_usuarios($filtros, $ordenacao, $quantidade, $descartar){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $builder->select('pk_usuario, nome_de_usuario, email, momento_do_cadastro, tipo');

    if(isset($filtros['where'])){
      $builder->where($filtros['where']);
    }
    if(isset($filtros['like'])){
      $builder->like($filtros['like']);
    }

    switch($ordenacao){
      case 'padrao':
        $builder->orderBy('pk_usuario', 'DESC');
        break;
      case 'nome_de_usuario_em_ordem_alfabetica':
        $builder->orderBy('nome_de_usuario', 'ASC');
        break;
      case 'nome_de_usuario_em_ordem_alfabetica_inversa':
        $builder->orderBy('nome_de_usuario', 'DESC');
        break;
      case 'email_em_ordem_alfabetica':
        $builder->orderBy('email', 'ASC');
        break;
      case 'email_em_ordem_alfabetica_inversa':
        $builder->orderBy('email', 'DESC');
        break;
      case 'momento_do_cadastro_em_ordem_cronologica':
        $builder->orderBy('momento_do_cadastro', 'ASC');
        $builder->orderBy('pk_usuario', 'DESC');
        break;
      case 'momento_do_cadastro_em_ordem_cronologica_inversa':
        $builder->orderBy('momento_do_cadastro', 'DESC');
        $builder->orderBy('pk_usuario', 'DESC');
        break;
      case 'tipo_em_ordem_alfabetica':
        $builder->orderBy('CAST(tipo AS CHAR)', 'ASC');
        $builder->orderBy('pk_usuario', 'DESC');
        break;
      case 'tipo_em_ordem_alfabetica_inversa':
        $builder->orderBy('CAST(tipo AS CHAR)', 'DESC');
        $builder->orderBy('pk_usuario', 'DESC');
        break;
    }

    $builder->limit($quantidade, $descartar);

    $query = $builder->get();
    $array_resultado = $query->getResult('array');

    $array_melhorado = array();
    foreach($array_resultado as $array_usuario){
      $usuario = new Usuario($array_usuario);
      $array_melhorado[] = $usuario;
    }
    $array_resultado = $array_melhorado;

    return $array_resultado;
  }

}
