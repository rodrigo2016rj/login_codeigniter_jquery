<?php

namespace App\Models\Entidades;

final class Usuario{
  private $pk_usuario;
  private $nome_de_usuario;
  private $email;
  private $senha;
  private $momento_do_cadastro;
  private $fuso_horario;
  private $visual;
  private $tipo;
  private $sexo;
  private $exibir_sexo_no_perfil;
  private $exibir_email_no_perfil;

  public function __construct($array_usuario = array()){
    if(isset($array_usuario['pk_usuario'])){
      $this->pk_usuario = $array_usuario['pk_usuario'];
    }
    if(isset($array_usuario['nome_de_usuario'])){
      $this->nome_de_usuario = $array_usuario['nome_de_usuario'];
    }
    if(isset($array_usuario['email'])){
      $this->email = $array_usuario['email'];
    }
    if(isset($array_usuario['senha'])){
      $this->senha = $array_usuario['senha'];
    }
    if(isset($array_usuario['momento_do_cadastro'])){
      $this->momento_do_cadastro = $array_usuario['momento_do_cadastro'];
    }
    if(isset($array_usuario['fuso_horario'])){
      $this->fuso_horario = $array_usuario['fuso_horario'];
    }
    if(isset($array_usuario['visual'])){
      $this->visual = $array_usuario['visual'];
    }
    if(isset($array_usuario['tipo'])){
      $this->tipo = $array_usuario['tipo'];
    }
    if(isset($array_usuario['sexo'])){
      $this->sexo = $array_usuario['sexo'];
    }
    if(isset($array_usuario['exibir_sexo_no_perfil'])){
      $this->exibir_sexo_no_perfil = $array_usuario['exibir_sexo_no_perfil'];
    }
    if(isset($array_usuario['exibir_email_no_perfil'])){
      $this->exibir_email_no_perfil = $array_usuario['exibir_email_no_perfil'];
    }
  }

  public function set_pk_usuario($pk_usuario){
    $this->pk_usuario = $pk_usuario;
  }

  public function set_nome_de_usuario($nome_de_usuario){
    $this->nome_de_usuario = $nome_de_usuario;
  }

  public function set_email($email){
    $this->email = $email;
  }

  public function set_senha($senha){
    $this->senha = $senha;
  }

  public function set_momento_do_cadastro($momento_do_cadastro){
    $this->momento_do_cadastro = $momento_do_cadastro;
  }

  public function set_fuso_horario($fuso_horario){
    $this->fuso_horario = $fuso_horario;
  }

  public function set_visual($visual){
    $this->visual = $visual;
  }

  public function set_tipo($tipo){
    $this->tipo = $tipo;
  }

  public function set_sexo($sexo){
    $this->sexo = $sexo;
  }

  public function set_exibir_sexo_no_perfil($exibir_sexo_no_perfil){
    $this->exibir_sexo_no_perfil = $exibir_sexo_no_perfil;
  }

  public function set_exibir_email_no_perfil($exibir_email_no_perfil){
    $this->exibir_email_no_perfil = $exibir_email_no_perfil;
  }

  public function get_pk_usuario(){
    return $this->pk_usuario;
  }

  public function get_nome_de_usuario(){
    return $this->nome_de_usuario;
  }

  public function get_email(){
    return $this->email;
  }

  public function get_senha(){
    return $this->senha;
  }

  public function get_momento_do_cadastro(){
    return $this->momento_do_cadastro;
  }

  public function get_fuso_horario(){
    return $this->fuso_horario;
  }

  public function get_visual(){
    return $this->visual;
  }

  public function get_tipo(){
    return $this->tipo;
  }

  public function get_sexo(){
    return $this->sexo;
  }

  public function get_exibir_sexo_no_perfil(){
    return $this->exibir_sexo_no_perfil;
  }

  public function get_exibir_email_no_perfil(){
    return $this->exibir_email_no_perfil;
  }

  public function enum_sexo(){
    $array_enum['masculino'] = 'Masculino';
    $array_enum['feminino'] = 'Feminino';
    return $array_enum;
  }

  public function enum_tipo(){
    $array_enum['comum'] = 'Comum';
    $array_enum['moderador'] = 'Moderador';
    $array_enum['administrador'] = 'Administrador';
    $array_enum['dono'] = 'Dono';
    return $array_enum;
  }

  public function enum_exibir_sexo_no_perfil(){
    $array_enum['sim'] = 'Sim';
    $array_enum['nao'] = 'Não';
    return $array_enum;
  }

  public function enum_exibir_email_no_perfil(){
    $array_enum['sim'] = 'Sim';
    $array_enum['nao'] = 'Não';
    return $array_enum;
  }

  public function caracteres_permitidos_para_nome_de_usuario(){
    $caracteres_permitidos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_.-';
    return $caracteres_permitidos;
  }

  public function quantidade_minima_de_caracteres($atributo){
    switch($atributo){
      case 'nome_de_usuario':
        return 2;
      case 'email':
        return 6;
      case 'senha':
        return 9;
    }
    return -1;
  }

  // O método abaixo deve ser sempre igual ou mais restritivo que o banco de dados.
  public function quantidade_maxima_de_caracteres($atributo){
    switch($atributo){
      case 'nome_de_usuario':
        return 25;
      case 'email':
        return 160;
      case 'senha':
        return 120;
    }
    return -1;
  }

  public function niveis_dos_tipos_de_usuario(){
    /* Pode ter mais de um tipo por nível */
    $tipos_deste_nivel[] = 'comum';
    $niveis[] = $tipos_deste_nivel;
    unset($tipos_deste_nivel);

    $tipos_deste_nivel[] = 'moderador';
    $niveis[] = $tipos_deste_nivel;
    unset($tipos_deste_nivel);

    $tipos_deste_nivel[] = 'administrador';
    $niveis[] = $tipos_deste_nivel;
    unset($tipos_deste_nivel);

    $tipos_deste_nivel[] = 'dono';
    $niveis[] = $tipos_deste_nivel;
    unset($tipos_deste_nivel);

    return $niveis;
  }

}
