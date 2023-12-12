<?php

namespace App\Controllers;

use App\Models\PaginaInicialModel;
use App\Models\Entidades\Usuario;

final class PaginaInicialController extends TemplateController{

  public function index($redirecionar = false){
    if($redirecionar){
      //Redireciona para si mesmo, motivo: limpar a requisição.
      header('Location: /pagina_inicial');
      die;
    }

    /* Especificando a página do sistema para os links e outras tags */
    $this->get_smarty()->assign('pagina_template', 'pagina_inicial');

    /* Mostrando mensagem caso exista alguma */
    if($this->get_sessao()->has('mensagem_template')){
      $mensagem_template = $this->get_sessao()->get('mensagem_template');
      $mensagem_template = esc($mensagem_template);
      $this->get_smarty()->assign('mensagem_template', $mensagem_template);
      $this->get_sessao()->remove('mensagem_template');
    }

    /* Variável que guarda a mensagem da página começa inicialmente vazia */
    $mensagem = '';

    /* Se houver mensagem na sessão, deve ser mostrada */
    if($this->get_sessao()->has('mensagem_da_pagina_inicial')){
      $mensagem = $this->get_sessao()->get('mensagem_da_pagina_inicial');
      $this->get_sessao()->remove('mensagem_da_pagina_inicial');
    }

    $mensagem = esc($mensagem);
    $this->get_smarty()->assign('mensagem_da_pagina', $mensagem);
    $this->get_smarty()->display('pagina_inicial/pagina_inicial.html');
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

  public function confirmar_conta(){
    $pagina_inicial_model = new PaginaInicialModel();

    $usuario = new Usuario();

    /* Obtendo valores da URL */
    $requisicao = service('request');
    $pk_usuario = $requisicao->getGet('id_do_usuario');
    $chave = $requisicao->getGet('chave');

    /* Validações */
    if(!is_numeric($pk_usuario) or $pk_usuario <= 0 or floor($pk_usuario) != $pk_usuario){
      $mensagem = 'A conta não foi confirmada.';
      $mensagem .= ' O ID do usuário precisa ser um número natural maior que zero.';
      $this->get_sessao()->set('mensagem_da_pagina_inicial', $mensagem);
      $this->index();
    }
    $array_resultado = $pagina_inicial_model->selecionar_usuario($pk_usuario);
    if(isset($array_resultado['mensagem_do_model'])){
      $mensagem = 'A conta não foi confirmada.';
      $mensagem .= " {$array_resultado['mensagem_do_model']}";
      $this->get_sessao()->set('mensagem_da_pagina_inicial', $mensagem);
      $this->index();
    }else{
      $usuario = $array_resultado[0];
    }

    if($usuario->get_conta_confirmada() === 'sim'){
      $mensagem = 'A conta já havia sido confirmada.';
      $mensagem .= ' Para fazer login clique no link "Entrar" no menu do topo direito.';
      $this->get_sessao()->set('mensagem_da_pagina_inicial', $mensagem);
      $this->index();
    }

    if($chave === '' or $chave !== $usuario->get_chave_para_operacoes_via_link()){
      $mensagem = 'A conta não foi confirmada.';
      $mensagem .= ' A chave contida na URL não é válida.';
      $this->get_sessao()->set('mensagem_da_pagina_inicial', $mensagem);
      $this->index();
    }

    /* Confirmando conta */
    $array_resultado = $pagina_inicial_model->confirmar_conta($usuario);
    if(isset($array_resultado['mensagem_do_model'])){
      $mensagem = 'A conta não foi confirmada.';
      $mensagem .= " {$array_resultado['mensagem_do_model']}";
      $this->get_sessao()->set('mensagem_da_pagina_inicial', $mensagem);
      $this->index();
    }else{
      $mensagem = 'A conta foi confirmada com sucesso.';
      $mensagem .= ' Para fazer login clique no link "Entrar" no menu do topo direito.';
      $this->get_sessao()->set('mensagem_da_pagina_inicial', $mensagem);
      $this->index();
    }
  }

}
