<?php

namespace App\Models;

use CodeIgniter\Database\Exceptions\DatabaseException;
use App\Models\PrimordialModel;
use App\Models\Entidades\Usuario;

final class CadastreSeModel extends PrimordialModel{

  public function __construct(){
    parent::__construct();
  }

  public function verifica_disponibilidade_de_email($email){
    $sql = <<<'MySQL'
SELECT email FROM usuario WHERE email=?
MySQL;

    $query = $this->get_banco_de_dados()->query($sql, $email);
    $array_resultado = $query->getResult('array');

    if(count($array_resultado) > 0){
      $mensagem_do_model = 'O e-mail escolhido já foi utilizado em outro cadastro de usuário.';
      $mensagem_do_model .= ' Por favor, utilize outro e-mail no momento do cadastro.';
      $array_resultado['mensagem_do_model'] = $mensagem_do_model;
    }

    return $array_resultado;
  }

  public function verifica_disponibilidade_de_nome_de_usuario($nome_de_usuario){
    $sql = <<<'MySQL'
SELECT nome_de_usuario
FROM usuario
WHERE REPLACE(REPLACE(REPLACE(nome_de_usuario, '_', ''), '-', ''), '.', '') = 
REPLACE(REPLACE(REPLACE(?, '_', ''), '-', ''), '.', '')
MySQL;

    $query = $this->get_banco_de_dados()->query($sql, $nome_de_usuario);
    $array_resultado = $query->getResult('array');

    if(count($array_resultado) === 1){
      $usuario_encontrado = new Usuario($array_resultado[0]);
      if($usuario_encontrado->get_nome_de_usuario() === $nome_de_usuario){
        $mensagem_do_model = 'O nome de usuário escolhido já foi utilizado em outro cadastro.';
        $mensagem_do_model .= ' Por favor, escolha outro nome de usuário.';
        $array_resultado['mensagem_do_model'] = $mensagem_do_model;
      }else{
        $nome_similar = $usuario_encontrado->get_nome_de_usuario();
        $mensagem_do_model = 'O nome de usuário escolhido é muito similar a um nome de usuário já';
        $mensagem_do_model .= " cadastrado (nome \"$nome_similar\"). Por favor, escolha outro";
        $mensagem_do_model .= ' nome de usuário.';
        $array_resultado['mensagem_do_model'] = $mensagem_do_model;
      }
    }elseif(count($array_resultado) > 1){
      $mensagem_do_model = 'O nome de usuário escolhido é muito similar a outros nomes de usuários';
      $mensagem_do_model .= ' já cadastrados. Por favor, escolha outro nome de usuário.';
      $array_resultado['mensagem_do_model'] = $mensagem_do_model;
    }

    return $array_resultado;
  }

  public function cadastrar_usuario($usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $insert['nome_de_usuario'] = $usuario->get_nome_de_usuario();
    $insert['email'] = $usuario->get_email();
    $insert['senha'] = $usuario->get_senha();
    $insert['chave_para_operacoes_via_link'] = $usuario->get_chave_para_operacoes_via_link();
    $insert['momento_do_cadastro'] = $usuario->get_momento_do_cadastro();
    $insert['sexo'] = $usuario->get_sexo();

    $array_resultado = array();

    try{
      $builder->insert($insert);
    }catch(DatabaseException $excecao){
      $codigo_da_excecao = $excecao->getCode();
      switch($codigo_da_excecao){
        case 1062:
          $mensagem_do_model = 'Já existe um usuário cadastrado com uma ou mais destas informações.';
          $array_resultado['mensagem_do_model'] = $mensagem_do_model;
          break;
        default:
          $array_resultado['mensagem_do_model'] = $excecao->getMessage();
          break;
      }
    }

    $array_resultado['pk_usuario'] = $this->db->insertID();
    return $array_resultado;
  }

}
