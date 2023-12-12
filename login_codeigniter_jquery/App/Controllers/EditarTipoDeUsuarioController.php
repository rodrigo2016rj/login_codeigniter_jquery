<?php

namespace App\Controllers;

use App\Models\EditarTipoDeUsuarioModel;
use App\Models\Entidades\Usuario;

final class EditarTipoDeUsuarioController extends TemplateController{

  public function index($redirecionar_com_id = false){
    if($redirecionar_com_id === 'pagina_inicial'){
      //Redireciona para a página inicial caso seja necessário.
      header('Location: /pagina_inicial');
      die;
    }elseif($redirecionar_com_id !== false){
      //Redireciona para si mesmo, motivo: limpar a requisição.
      header("Location: /editar_tipo_de_usuario?id=$redirecionar_com_id");
      die;
    }

    /* Especificando a página do sistema para os links e outras tags */
    $this->get_smarty()->assign('pagina_template', 'editar_tipo_de_usuario');

    /* Mostrando mensagem caso exista alguma */
    if($this->get_sessao()->has('mensagem_template')){
      $mensagem_template = $this->get_sessao()->get('mensagem_template');
      $mensagem_template = esc($mensagem_template);
      $this->get_smarty()->assign('mensagem_template', $mensagem_template);
      $this->get_sessao()->remove('mensagem_template');
    }

    /* Variável que guarda a mensagem da página começa inicialmente vazia */
    $mensagem = '';

    /* $mostrar_formulario a princípio é true */
    $mostrar_formulario = true;

    /* Informando os tipos de usuário */
    $usuario = new Usuario();
    $tipos_de_usuario = $usuario->enum_tipo();
    $this->get_smarty()->assign('tipos_de_usuario', $tipos_de_usuario);

    /* Colocando valores iniciais nas variáveis do smarty para não ficarem undefined no HTML */
    $this->get_smarty()->assign('nome_de_usuario', '');
    $this->get_smarty()->assign('tipo', '');
    $this->get_smarty()->assign('id', '');

    $editar_tipo_de_usuario_model = new EditarTipoDeUsuarioModel();

    /* Validando o ID de usuário informado na URL */
    $requisicao = service('request');
    $pk_usuario = $requisicao->getGet('id');
    if(!is_numeric($pk_usuario) or $pk_usuario <= 0 or floor($pk_usuario) != $pk_usuario){
      $mensagem = 'ID inválido, o ID do usuário precisa ser um número natural maior que zero.';
      $mostrar_formulario = false;
    }else{
      $this->get_smarty()->assign('id_referencia_template', $pk_usuario);

      /* Consultando e mostrando informações do usuário */
      $array_resultado = $editar_tipo_de_usuario_model->selecionar_usuario($pk_usuario);
      if(isset($array_resultado['mensagem_do_model'])){
        $mensagem = $array_resultado['mensagem_do_model'];
        $mostrar_formulario = false;
      }else{
        $usuario = $array_resultado[0];

        $id = $usuario->get_pk_usuario();
        $this->get_smarty()->assign('id', $id);

        $nome_de_usuario = esc($usuario->get_nome_de_usuario());
        $this->get_smarty()->assign('nome_de_usuario', $nome_de_usuario);

        $tipo = $usuario->get_tipo();
        $this->get_smarty()->assign('tipo', $tipo);
      }
    }

    /* Recolocando valores preenchidos previamente pelo usuário no formulário */
    if($this->get_sessao()->has('backup_do_formulario_da_pagina_editar_tipo_de_usuario')){
      $backup = $this->get_sessao()->get('backup_do_formulario_da_pagina_editar_tipo_de_usuario');
      $tipo = esc($backup['tipo']);
      $this->get_smarty()->assign('tipo', $tipo);
      $this->get_sessao()->remove('backup_do_formulario_da_pagina_editar_tipo_de_usuario');
    }

    /* Colocando campo anti csrf no formulário e no array de chaves desta página */
    $chave_anti_csrf = $this->criar_hash_da_chave_anti_csrf();
    $this->get_smarty()->assign('chave_anti_csrf', $chave_anti_csrf);
    if(!$this->get_sessao()->has('chaves_anti_csrf_da_pagina_editar_tipo_de_usuario')){
      $this->get_sessao()->set('chaves_anti_csrf_da_pagina_editar_tipo_de_usuario', array());
    }
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_da_pagina_editar_tipo_de_usuario');
    $chaves_anti_csrf[] = $chave_anti_csrf;
    $this->get_sessao()->set('chaves_anti_csrf_da_pagina_editar_tipo_de_usuario', $chaves_anti_csrf);

    /* Verificando se o usuário está logado */
    if($this->get_usuario_logado() === null){
      $mensagem = 'Você precisa entrar para poder acessar esta página.';
      $this->get_sessao()->set('chaves_anti_csrf_da_pagina_editar_tipo_de_usuario', array());
    }elseif($mostrar_formulario){
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

      $tipo_do_usuario_logado_na_frase = $tipo_do_usuario_logado;
      if(isset($tipos_de_usuario[$tipo_do_usuario_logado])){
        $tipo_do_usuario_logado_na_frase = mb_strtolower($tipos_de_usuario[$tipo_do_usuario_logado]);
      }
      $tipo_do_usuario_alvo_na_frase = $tipo_do_usuario_alvo;
      if(isset($tipos_de_usuario[$tipo_do_usuario_alvo])){
        $tipo_do_usuario_alvo_na_frase = mb_strtolower($tipos_de_usuario[$tipo_do_usuario_alvo]);
      }

      if($nivel_do_tipo_do_usuario_logado <= $nivel_do_tipo_do_usuario_alvo){
        $mensagem = 'Você não tem permissão para editar o tipo deste usuário.';
        $mostrar_formulario = false;
      }elseif($nivel_do_tipo_do_usuario_logado === 1){
        $mensagem = "Como um usuário do tipo $tipo_do_usuario_logado_na_frase não pode promover";
        $mensagem .= " um usuário do tipo $tipo_do_usuario_alvo_na_frase, você não pode acessar";
        $mensagem .= ' esta página.';
        $mostrar_formulario = false;
      }
    }

    /* Se houver mensagem na sessão, deve ser mostrada */
    if($this->get_sessao()->has('mensagem_da_pagina_editar_tipo_de_usuario')){
      $mensagem = $this->get_sessao()->get('mensagem_da_pagina_editar_tipo_de_usuario');
      $this->get_sessao()->remove('mensagem_da_pagina_editar_tipo_de_usuario');
    }

    $this->get_smarty()->assign('mostrar_formulario', $mostrar_formulario);
    $mensagem = esc($mensagem);
    $this->get_smarty()->assign('mensagem_da_pagina', $mensagem);
    $this->get_smarty()->display('editar_tipo_de_usuario/editar_tipo_de_usuario.html');
    die;
  }

  public function entrar(){
    $this->get_sessao()->set('chaves_anti_csrf_da_pagina_editar_tipo_de_usuario', array());
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
    $this->get_sessao()->set('chaves_anti_csrf_da_pagina_editar_tipo_de_usuario', array());
    $this->sair_padronizado();
    $requisicao = service('request');
    $pk_usuario = $requisicao->getGet('id');
    if(is_numeric($pk_usuario) and $pk_usuario > 0 and floor($pk_usuario) == $pk_usuario){
      $this->index($pk_usuario);
    }else{
      $this->index('pagina_inicial');
    }
  }

  public function editar(){
    $editar_tipo_de_usuario_model = new EditarTipoDeUsuarioModel();

    $usuario = new Usuario();

    /* Obtendo valores do formulário */
    $requisicao = service('request');
    $pk_usuario = $requisicao->getPost('pk_usuario');
    $tipo = $requisicao->getPost('tipo');

    /* Fazendo backup do formulário */
    $backup_do_formulario['pk_usuario'] = $pk_usuario;
    $backup_do_formulario['tipo'] = $tipo;
    $this->get_sessao()->set('backup_do_formulario_da_pagina_editar_tipo_de_usuario', $backup_do_formulario);

    /* Verificando se o usuário está logado */
    if($this->get_usuario_logado() === null){
      $mensagem = 'O tipo do usuário não foi editado.';
      $mensagem .= ' Você precisa entrar para poder editar.';
      $this->get_sessao()->set('mensagem_da_pagina_editar_tipo_de_usuario', $mensagem);
      $this->index($pk_usuario);
    }

    /* Validações */
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_da_pagina_editar_tipo_de_usuario');
    $chave_anti_csrf = $requisicao->getPost('chave_anti_csrf');
    if(!in_array($chave_anti_csrf, $chaves_anti_csrf)){
      $mensagem = 'O tipo do usuário não foi editado.';
      $mensagem .= ' O formulário havia expirado, tente novamente.';
      $this->get_sessao()->set('mensagem_da_pagina_editar_tipo_de_usuario', $mensagem);
      $this->index($pk_usuario);
    }else{
      $chaves_anti_csrf = array_diff($chaves_anti_csrf, [$chave_anti_csrf]);
      $this->get_sessao()->set('chaves_anti_csrf_da_pagina_editar_tipo_de_usuario', $chaves_anti_csrf);
    }

    if(!is_numeric($pk_usuario) or $pk_usuario <= 0 or floor($pk_usuario) != $pk_usuario){
      $mensagem = 'O tipo do usuário não foi editado.';
      $mensagem .= ' O ID do usuário precisa ser um número natural maior que zero.';
      $this->get_sessao()->set('mensagem_da_pagina_editar_tipo_de_usuario', $mensagem);
      $this->index($pk_usuario);
    }
    $array_resultado = $editar_tipo_de_usuario_model->selecionar_usuario($pk_usuario);
    if(isset($array_resultado['mensagem_do_model'])){
      $mensagem = 'O tipo do usuário não foi editado.';
      $mensagem .= " {$array_resultado['mensagem_do_model']}";
      $this->get_sessao()->set('mensagem_da_pagina_editar_tipo_de_usuario', $mensagem);
      $this->index($pk_usuario);
    }else{
      $usuario = $array_resultado[0];
    }

    $tipos_de_usuario = $usuario->enum_tipo();
    if(!in_array($tipo, array_keys($tipos_de_usuario))){
      $mensagem = 'O tipo do usuário não foi editado.';
      $mensagem .= ' O tipo escolhido não é válido.';
      $this->get_sessao()->set('mensagem_da_pagina_editar_tipo_de_usuario', $mensagem);
      $this->index($pk_usuario);
    }

    $tipo_do_usuario_logado = $this->get_usuario_logado()->get_tipo();
    $tipo_do_usuario_alvo = $usuario->get_tipo();
    $niveis = $usuario->niveis_dos_tipos_de_usuario();
    $nivel_do_tipo_do_usuario_logado = 0;
    $nivel_do_tipo_do_usuario_alvo = 0;
    $nivel_do_tipo_escolhido = 0;
    foreach($niveis as $chave => $valor){
      if(in_array($tipo_do_usuario_logado, $valor)){
        $nivel_do_tipo_do_usuario_logado = $chave;
      }
      if(in_array($tipo_do_usuario_alvo, $valor)){
        $nivel_do_tipo_do_usuario_alvo = $chave;
      }
      if(in_array($tipo, $valor)){
        $nivel_do_tipo_escolhido = $chave;
      }
    }

    $tipo_do_usuario_alvo_na_frase = $tipo_do_usuario_alvo;
    if(isset($tipos_de_usuario[$tipo_do_usuario_alvo])){
      $tipo_do_usuario_alvo_na_frase = mb_strtolower($tipos_de_usuario[$tipo_do_usuario_alvo]);
    }
    $tipo_na_frase = $tipo;
    if(isset($tipos_de_usuario[$tipo])){
      $tipo_na_frase = mb_strtolower($tipos_de_usuario[$tipo]);
    }

    if($nivel_do_tipo_do_usuario_logado <= $nivel_do_tipo_do_usuario_alvo){
      $mensagem = 'O tipo do usuário não foi editado.';
      $mensagem .= " Você não tem permissão para editar um usuário do tipo $tipo_do_usuario_alvo_na_frase.";
      $this->get_sessao()->set('mensagem_da_pagina_editar_tipo_de_usuario', $mensagem);
      $this->index($pk_usuario);
    }
    if($nivel_do_tipo_do_usuario_logado <= $nivel_do_tipo_escolhido){
      $mensagem = 'O tipo do usuário não foi editado.';
      $mensagem .= " Você não tem permissão para atribuir o tipo $tipo_na_frase a um usuário.";
      $this->get_sessao()->set('mensagem_da_pagina_editar_tipo_de_usuario', $mensagem);
      $this->index($pk_usuario);
    }

    /* Editar usuário no banco de dados */
    $usuario->set_pk_usuario($pk_usuario);
    $usuario->set_tipo($tipo);
    $array_resultado = $editar_tipo_de_usuario_model->editar_tipo_de_usuario($usuario);
    if(isset($array_resultado['mensagem_do_model'])){
      $mensagem = 'O tipo do usuário não foi editado.';
      $mensagem .= " {$array_resultado['mensagem_do_model']}";
      $this->get_sessao()->set('mensagem_da_pagina_editar_tipo_de_usuario', $mensagem);
      $this->index($pk_usuario);
    }else{
      $mensagem = 'O tipo do usuário foi editado com sucesso.';
      $this->get_sessao()->set('mensagem_da_pagina_editar_tipo_de_usuario', $mensagem);
      $this->get_sessao()->remove('backup_do_formulario_da_pagina_editar_tipo_de_usuario');
      $this->index($pk_usuario);
    }
  }

}
