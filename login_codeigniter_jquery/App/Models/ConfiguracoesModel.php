<?php

namespace App\Models;

use App\Models\PrimordialModel;
use App\Models\Entidades\Usuario;

final class ConfiguracoesModel extends PrimordialModel{

  public function __construct(){
    parent::__construct();
  }

  public function selecionar_usuario($pk_usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $builder->select('pk_usuario, visual, fuso_horario, nome_de_usuario, exibir_sexo_no_perfil, 
exibir_email_no_perfil');
    $builder->where('pk_usuario =', $pk_usuario);

    $query = $builder->get();
    $array_resultado = $query->getResult('array');

    if(count($array_resultado) === 0){
      $mensagem_do_model = 'Este usuário não se encontra mais no banco de dados do sistema.';
      $array_resultado['mensagem_do_model'] = $mensagem_do_model;
    }else{
      $usuario = new Usuario($array_resultado[0]);
      $array_melhorado[] = $usuario;
      $array_resultado = $array_melhorado;
    }

    return $array_resultado;
  }

  public function salvar_fuso_horario($fuso_horario, $pk_usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $update['fuso_horario'] = $fuso_horario;
    $builder->where('pk_usuario =', $pk_usuario);

    $builder->update($update);

    $array_resultado = array();
    return $array_resultado;
  }

  public function salvar_visual($visual, $pk_usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $update['visual'] = $visual;
    $builder->where('pk_usuario =', $pk_usuario);

    $builder->update($update);

    $array_resultado = array();
    return $array_resultado;
  }

  public function verifica_disponibilidade_de_nome_de_usuario($nome_de_usuario, $pk_usuario){
    $sql = <<<'MySQL'
SELECT nome_de_usuario
FROM usuario
WHERE REPLACE(REPLACE(REPLACE(nome_de_usuario, '_', ''), '-', ''), '.', '') = 
REPLACE(REPLACE(REPLACE(?, '_', ''), '-', ''), '.', '') 
AND pk_usuario <> ?
MySQL;

    $array_valores[] = $nome_de_usuario;
    $array_valores[] = $pk_usuario;
    $query = $this->get_banco_de_dados()->query($sql, $array_valores);
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

  public function salvar_nome_de_usuario($nome_de_usuario, $pk_usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $update['nome_de_usuario'] = $nome_de_usuario;
    $builder->where('pk_usuario =', $pk_usuario);

    $array_resultado = array();

    try{
      $builder->update($update);
    }catch(\Exception $excecao){
      $codigo_da_excecao = $excecao->getCode();
      switch($codigo_da_excecao){
        case 1062:
          $mensagem_do_model = 'O nome de usuário escolhido já foi utilizado em outro cadastro.';
          $mensagem_do_model .= ' Por favor, escolha outro nome de usuário.';
          $array_resultado['mensagem_do_model'] = $mensagem_do_model;
          break;
        default:
          $array_resultado['mensagem_do_model'] = $excecao->getMessage();
          break;
      }
    }

    return $array_resultado;
  }

  public function salvar_escolha_de_exibicao_de_sexo_no_perfil($opcao_escolhida, $pk_usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $update['exibir_sexo_no_perfil'] = $opcao_escolhida;
    $builder->where('pk_usuario =', $pk_usuario);

    $builder->update($update);

    $array_resultado = array();
    return $array_resultado;
  }

  public function salvar_escolha_de_exibicao_de_email_no_perfil($opcao_escolhida, $pk_usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $update['exibir_email_no_perfil'] = $opcao_escolhida;
    $builder->where('pk_usuario =', $pk_usuario);

    $builder->update($update);

    $array_resultado = array();
    return $array_resultado;
  }

  public function salvar_nova_senha($senha, $pk_usuario){
    $builder = $this->get_banco_de_dados()->table('usuario');
    $update['senha'] = $senha;
    $builder->where('pk_usuario =', $pk_usuario);

    $builder->update($update);

    $array_resultado = array();
    return $array_resultado;
  }

}
