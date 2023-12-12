<?php

namespace App\Controllers;

use App\Models\CadastreSeModel;
use App\Models\Entidades\Usuario;
use DateTime;
use DateTimeZone;
use Config\Services as Servicos;

final class CadastreSeController extends TemplateController{

  public function index($redirecionar = false){
    if($redirecionar){
      //Redireciona para si mesmo, motivo: limpar a requisição.
      header('Location: /cadastre-se');
      die;
    }

    /* Especificando a página do sistema para os links e outras tags */
    $this->get_smarty()->assign('pagina_template', 'cadastre-se');

    /* Mostrando mensagem caso exista alguma */
    if($this->get_sessao()->has('mensagem_template')){
      $mensagem_template = $this->get_sessao()->get('mensagem_template');
      $mensagem_template = esc($mensagem_template);
      $this->get_smarty()->assign('mensagem_template', $mensagem_template);
      $this->get_sessao()->remove('mensagem_template');
    }

    /* Variável que guarda a mensagem da página começa inicialmente vazia */
    $mensagem = '';

    /* Carregando lista de sexos */
    $usuario = new Usuario();
    $this->get_smarty()->assign('array_sexos', $usuario->enum_sexo());

    /* Colocando valores iniciais nas variáveis do smarty para não ficarem undefined no HTML */
    $this->get_smarty()->assign('nome_de_usuario', '');
    $this->get_smarty()->assign('email', '');
    $this->get_smarty()->assign('sexo', '');

    /* Recolocando valores preenchidos previamente pelo usuário no formulário */
    if($this->get_sessao()->has('backup_do_formulario_da_pagina_cadastre_se')){
      $backup = $this->get_sessao()->get('backup_do_formulario_da_pagina_cadastre_se');
      $nome_de_usuario = esc($backup['nome_de_usuario']);
      $this->get_smarty()->assign('nome_de_usuario', $nome_de_usuario);
      $email = esc($backup['email']);
      $this->get_smarty()->assign('email', $email);
      $sexo = esc($backup['sexo']);
      $this->get_smarty()->assign('sexo', $sexo);
      $this->get_sessao()->remove('backup_do_formulario_da_pagina_cadastre_se');
    }

    /* Colocando campo anti csrf no formulário e no array de chaves desta página */
    $chave_anti_csrf = $this->criar_hash_da_chave_anti_csrf();
    $this->get_smarty()->assign('chave_anti_csrf', $chave_anti_csrf);
    if(!$this->get_sessao()->has('chaves_anti_csrf_da_pagina_cadastre_se')){
      $this->get_sessao()->set('chaves_anti_csrf_da_pagina_cadastre_se', array());
    }
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_da_pagina_cadastre_se');
    $chaves_anti_csrf[] = $chave_anti_csrf;
    $this->get_sessao()->set('chaves_anti_csrf_da_pagina_cadastre_se', $chaves_anti_csrf);

    /* Se houver mensagem na sessão, deve ser mostrada */
    if($this->get_sessao()->has('mensagem_da_pagina_cadastre_se')){
      $mensagem = $this->get_sessao()->get('mensagem_da_pagina_cadastre_se');
      $this->get_sessao()->remove('mensagem_da_pagina_cadastre_se');
    }

    $mensagem = esc($mensagem);
    $this->get_smarty()->assign('mensagem_da_pagina', $mensagem);
    $this->get_smarty()->display('cadastre-se/cadastre-se.html');
    die;
  }

  public function entrar(){
    $this->get_sessao()->set('chaves_anti_csrf_da_pagina_cadastre_se', array());
    $this->entrar_padronizado();
    $this->index(true);
  }

  public function sair(){
    $this->get_sessao()->set('chaves_anti_csrf_da_pagina_cadastre_se', array());
    $this->sair_padronizado();
    $this->index(true);
  }

  public function cadastrar(){
    $cadastre_se_model = new CadastreSeModel();

    $usuario = new Usuario();

    /* Obtendo valores do formulário */
    $requisicao = service('request');
    $nome_de_usuario = trim($requisicao->getPost('nome_de_usuario') ?? '');
    $email = trim($requisicao->getPost('email') ?? '');
    $senha = $requisicao->getPost('senha');
    $senha_novamente = $requisicao->getPost('senha_novamente');
    $sexo = trim($requisicao->getPost('sexo') ?? '');

    /* Removendo espaços vazios do e-mail digitado caso existam */
    $email = str_replace(' ', '', $email);

    /* Fazendo backup do formulário */
    $backup_do_formulario['nome_de_usuario'] = $nome_de_usuario;
    $backup_do_formulario['email'] = $email;
    $backup_do_formulario['sexo'] = $sexo;
    $this->get_sessao()->set('backup_do_formulario_da_pagina_cadastre_se', $backup_do_formulario);

    /* Validações */
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_da_pagina_cadastre_se');
    $chave_anti_csrf = $requisicao->getPost('chave_anti_csrf');
    if(!in_array($chave_anti_csrf, $chaves_anti_csrf)){
      $mensagem = 'O formulário havia expirado, tente novamente.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }else{
      $chaves_anti_csrf = array_diff($chaves_anti_csrf, [$chave_anti_csrf]);
      $this->get_sessao()->set('chaves_anti_csrf_da_pagina_cadastre_se', $chaves_anti_csrf);
    }

    if($nome_de_usuario === ''){
      $mensagem = 'O campo nome de usuário precisa ser preenchido.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    $array_caracteres_do_nome_do_usuario = mb_str_split($nome_de_usuario, 1);
    $caracteres_permitidos = $usuario->caracteres_permitidos_para_nome_de_usuario();
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
        $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
        $this->index(true);
      }
    }
    $array_resultado = $cadastre_se_model->verifica_disponibilidade_de_nome_de_usuario($nome_de_usuario);
    if(isset($array_resultado['mensagem_do_model'])){
      $mensagem = $array_resultado['mensagem_do_model'];
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    $minimo = $usuario->quantidade_minima_de_caracteres('nome_de_usuario');
    $maximo = $usuario->quantidade_maxima_de_caracteres('nome_de_usuario');
    $quantidade = mb_strlen($nome_de_usuario);
    if($quantidade < $minimo){
      $mensagem = "O campo nome de usuário precisa ter no mínimo $minimo caracteres.";
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    if($quantidade > $maximo){
      $mensagem = "O campo nome de usuário não pode ultrapassar $maximo caracteres.";
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }

    if($email === ''){
      $mensagem = 'O campo e-mail precisa ser preenchido.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    $quantidade_de_arrobas = substr_count($email, '@');
    if($quantidade_de_arrobas > 1){
      $mensagem = 'O campo e-mail precisa ter somente um caractere @.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    if($quantidade_de_arrobas < 1){
      $mensagem = 'O campo e-mail precisa ter pelo menos um caractere @.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    if(str_starts_with($email, '@')){
      $mensagem = 'A parte antes do @ no campo e-mail precisa ser preenchida.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    $parte_do_arroba_e_apos = strstr($email, '@');
    if($parte_do_arroba_e_apos === '@'){
      $mensagem = 'A parte após o @ no campo e-mail, domínio do e-mail, precisa ser preenchida.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    if(strpos($parte_do_arroba_e_apos, '.') === false){
      $mensagem = 'A parte após o @ no campo e-mail, domínio do e-mail, não foi preenchida';
      $mensagem .= ' corretamente. Está faltando o caractere . (ponto).';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    if(strpos($parte_do_arroba_e_apos, '.') === 1){
      $mensagem = 'A parte após o @ no campo e-mail, domínio do e-mail, não foi preenchida';
      $mensagem .= ' corretamente. Está faltando a parte antes do ponto.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    $parte_do_ponto_e_apos = strstr($parte_do_arroba_e_apos, '.');
    if($parte_do_ponto_e_apos === '.'){
      $mensagem = 'A parte após o @ no campo e-mail, domínio do e-mail, não foi preenchida';
      $mensagem .= ' corretamente. Está faltando a parte após o ponto.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    $array_resultado = $cadastre_se_model->verifica_disponibilidade_de_email($email);
    if(isset($array_resultado['mensagem_do_model'])){
      $mensagem = $array_resultado['mensagem_do_model'];
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    $minimo = $usuario->quantidade_minima_de_caracteres('email');
    $maximo = $usuario->quantidade_maxima_de_caracteres('email');
    $quantidade = mb_strlen($email);
    if($quantidade < $minimo){
      $mensagem = "O campo e-mail precisa ter no mínimo $minimo caracteres.";
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    if($quantidade > $maximo){
      $mensagem = "O campo e-mail não pode ultrapassar $maximo caracteres.";
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }

    if($senha === '' or $senha === null){
      $mensagem = 'O campo senha precisa ser preenchido.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    $minimo = $usuario->quantidade_minima_de_caracteres('senha');
    $maximo = $usuario->quantidade_maxima_de_caracteres('senha');
    $quantidade = mb_strlen($senha);
    if($quantidade < $minimo){
      $mensagem = "O campo senha precisa ter no mínimo $minimo caracteres.";
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    if($quantidade > $maximo){
      $mensagem = "O campo senha não pode ultrapassar $maximo caracteres.";
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }

    if($senha_novamente === '' or $senha_novamente === null){
      $mensagem = 'O segundo campo de senha precisa ser preenchido.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    if($senha_novamente !== $senha){
      $mensagem = 'O valor do segundo campo de senha precisa ser o mesmo valor do primeiro campo';
      $mensagem .= ' de senha. Essa validação existe para te ajudar a não preencher errado.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }

    if($sexo === ''){
      $mensagem = 'O sexo precisa ser informado.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }
    if(!array_key_exists($sexo, $usuario->enum_sexo())){
      $mensagem = 'O valor escolhido para o sexo não é válido.';
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }

    /* Criptografias */
    $senha = $this->criptografar_senha_do_usuario($senha);

    /* Chave para operações via link (operação confirmar conta) */
    $chave = $this->criar_chave_para_operacoes_via_link();

    /* Momento atual sem fuso horário, pois no banco de dados armazeno sem fuso horário (timezone) */
    $sem_fuso_horario = new DateTimeZone('GMT');
    $objeto_date_time = new DateTime('now', $sem_fuso_horario);
    $momento_atual = $objeto_date_time->format('Y-m-d H:i:s');

    /* Criando objeto usuário */
    $usuario->set_nome_de_usuario($nome_de_usuario);
    $usuario->set_email($email);
    $usuario->set_senha($senha);
    $usuario->set_chave_para_operacoes_via_link($chave);
    $usuario->set_momento_do_cadastro($momento_atual);
    $usuario->set_sexo($sexo);

    /* Cadastrar usuário no banco de dados */
    $array_resultado = $cadastre_se_model->cadastrar_usuario($usuario);
    if(isset($array_resultado['mensagem_do_model'])){
      $mensagem = $array_resultado['mensagem_do_model'];
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->index(true);
    }else{
      $pk_usuario = $array_resultado['pk_usuario'];

      $servico_de_email = Servicos::email();

      $servico_de_email->setFrom('sistema@localhost.rds', 'Sistema Login CodeIgniter jQuery');

      $email = esc($email);
      $servico_de_email->setTo($email);

      $servico_de_email->setSubject('Confirmação de Conta');

      $texto = <<<HTML
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
  </head>
  <div>
    <span>Alguém, provavelmente você, se cadastrou no Sistema Login CodeIgniter jQuery,
    com este e-mail ($email) e IP {$_SERVER['REMOTE_ADDR']}.</span>
    <br/><br/>
    <span>Se você não fez este cadastro, ignore esta mensagem.</span>
    <br/><br/>
    <span>Acesse o link abaixo para confirmar sua conta:</span>
    <br/>
    <a href="http://localhost/pagina_inicial/confirmar_conta?id_do_usuario=$pk_usuario&chave=$chave">Confirmar Conta</a>
    <br/><br/>
    <span>--</span>
    <br/>
    <span>Sistema</span>
  </div>
</html>
HTML;

      $servico_de_email->setMessage($texto);

      $servico_de_email->send();

      $mensagem = 'Seu cadastro foi realizado com sucesso, confirme sua conta pelo link enviado';
      $mensagem .= " para o seu e-mail ($email).";
      $this->get_sessao()->set('mensagem_da_pagina_cadastre_se', $mensagem);
      $this->get_sessao()->remove('backup_do_formulario_da_pagina_cadastre_se');
      $this->index(true);
    }
  }

}
