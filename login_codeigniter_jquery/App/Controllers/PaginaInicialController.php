<?php

namespace App\Controllers;

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
      $this->get_smarty()->assign('mensagem_template', $mensagem_template);
      $this->get_sessao()->remove('mensagem_template');
    }

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

}
