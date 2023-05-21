<?php

namespace App\Controllers;

use Smarty;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\TemplateModel;
use DateTime;
use DateTimeZone;
use Exception;

class TemplateController extends Controller{
  private $smarty; //Armazena objeto do smarty.
  private $sessao; //Armazena a sessão.
  private $usuario_logado; //Armazena informações do usuário logado.

  public function initController(RequestInterface $requisicao, ResponseInterface $response,
    LoggerInterface $logger){
    parent::initController($requisicao, $response, $logger);

    $this->sessao = session();

    $this->smarty = new Smarty;

    /* Colocando valores iniciais nas variáveis do smarty para não ficarem undefined no HTML */
    $this->smarty->assign('pagina_template', '');
    $this->smarty->assign('id_referencia_template', '');
    $this->smarty->assign('visual_escolhido_template', 'tema_inicial');
    $this->smarty->assign('mensagem_template', '');

    /* Colocando campo anti csrf no formulário e no array de chaves do template */
    $chave_anti_csrf = $this->criar_hash_da_chave_anti_csrf();
    $this->smarty->assign('chave_anti_csrf_template', $chave_anti_csrf);
    if(!$this->get_sessao()->has('chaves_anti_csrf_template')){
      $this->get_sessao()->set('chaves_anti_csrf_template', array());
    }
    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_template');
    $chaves_anti_csrf[] = $chave_anti_csrf;
    $this->get_sessao()->set('chaves_anti_csrf_template', $chaves_anti_csrf);

    $this->conferir_se_o_usuario_esta_logado();
    if($this->usuario_logado !== null){
      $visual_escolhido = $this->usuario_logado->get_visual();
      $visuais_permitidos = $this->criar_array_de_temas_visuais();
      if(isset($visuais_permitidos[$visual_escolhido])){
        $this->smarty->assign('visual_escolhido_template', $visual_escolhido);
      }
    }
  }

  protected final function get_smarty(){
    return $this->smarty;
  }

  protected final function get_sessao(){
    return $this->sessao;
  }

  protected final function get_usuario_logado(){
    return $this->usuario_logado;
  }

  /** ---------------------------------------------------------------------------------------------
    Confere se o usuário está logado e indica para o smarty. */
  protected final function conferir_se_o_usuario_esta_logado(){
    $template_model = new TemplateModel();

    $usuario_esta_logado_template = false;
    $this->usuario_logado = null;
    $id_do_usuario_logado_template = null;

    if($this->sessao->has('nome_de_usuario')){
      $nome_de_usuario = $this->sessao->get('nome_de_usuario');
      $array_resultado = $template_model->seleciona_usuario_pelo_nome_de_usuario($nome_de_usuario);

      if(isset($array_resultado['mensagem_do_model'])){
        //Aqui eu posso colocar para guardar um registro de tentativa suspeita de login por exemplo.
      }else{
        $usuario_esta_logado_template = true;
        $this->usuario_logado = $array_resultado[0];
        $id_do_usuario_logado_template = $this->usuario_logado->get_pk_usuario();
      }
    }

    $this->smarty->assign('usuario_esta_logado_template', $usuario_esta_logado_template);
    $this->smarty->assign('id_do_usuario_logado_template', $id_do_usuario_logado_template);
  }

  /** ---------------------------------------------------------------------------------------------
    Função padrão para o usuário entrar na conta (login). */
  protected final function entrar_padronizado(){
    $template_model = new TemplateModel();

    $requisicao = service('request');
    $nome_de_usuario = trim($requisicao->getPost('nome_de_usuario') ?? '');
    $senha = $requisicao->getPost('senha');

    $chaves_anti_csrf = $this->get_sessao()->get('chaves_anti_csrf_template');
    $chave_anti_csrf = $requisicao->getPost('chave_anti_csrf');

    if(!in_array($chave_anti_csrf, $chaves_anti_csrf)){
      $mensagem = 'O formulário havia expirado, tente novamente.';
      $this->get_sessao()->set('mensagem_template', $mensagem);
      return false;
    }else{
      $chaves_anti_csrf = array_diff($chaves_anti_csrf, [$chave_anti_csrf]);
      $this->get_sessao()->set('chaves_anti_csrf_template', $chaves_anti_csrf);
    }

    if(empty($nome_de_usuario)){
      $mensagem = 'O campo nome de usuário precisa ser preenchido.';
      $this->sessao->set('mensagem_template', $mensagem);
      return false;
    }
    if(empty($senha)){
      $mensagem = 'O campo senha precisa ser preenchido.';
      $this->sessao->set('mensagem_template', $mensagem);
      return false;
    }

    $array_resultado = $template_model->seleciona_senha_do_usuario_pelo_nome_de_usuario($nome_de_usuario);

    if(isset($array_resultado['mensagem_do_model'])){
      $this->sessao->set('mensagem_template', $array_resultado['mensagem_do_model']);
      return false;
    }else{
      $usuario = $array_resultado[0];
      if(password_verify($senha, $usuario->get_senha())){
        $this->sessao->set('nome_de_usuario', $nome_de_usuario);
      }else{
        $mensagem = 'A senha digitada não está correta.';
        $this->sessao->set('mensagem_template', $mensagem);
        return false;
      }
    }

    return true;
  }

  /** ---------------------------------------------------------------------------------------------
    Função padrão para o usuário sair da conta (logout). */
  protected final function sair_padronizado(){
    $this->sessao->remove('nome_de_usuario');
  }

  /** ---------------------------------------------------------------------------------------------
    Criptografa a senha do usuário. */
  protected final function criptografar_senha_do_usuario($senha){
    $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

    return $senha_criptografada;
  }

  /** ---------------------------------------------------------------------------------------------
    Cria hash para a chave anti csrf. */
  protected final function criar_hash_da_chave_anti_csrf(){
    //Baseado no método generateHash da framework CodeIgniter.
    return bin2hex(random_bytes(17));
  }

  /** ---------------------------------------------------------------------------------------------
    Acrescenta quebras de linha no padrão XHTML. */
  protected function acrescentar_quebras_de_linha_xhtml($texto){
    //Armazena em array todos os padrões de quebra de linha de sistemas operacionais diferentes
    $tipos_de_quebras_de_sistemas_operacionais = array("\r\n", "\r", "\n");
    //Substitui quebras de linha presentes na string por: termina parágrafo </p> começa parágrafo <p>
    $texto_modificado = str_replace($tipos_de_quebras_de_sistemas_operacionais, '</p><p>', $texto);
    //Substitui parágrafo vazio por: quebra de linha <br/>
    $texto_resultante = str_replace('<p></p>', '<br/>', $texto_modificado);
    //Retorna o texto resultante dentro da tag <p></p>
    return "<p>$texto_resultante</p>";
  }

  /** ---------------------------------------------------------------------------------------------
    Converte dd/MM/yyyy para: yyyy-MM-dd */
  protected function converter_para_data_do_sql($data){
    if(!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data)){
      //Caso não venha no formato certo, retorna a string sem conversão.
      return $data;
    }
    $dia = substr($data, 0, 2);
    $mes = substr($data, 3, 2);
    $ano = substr($data, 6, 4);
    return "$ano-$mes-$dia";
  }

  /** ---------------------------------------------------------------------------------------------
    Converte xxhyy para: xx:yy:zz */
  protected function converter_para_horario_do_sql($horario){
    if(!preg_match('/^\d{2}h\d{2}$/', $horario)){
      //Caso não venha no formato certo, retorna a string sem conversão.
      return $horario;
    }
    $horas = substr($horario, 0, 2);
    $minutos = substr($horario, 3, 2);
    return "$horas:$minutos:00";
  }

  /** ---------------------------------------------------------------------------------------------
    Converte xxhyy dd/MM/yyyy para: yyyy-MM-dd xx:yy:zz */
  protected function converter_para_horario_data_do_sql($string){
    if(!preg_match('/^\d{2}h\d{2} \d{2}\/\d{2}\/\d{4}$/', $string)){
      //Caso não venha no formato certo, retorna a string sem conversão.
      return $string;
    }
    $horas = substr($string, 0, 2);
    $minutos = substr($string, 3, 2);
    $dia = substr($string, 6, 2);
    $mes = substr($string, 9, 2);
    $ano = substr($string, 12, 4);
    return "$ano-$mes-$dia $horas:$minutos:00";
  }

  /** ---------------------------------------------------------------------------------------------
    Converte yyyy-MM-dd para: dd/MM/yyyy */
  protected function converter_para_data_do_html($data){
    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)){
      //Caso não venha no formato certo, retorna a string sem conversão.
      return $data;
    }
    $ano = substr($data, 0, 4);
    $mes = substr($data, 5, 2);
    $dia = substr($data, 8, 2);
    return "$dia/$mes/$ano";
  }

  /** ---------------------------------------------------------------------------------------------
    Converte xx:yy:zz para: xxhyy */
  protected function converter_para_horario_do_html($horario){
    if(!preg_match('/^\d{2}:\d{2}:\d{2}$/', $horario)){
      //Caso não venha no formato certo, retorna a string sem conversão.
      return $horario;
    }
    $horas = substr($horario, 0, 2);
    $minutos = substr($horario, 3, 2);
    return $horas.'h'.$minutos;
  }

  /** ---------------------------------------------------------------------------------------------
    Converte yyyy-MM-dd xx:yy:zz para: dd/MM/yyyy às xxhyy */
  protected function converter_para_horario_data_do_html($string){
    if(!preg_match('/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/', $string)){
      //Caso não venha no formato certo, retorna a string sem conversão.
      return $string;
    }
    $ano = substr($string, 0, 4);
    $mes = substr($string, 5, 2);
    $dia = substr($string, 8, 2);
    $horas = substr($string, 11, 2);
    $minutos = substr($string, 14, 2);
    return "$dia/$mes/$ano às ".$horas.'h'.$minutos;
  }

  /** ---------------------------------------------------------------------------------------------
    Coloca no fuso horário do usuário logado */
  protected function colocar_no_fuso_horario_do_usuario_logado($string){
    if(!preg_match('/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/', $string)){
      //Caso não venha no formato certo, retorna a string do jeito que veio.
      return $string;
    }
    if($this->usuario_logado === null){
      //Caso não tenha usuário logado, retorna a string do jeito que veio.
      return $string;
    }
    try{
      $sem_fuso_horario = new DateTimeZone('GMT');
      $objeto_date_time = new DateTime($string, $sem_fuso_horario);

      $fuso_horario_do_usuario = $this->usuario_logado->get_fuso_horario();
      $fuso_horario_do_usuario = new DateTimeZone($fuso_horario_do_usuario);

      $objeto_date_time->setTimeZone($fuso_horario_do_usuario);

      $string = $objeto_date_time->format('Y-m-d H:i:s');
    }catch(Exception $excecao){
      //Aqui posso registrar que há um fuso horário errado na coluna fuso_horario do banco de dados.
    }

    return $string;
  }

  /** ---------------------------------------------------------------------------------------------
    Cria array com os quatro fuso horários do Brasil mais os outros aceitos no PHP pelo local. */
  protected function criar_array_de_fuso_horarios(){
    $fuso_horarios = array();

    $fuso_horarios['-0500'] = 'Horário do Acre';
    $fuso_horarios['-0400'] = 'Horário do Amazonas';
    $fuso_horarios['-0300'] = 'Horário de Brasília';
    $fuso_horarios['-0200'] = 'Horário de Fernando de Noronha';

    $timezones = timezone_identifiers_list();
    foreach($timezones as $timezone){
      $fuso_horario = str_replace('Africa/', 'África/', $timezone);
      $fuso_horario = str_replace('America/', 'América/', $fuso_horario);
      $fuso_horario = str_replace('Asia/', 'Ásia/', $fuso_horario);
      $fuso_horario = str_replace('_', ' ', $fuso_horario);
      $fuso_horario = str_replace('/', ' / ', $fuso_horario);
      $fuso_horarios[$timezone] = $fuso_horario;
    }
    $fuso_horarios['America/Sao_Paulo'] = 'América / São Paulo';
    $fuso_horarios['America/Belem'] = 'América / Belém';

    return $fuso_horarios;
  }

  /** ---------------------------------------------------------------------------------------------
    Cria array com os temas visuais já criados na pasta css deste sistema. */
  protected function criar_array_de_temas_visuais(){
    $visuais['tema_inicial'] = 'Tema Inicial';
    $visuais['tema_sofisticado'] = 'Tema Sofisticado';
    return $visuais;
  }

}
