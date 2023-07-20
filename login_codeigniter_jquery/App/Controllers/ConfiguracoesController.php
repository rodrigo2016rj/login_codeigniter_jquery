<?php

namespace App\Controllers;

use App\Models\ConfiguracoesModel;
use App\Models\Entidades\Usuario;

final class ConfiguracoesController extends TemplateController{

  public function index($redirecionar = false){
    if($redirecionar){
      //Redireciona para si mesmo, motivo: limpar a requisição.
      header('Location: /configuracoes');
      die;
    }
    $requisicao = service('request');
    $id = $requisicao->getGet('id');
    if($id !== null){
      //Caso a pessoa coloque algum id na URL para confundir, redireciona.
      //Esta página não usa id da URL.
      header('Location: /configuracoes');
      die;
    }

    /* Especificando a página do sistema para os links e outras tags */
    $this->get_smarty()->assign('pagina_template', 'configuracoes');

    /* Mostrando mensagem caso exista alguma */
    if($this->get_sessao()->has('mensagem_template')){
      $mensagem_template = $this->get_sessao()->get('mensagem_template');
      $this->get_smarty()->assign('mensagem_template', $mensagem_template);
      $this->get_sessao()->remove('mensagem_template');
    }

    /* Variável que guarda a mensagem da página começa inicialmente vazia */
    $mensagem = '';

    /* $mostrar_configuracoes a princípio é true */
    $mostrar_configuracoes = true;

    /* Colocando valores nas listas de fuso horários e de visuais */
    $fuso_horarios = $this->criar_array_de_fuso_horarios();
    $this->get_smarty()->assign('fuso_horarios', $fuso_horarios);
    $visuais = $this->criar_array_de_temas_visuais();
    $this->get_smarty()->assign('visuais', $visuais);

    /* Colocando valores iniciais nas variáveis do smarty para não ficarem undefined no HTML */
    $this->get_smarty()->assign('fuso_horario_atual', '');
    $this->get_smarty()->assign('visual_atual', '');
    $this->get_smarty()->assign('nome_de_usuario', '');
    $this->get_smarty()->assign('exibir_sexo_no_perfil', '');
    $this->get_smarty()->assign('exibir_email_no_perfil', '');
    $this->get_smarty()->assign('aba_inicial', 'aba_preferencias');

    $configuracoes_model = new ConfiguracoesModel();

    /* Verificando se o usuário está logado */
    if($this->get_usuario_logado() === null){
      $mensagem = 'Você precisa entrar para poder acessar esta página.';
      $this->get_sessao()->set('chaves_anti_csrf_da_pagina_configuracoes', array());
    }else{
      $pk_usuario = $this->get_usuario_logado()->get_pk_usuario();

      /* Consultando e mostrando informações do usuário */
      $array_resultado = $configuracoes_model->selecionar_usuario($pk_usuario);
      if(isset($array_resultado['mensagem_do_model'])){
        $mensagem = $array_resultado['mensagem_do_model'];
        $mostrar_configuracoes = false;
      }else{
        $usuario = $array_resultado[0];

        $visual_atual = esc($usuario->get_visual());
        $this->get_smarty()->assign('visual_atual', $visual_atual);

        $fuso_horario_atual = esc($usuario->get_fuso_horario());
        $this->get_smarty()->assign('fuso_horario_atual', $fuso_horario_atual);

        $nome_de_usuario = esc($usuario->get_nome_de_usuario());
        $this->get_smarty()->assign('nome_de_usuario', $nome_de_usuario);

        $exibir_sexo_no_perfil = $usuario->get_exibir_sexo_no_perfil();
        $this->get_smarty()->assign('exibir_sexo_no_perfil', $exibir_sexo_no_perfil);

        $exibir_email_no_perfil = $usuario->get_exibir_email_no_perfil();
        $this->get_smarty()->assign('exibir_email_no_perfil', $exibir_email_no_perfil);
      }
    }

    /* Colocando campo anti csrf no HTML e no array de chaves desta página */
    $chave_anti_csrf = $this->criar_hash_da_chave_anti_csrf();
    $this->get_smarty()->assign('chave_anti_csrf', $chave_anti_csrf);
    if(!$this->get_sessao()->has('chaves_anti_csrf_da_pagina_configuracoes')){
      $this->get_sessao()->set('chaves_anti_csrf_da_pagina_configuracoes', array());
    }
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_da_pagina_configuracoes');
    $chaves_anti_csrf[] = $chave_anti_csrf;
    $this->get_sessao()->set('chaves_anti_csrf_da_pagina_configuracoes', $chaves_anti_csrf);

    /* Se houver mensagem na sessão, deve ser mostrada */
    if($this->get_sessao()->has('mensagem_da_pagina_configuracoes')){
      $mensagem = $this->get_sessao()->get('mensagem_da_pagina_configuracoes');
      $this->get_sessao()->remove('mensagem_da_pagina_configuracoes');
    }

    $this->get_smarty()->assign('mostrar_configuracoes', $mostrar_configuracoes);
    $this->get_smarty()->assign('mensagem_da_pagina', $mensagem);
    $this->get_smarty()->display('configuracoes/configuracoes.html');
    die;
  }

  public function entrar(){
    $this->get_sessao()->set('chaves_anti_csrf_da_pagina_configuracoes', array());
    $this->entrar_padronizado();
    $this->index(true);
  }

  public function sair(){
    $this->get_sessao()->set('chaves_anti_csrf_da_pagina_configuracoes', array());
    $this->sair_padronizado();
    $this->index(true);
  }

  public function escolher_fuso_horario_ajax(){
    if($this->get_usuario_logado() === null){
      $mensagem = 'Você precisa entrar para poder realizar esta operação.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $requisicao = service('request');
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_da_pagina_configuracoes');
    $chave_anti_csrf = $requisicao->getPost('chave_anti_csrf');
    if(!in_array($chave_anti_csrf, $chaves_anti_csrf)){
      $mensagem = 'O formulário havia expirado, recarregue esta página e tente novamente.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }
    $fuso_horario = $requisicao->getPost('fuso_horario');
    $fuso_horarios_permitidos = $this->criar_array_de_fuso_horarios();
    if(!in_array($fuso_horario, array_keys($fuso_horarios_permitidos))){
      $mensagem = 'O fuso horário escolhido não foi configurado para este sistema ou é um fuso';
      $mensagem .= ' horário inválido. Por favor, selecione outro fuso horário.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $pk_usuario = $this->get_usuario_logado()->get_pk_usuario();

    $configuracoes_model = new ConfiguracoesModel();
    $configuracoes_model->salvar_fuso_horario($fuso_horario, $pk_usuario);

    $retorno = array();
    echo json_encode($retorno);
    die;
  }

  public function escolher_visual_ajax(){
    if($this->get_usuario_logado() === null){
      $mensagem = 'Você precisa entrar para poder realizar esta operação.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $requisicao = service('request');
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_da_pagina_configuracoes');
    $chave_anti_csrf = $requisicao->getPost('chave_anti_csrf');
    if(!in_array($chave_anti_csrf, $chaves_anti_csrf)){
      $mensagem = 'O formulário havia expirado, recarregue esta página e tente novamente.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }
    $visual = $requisicao->getPost('visual');
    $visuais_permitidos = $this->criar_array_de_temas_visuais();
    if(!in_array($visual, array_keys($visuais_permitidos))){
      $mensagem = 'O visual escolhido não é um visual válido. Por favor, selecione outro visual.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $pk_usuario = $this->get_usuario_logado()->get_pk_usuario();

    $configuracoes_model = new ConfiguracoesModel();
    $configuracoes_model->salvar_visual($visual, $pk_usuario);

    $retorno = array();
    echo json_encode($retorno);
    die;
  }

  public function editar_nome_de_usuario_ajax(){
    if($this->get_usuario_logado() === null){
      $mensagem = 'Você precisa entrar para poder realizar esta operação.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $requisicao = service('request');
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_da_pagina_configuracoes');
    $chave_anti_csrf = $requisicao->getPost('chave_anti_csrf');
    if(!in_array($chave_anti_csrf, $chaves_anti_csrf)){
      $mensagem = 'O formulário havia expirado, recarregue esta página e tente novamente.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $nome_de_usuario = trim($requisicao->getPost('nome_de_usuario') ?? '');
    if($nome_de_usuario === ''){
      $mensagem = 'O campo nome de usuário não pode ficar em branco.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $usuario = new Usuario();
    $caracteres_permitidos = $usuario->caracteres_permitidos_para_nome_de_usuario();
    $array_caracteres_do_nome_do_usuario = mb_str_split($nome_de_usuario, 1);

    foreach($array_caracteres_do_nome_do_usuario as $caractere){
      if(strpos($caracteres_permitidos, $caractere) === false){
        if($caractere === ' '){
          $caractere = 'espaço';
        }
        $mensagem = 'O valor escolhido para o nome de usuário não é válido.';
        $mensagem .= " O nome de usuário não pode ter o caractere $caractere.";
        $mensagem .= ' O nome de usuário só pode ter os seguintes caracteres:';
        $caracteres_permitidos = implode(' ', (str_split($caracteres_permitidos, 1)));
        $mensagem .= " $caracteres_permitidos";
        $retorno['mensagem_de_falha'] = $mensagem;
        echo json_encode($retorno);
        die;
      }
    }

    $configuracoes_model = new ConfiguracoesModel();
    $pk_usuario = $this->get_usuario_logado()->get_pk_usuario();

    $array_resultado = $configuracoes_model->verifica_disponibilidade_de_nome_de_usuario($nome_de_usuario,
      $pk_usuario);
    if(isset($array_resultado['mensagem_do_model'])){
      $mensagem = $array_resultado['mensagem_do_model'];
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $minimo = $usuario->quantidade_minima_de_caracteres('nome_de_usuario');
    $maximo = $usuario->quantidade_maxima_de_caracteres('nome_de_usuario');
    $quantidade = mb_strlen($nome_de_usuario);

    if($quantidade < $minimo){
      $mensagem = "O campo nome de usuário precisa ter no mínimo $minimo caracteres.";
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    if($quantidade > $maximo){
      $mensagem = "O campo nome de usuário não pode ultrapassar $maximo caracteres.";
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $array_resultado = $configuracoes_model->salvar_nome_de_usuario($nome_de_usuario, $pk_usuario);
    if(isset($array_resultado['mensagem_do_model'])){
      $mensagem = $array_resultado['mensagem_do_model'];
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $this->get_usuario_logado()->set_nome_de_usuario($nome_de_usuario);
    if($this->get_sessao()->has('nome_de_usuario')){
      $this->get_sessao()->set('nome_de_usuario', $nome_de_usuario);
    }

    $retorno['nome_de_usuario'] = $nome_de_usuario;
    echo json_encode($retorno);
    die;
  }

  public function exibir_nao_exibir_sexo_no_perfil_ajax(){
    if($this->get_usuario_logado() === null){
      $mensagem = 'Você precisa entrar para poder realizar esta operação.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $requisicao = service('request');
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_da_pagina_configuracoes');
    $chave_anti_csrf = $requisicao->getPost('chave_anti_csrf');
    if(!in_array($chave_anti_csrf, $chaves_anti_csrf)){
      $mensagem = 'O formulário havia expirado, recarregue esta página e tente novamente.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $opcao_escolhida = $requisicao->getPost('opcao_escolhida');

    $usuario = new Usuario();
    $array_opcoes = $usuario->enum_exibir_sexo_no_perfil();

    if(!in_array($opcao_escolhida, array_keys($array_opcoes))){
      $mensagem = 'A opção escolhida não é uma opção válida. Por favor, selecione outra opção.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $pk_usuario = $this->get_usuario_logado()->get_pk_usuario();

    $configuracoes_model = new ConfiguracoesModel();
    $configuracoes_model->salvar_escolha_de_exibicao_de_sexo_no_perfil($opcao_escolhida, $pk_usuario);

    $retorno = array();
    echo json_encode($retorno);
    die;
  }

  public function exibir_nao_exibir_email_no_perfil_ajax(){
    if($this->get_usuario_logado() === null){
      $mensagem = 'Você precisa entrar para poder realizar esta operação.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $requisicao = service('request');
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_da_pagina_configuracoes');
    $chave_anti_csrf = $requisicao->getPost('chave_anti_csrf');
    if(!in_array($chave_anti_csrf, $chaves_anti_csrf)){
      $mensagem = 'O formulário havia expirado, recarregue esta página e tente novamente.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $opcao_escolhida = $requisicao->getPost('opcao_escolhida');

    $usuario = new Usuario();
    $array_opcoes = $usuario->enum_exibir_email_no_perfil();

    if(!in_array($opcao_escolhida, array_keys($array_opcoes))){
      $mensagem = 'A opção escolhida não é uma opção válida. Por favor, selecione outra opção.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $pk_usuario = $this->get_usuario_logado()->get_pk_usuario();

    $configuracoes_model = new ConfiguracoesModel();
    $configuracoes_model->salvar_escolha_de_exibicao_de_email_no_perfil($opcao_escolhida, $pk_usuario);

    $retorno = array();
    echo json_encode($retorno);
    die;
  }

  public function mudar_senha_ajax(){
    if($this->get_usuario_logado() === null){
      $mensagem = 'Você precisa entrar para poder realizar esta operação.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $requisicao = service('request');
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_da_pagina_configuracoes');
    $chave_anti_csrf = $requisicao->getPost('chave_anti_csrf');
    if(!in_array($chave_anti_csrf, $chaves_anti_csrf)){
      $mensagem = 'O formulário havia expirado, recarregue esta página e tente novamente.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $senha_atual = $requisicao->getPost('senha_atual');
    $nova_senha = $requisicao->getPost('nova_senha');
    $nova_senha_novamente = $requisicao->getPost('nova_senha_novamente');

    if($senha_atual === '' or $senha_atual === null){
      $mensagem = 'O campo senha atual não pode ficar em branco.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    if(!password_verify($senha_atual, $this->get_usuario_logado()->get_senha())){
      $mensagem = 'A senha atual digitada não está correta.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    if($nova_senha === '' or $nova_senha === null){
      $mensagem = 'O campo nova senha não pode ficar em branco.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $usuario = new Usuario();
    $minimo = $usuario->quantidade_minima_de_caracteres('senha');
    $maximo = $usuario->quantidade_maxima_de_caracteres('senha');
    $quantidade = mb_strlen($nova_senha);

    if($quantidade < $minimo){
      $mensagem = "O campo nova senha precisa ter no mínimo $minimo caracteres.";
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }
    if($quantidade > $maximo){
      $mensagem = "O campo nova senha não pode ultrapassar $maximo caracteres.";
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    if($nova_senha_novamente === '' or $nova_senha_novamente === null){
      $mensagem = 'O campo nova senha novamente não pode ficar em branco.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    if($nova_senha_novamente !== $nova_senha){
      $mensagem = 'O valor do campo nova senha novamente precisa ser o mesmo valor do campo nova';
      $mensagem .= ' senha. Essa validação existe para te ajudar a não preencher errado.';
      $retorno['mensagem_de_falha'] = $mensagem;
      echo json_encode($retorno);
      die;
    }

    $nova_senha = $this->criptografar_senha_do_usuario($nova_senha);
    $pk_usuario = $this->get_usuario_logado()->get_pk_usuario();

    $configuracoes_model = new ConfiguracoesModel();
    $configuracoes_model->salvar_nova_senha($nova_senha, $pk_usuario);

    $retorno = array();
    echo json_encode($retorno);
    die;
  }

}
