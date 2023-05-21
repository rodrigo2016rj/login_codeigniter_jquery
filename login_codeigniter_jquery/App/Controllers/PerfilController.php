<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use App\Models\Entidades\Usuario;

final class PerfilController extends TemplateController{

  public function index($redirecionar_com_id = false){
    if($redirecionar_com_id === 'pagina_inicial'){
      //Redireciona para a página inicial caso seja necessário.
      header('Location: /pagina_inicial');
      die;
    }elseif($redirecionar_com_id !== false){
      //Redireciona para si mesmo, motivo: limpar a requisição.
      header("Location: /perfil?id=$redirecionar_com_id");
      die;
    }

    /* Especificando a página do sistema para os links e outras tags */
    $this->get_smarty()->assign('pagina_template', 'perfil');

    /* Mostrando mensagem caso exista alguma */
    if($this->get_sessao()->has('mensagem_template')){
      $mensagem_template = $this->get_sessao()->get('mensagem_template');
      $this->get_smarty()->assign('mensagem_template', $mensagem_template);
      $this->get_sessao()->remove('mensagem_template');
    }

    /* Variável que guarda a mensagem da página começa inicialmente vazia */
    $mensagem = '';

    /* Array com as informações do usuário começa inicialmente assim */
    $array_usuario['id'] = '';
    $array_usuario['nome_de_usuario'] = '';
    $array_usuario['email'] = '';
    $array_usuario['momento_do_cadastro'] = '';
    $array_usuario['tipo'] = '';
    $array_usuario['sexo'] = '';
    $array_usuario['exibir_sexo_no_perfil'] = '';
    $array_usuario['exibir_email_no_perfil'] = '';
    $this->get_smarty()->assign('usuario', $array_usuario);

    /* $mostrar_link_editar_tipo_de_usuario a princípio é true */
    $mostrar_link_editar_tipo_de_usuario = true;

    $perfil_model = new PerfilModel();

    /* Validando o ID de usuário informado na URL */
    $requisicao = service('request');
    $pk_usuario = $requisicao->getGet('id');
    $usuario = new Usuario();
    if(!is_numeric($pk_usuario) or $pk_usuario <= 0 or floor($pk_usuario) != $pk_usuario){
      $mensagem = 'ID inválido, o ID do usuário precisa ser um número natural maior que zero.';
      $mostrar_link_editar_tipo_de_usuario = false;
    }else{
      $this->get_smarty()->assign('id_referencia_template', $pk_usuario);

      /* Consultando e mostrando informações do usuário */
      $array_resultado = $perfil_model->selecionar_usuario($pk_usuario);
      if(isset($array_resultado['mensagem_do_model'])){
        $mensagem = $array_resultado['mensagem_do_model'];
        $mostrar_link_editar_tipo_de_usuario = false;
      }else{
        $usuario = $array_resultado[0];

        $nome_de_usuario = $usuario->get_nome_de_usuario();
        $array_usuario['nome_de_usuario'] = esc($nome_de_usuario);

        $id = $usuario->get_pk_usuario();
        $array_usuario['id'] = $id;

        $tipo = $usuario->get_tipo();
        $array_usuario['tipo'] = $tipo;

        $array_usuario['exibir_sexo_no_perfil'] = false;
        if($usuario->get_exibir_sexo_no_perfil() == 'sim'){
          $array_usuario['exibir_sexo_no_perfil'] = true;
        }

        $sexo = $usuario->get_sexo();
        $array_usuario['sexo'] = $sexo;

        $array_usuario['exibir_email_no_perfil'] = false;
        if($usuario->get_exibir_email_no_perfil() == 'sim'){
          $array_usuario['exibir_email_no_perfil'] = true;
        }

        $email = $usuario->get_email();
        $array_usuario['email'] = esc($email);

        $momento_do_cadastro = $usuario->get_momento_do_cadastro();
        $momento_do_cadastro = $this->colocar_no_fuso_horario_do_usuario_logado($momento_do_cadastro);
        $momento_do_cadastro = $this->converter_para_horario_data_do_html($momento_do_cadastro);
        $array_usuario['momento_do_cadastro'] = $momento_do_cadastro;

        $this->get_smarty()->assign('usuario', $array_usuario);
      }
    }

    /* Verificando se o usuário está logado */
    if($this->get_usuario_logado() === null){
      $mensagem = 'Você precisa entrar para poder acessar esta página.';
    }else{
      /* Verificando se o usuário logado pode editar o tipo do outro usuário */
      $tipo_do_usuario_logado = $this->get_usuario_logado()->get_tipo();
      $tipo_do_usuario_alvo = $usuario->get_tipo();

      $niveis = $usuario->niveis_dos_tipos_de_usuario();
      $nivel_do_tipo_do_usuario_logado = 0;
      $nivel_do_tipo_do_usuario_alvo = 0;
      foreach($niveis as $chave => $valor){
        if(in_array($tipo_do_usuario_logado, $valor)){
          $nivel_do_tipo_do_usuario_logado = $chave;
        }
        if(in_array($tipo_do_usuario_alvo, $valor)){
          $nivel_do_tipo_do_usuario_alvo = $chave;
        }
      }

      if($nivel_do_tipo_do_usuario_logado <= $nivel_do_tipo_do_usuario_alvo ||
        $nivel_do_tipo_do_usuario_logado <= 1){
        $mostrar_link_editar_tipo_de_usuario = false;
      }
    }

    /* Se houver mensagem na sessão, deve ser mostrada */
    if($this->get_sessao()->has('mensagem_da_pagina_perfil')){
      $mensagem = $this->get_sessao()->get('mensagem_da_pagina_perfil');
      $this->get_sessao()->remove('mensagem_da_pagina_perfil');
    }

    $this->get_smarty()->assign('mostrar_link_editar_tipo_de_usuario', $mostrar_link_editar_tipo_de_usuario);
    $this->get_smarty()->assign('mensagem_da_pagina', $mensagem);
    $this->get_smarty()->display('perfil/perfil.html');
    die;
  }

  public function entrar(){
    $this->entrar_padronizado();
    $requisicao = service('request');
    $pk_usuario = $requisicao->getGet('id');
    if(is_numeric($pk_usuario) and $pk_usuario > 0 and floor($pk_usuario) == $pk_usuario){
      $this->index($pk_usuario);
    }else{
      $this->index('pagina_inicial');
    }
  }

  public function sair(){
    $this->sair_padronizado();
    $requisicao = service('request');
    $pk_usuario = $requisicao->getGet('id');
    if(is_numeric($pk_usuario) and $pk_usuario > 0 and floor($pk_usuario) == $pk_usuario){
      $this->index($pk_usuario);
    }else{
      $this->index('pagina_inicial');
    }
  }

}
