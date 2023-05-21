<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use App\Models\Entidades\Usuario;

final class UsuariosController extends TemplateController{
  private const QUANTIDADE_PADRAO_POR_SEGMENTO = 10;

  public function index($redirecionar = false){
    if($redirecionar){
      //Redireciona para si mesmo, motivo: limpar a requisição.
      header('Location: /usuarios');
      die;
    }

    /* Especificando a página do sistema para os links e outras tags */
    $this->get_smarty()->assign('pagina_template', 'usuarios');

    /* Mostrando mensagem caso exista alguma */
    if($this->get_sessao()->has('mensagem_template')){
      $mensagem_template = $this->get_sessao()->get('mensagem_template');
      $this->get_smarty()->assign('mensagem_template', $mensagem_template);
      $this->get_sessao()->remove('mensagem_template');
    }

    /* Variável que guarda a mensagem da página começa inicialmente vazia */
    $mensagem = '';

    /* Carregando lista de tipos de usuários */
    $usuario = new Usuario();
    $this->get_smarty()->assign('tipos_de_usuario', $usuario->enum_tipo());

    /* Carregando lista de quantidades por segmento */
    $quantidades_por_segmento = $this->criar_array_quantidades_por_segmento();
    $this->get_smarty()->assign('quantidades_por_segmento', $quantidades_por_segmento);

    /* Carregando tabela de usuários */
    $this->mostrar_usuarios();

    /* Verificando se o usuário está logado */
    if($this->get_usuario_logado() === null){
      $mensagem = 'Você precisa entrar para poder acessar esta página.';
    }

    $this->get_smarty()->assign('mensagem_da_pagina', $mensagem);
    $this->get_smarty()->display('usuarios/usuarios.html');
    die;
  }

  public function entrar(){
    $this->entrar_padronizado();
    $this->index(true);
  }

  public function sair(){
    $this->sair_padronizado();
    $this->index(true);
  }

  public function criar_array_quantidades_por_segmento(){
    $quantidades_por_segmento["5"] = 5;
    $quantidades_por_segmento["10"] = 10;
    $quantidades_por_segmento["15"] = 15;
    $quantidades_por_segmento["20"] = 20;
    $quantidades_por_segmento["25"] = 25;
    $quantidades_por_segmento["30"] = 30;
    $quantidades_por_segmento["40"] = 40;
    $quantidades_por_segmento["50"] = 50;
    $quantidades_por_segmento["60"] = 60;
    $quantidades_por_segmento["100"] = 100;
    $quantidades_por_segmento["120"] = 120;

    return $quantidades_por_segmento;
  }

  private function mostrar_usuarios($segmento = 1){
    $usuarios_model = new UsuariosModel();

    $requisicao = service('request');
    $usuario = new Usuario();

    /* Verificando se o sistema irá mostrar os e-mails */
    $mostrar_email = false;
    if($this->get_usuario_logado() !== null){
      $tipo_do_usuario_logado = $this->get_usuario_logado()->get_tipo();
      switch($tipo_do_usuario_logado){
        case "dono":
        case "administrador":
        case "moderador":
          $mostrar_email = true;
          break;
      }
    }
    $this->get_smarty()->assign('mostrar_email', $mostrar_email);

    /* Preparando os filtros */
    $filtros = array();
    $filtro_nome_de_usuario = trim($requisicao->getGet('filtro_nome_de_usuario') ?? '');
    if($filtro_nome_de_usuario !== ''){
      $filtros['like']['nome_de_usuario'] = $filtro_nome_de_usuario;
    }
    $this->get_smarty()->assign('filtro_nome_de_usuario', esc($filtro_nome_de_usuario));

    $filtro_email_do_usuario = trim($requisicao->getGet('filtro_email_do_usuario') ?? '');
    if($filtro_email_do_usuario !== '' && $mostrar_email){
      $filtros['like']['email'] = $filtro_email_do_usuario;
    }
    $this->get_smarty()->assign('filtro_email_do_usuario', esc($filtro_email_do_usuario));

    $filtro_tipo_de_usuario = $requisicao->getGet('filtro_tipo_de_usuario');
    $array_tipos_de_usuario = $usuario->enum_tipo();
    if(in_array($filtro_tipo_de_usuario, array_keys($array_tipos_de_usuario))){
      $filtros['where']['tipo'] = $filtro_tipo_de_usuario;
    }else{
      $filtro_tipo_de_usuario = 'todos';
    }
    $this->get_smarty()->assign('filtro_tipo_de_usuario', esc($filtro_tipo_de_usuario));

    /* Preparando a ordenação */
    $ordenacao = $requisicao->getGet('ordenacao');
    $this->get_smarty()->assign('ordem_do_nome_de_usuario', "Nome de Usuário");
    $this->get_smarty()->assign('ordem_do_email', "E-mail");
    $this->get_smarty()->assign('ordem_do_momento_do_cadastro', "Cadastrado em");
    $this->get_smarty()->assign('ordem_do_tipo', "Tipo");
    switch($ordenacao){
      case 'padrao':
        break;
      case 'nome_de_usuario_em_ordem_alfabetica':
        $this->get_smarty()->assign('ordem_do_nome_de_usuario', "Nome de Usuário ▲");
        break;
      case 'nome_de_usuario_em_ordem_alfabetica_inversa':
        $this->get_smarty()->assign('ordem_do_nome_de_usuario', "Nome de Usuário ▼");
        break;
      case 'email_em_ordem_alfabetica':
        if($mostrar_email){
          $this->get_smarty()->assign('ordem_do_email', "E-mail ▲");
        }else{
          $ordenacao = 'padrao';
        }
        break;
      case 'email_em_ordem_alfabetica_inversa':
        if($mostrar_email){
          $this->get_smarty()->assign('ordem_do_email', "E-mail ▼");
        }else{
          $ordenacao = 'padrao';
        }
        break;
      case 'momento_do_cadastro_em_ordem_cronologica':
        $this->get_smarty()->assign('ordem_do_momento_do_cadastro', "Cadastrado em ▲");
        break;
      case 'momento_do_cadastro_em_ordem_cronologica_inversa':
        $this->get_smarty()->assign('ordem_do_momento_do_cadastro', "Cadastrado em ▼");
        break;
      case 'tipo_em_ordem_alfabetica':
        $this->get_smarty()->assign('ordem_do_tipo', "Tipo ▲");
        break;
      case 'tipo_em_ordem_alfabetica_inversa':
        $this->get_smarty()->assign('ordem_do_tipo', "Tipo ▼");
        break;
      default:
        $ordenacao = 'padrao';
        break;
    }
    $this->get_smarty()->assign('ordenacao', $ordenacao);

    /* Preparando a quantidade por segmento */
    $quantidade_por_segmento = (int) $requisicao->getGet('quantidade_por_segmento');
    $quantidades_por_segmento = $this->criar_array_quantidades_por_segmento();
    if(in_array($quantidade_por_segmento, $quantidades_por_segmento)){
      $this->get_smarty()->assign('quantidade_por_segmento', $quantidade_por_segmento);
    }else{
      $quantidade_por_segmento = self::QUANTIDADE_PADRAO_POR_SEGMENTO;
      $this->get_smarty()->assign('quantidade_por_segmento', 'padrao');
    }

    $descartar = $quantidade_por_segmento * $segmento - $quantidade_por_segmento;
    $descartar = max($descartar, 0);

    /* Preparando o resultado */
    $usuarios = $usuarios_model->selecionar_usuarios($filtros, $ordenacao, $quantidade_por_segmento,
      $descartar);
    $array_usuarios = array();
    foreach($usuarios as $usuario){
      $array_usuario = array();

      $pk_usuario = $usuario->get_pk_usuario();
      $array_usuario['id_do_usuario'] = $pk_usuario;

      $nome_de_usuario = $usuario->get_nome_de_usuario();
      $nome_de_usuario = esc($nome_de_usuario);
      $array_usuario['nome_de_usuario'] = $nome_de_usuario;

      $array_usuario['email'] = '---';
      if($mostrar_email){
        $email = $usuario->get_email();
        $email = esc($email);
        $array_usuario['email'] = $email;
      }

      $momento_do_cadastro = $usuario->get_momento_do_cadastro();
      $momento_do_cadastro = $this->colocar_no_fuso_horario_do_usuario_logado($momento_do_cadastro);
      $momento_do_cadastro = $this->converter_para_horario_data_do_html($momento_do_cadastro);
      $array_usuario['momento_do_cadastro'] = $momento_do_cadastro;

      $tipo = $usuario->get_tipo();
      $array_usuario['tipo'] = $tipo;

      $array_usuarios[] = $array_usuario;
    }

    $this->get_smarty()->assign('usuarios', $array_usuarios);
  }

  public function mostrar_usuarios_ajax(){
    /* Verificando se o usuário está logado */
    if($this->get_usuario_logado() === null){
      $mensagem = 'Você precisa entrar para poder consultar usuários.';
      $retorno['erro'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $requisicao = service('request');
    $segmento = $requisicao->getGet('segmento');
    if(!is_numeric($segmento) or $segmento <= 0 or floor($segmento) != $segmento){
      $segmento = 1;
    }

    /* Mostrando os usuários */
    $this->mostrar_usuarios($segmento);

    /* Retornando o HTML de mais um segmento da tabela */
    $retorno['linhas_da_tabela'] = '';
    if(!empty($this->get_smarty()->getTemplateVars('usuarios'))){
      $html_usuarios = $this->get_smarty()->fetch('usuarios/linhas_da_tabela_de_usuarios.html');
      $retorno['linhas_da_tabela'] = $html_usuarios;
    }

    echo json_encode($retorno);
    die;
  }

}
