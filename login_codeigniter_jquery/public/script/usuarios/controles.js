$(document).ready(function(){
  const $form_opcoes_de_filtro = $("#form_opcoes_de_filtro");
  const $campo_filtro_nome_de_usuario = $("#campo_filtro_nome_de_usuario");
  const $campo_filtro_email_do_usuario = $("#campo_filtro_email_do_usuario");
  const $caixa_de_selecao_filtro_tipo_de_usuario = $("#caixa_de_selecao_filtro_tipo_de_usuario");
  const $caixa_de_selecao_quantidade_por_segmento = $("#caixa_de_selecao_quantidade_por_segmento");
  const $campo_ordenacao = $("#campo_ordenacao");
  const $span_ordenacao = $(".span_ordenacao");
  
  /* Guardando backup dos valores iniciais dos filtros */
  const backup_filtro_nome_de_usuario = $campo_filtro_nome_de_usuario.val();
  const backup_filtro_email_do_usuario = $campo_filtro_email_do_usuario.val();
  const backup_filtro_tipo_de_usuario = $caixa_de_selecao_filtro_tipo_de_usuario.val();
  const backup_quantidade_por_segmento = $caixa_de_selecao_quantidade_por_segmento.val();
  const backup_ordenacao = $campo_ordenacao.val();
  
  /* Comportamento do botão buscar */
  const $botao_buscar = $("#botao_buscar");
  
  $botao_buscar.click(function(evento){
    evento.preventDefault();
    
    let quantidade_nao_enviada = 0;
    
    if($campo_filtro_nome_de_usuario.val() == ""){
      $campo_filtro_nome_de_usuario.removeAttr("name");
      quantidade_nao_enviada++;
    }
    if($campo_filtro_email_do_usuario.val() == "" || 
       typeof($campo_filtro_email_do_usuario.val()) == "undefined"){
      $campo_filtro_email_do_usuario.removeAttr("name");
      quantidade_nao_enviada++;
    }
    if($caixa_de_selecao_filtro_tipo_de_usuario.val() == "todos"){
      $caixa_de_selecao_filtro_tipo_de_usuario.removeAttr("name");
      quantidade_nao_enviada++;
    }
    if($caixa_de_selecao_quantidade_por_segmento.val() == "padrao"){
      $caixa_de_selecao_quantidade_por_segmento.removeAttr("name");
      quantidade_nao_enviada++;
    }
    if($campo_ordenacao.val() == "padrao"){
      $campo_ordenacao.removeAttr("name");
      quantidade_nao_enviada++;
    }
    
    if(quantidade_nao_enviada == 5){
      window.location.href = "/usuarios";
    }else{
      $form_opcoes_de_filtro.submit();
    }
  });
  
  /* Comportamento do botão limpar */
  const $botao_limpar = $("#botao_limpar");
  
  $botao_limpar.click(function(evento){
    evento.preventDefault();
    
    $campo_filtro_nome_de_usuario.val("").change().removeAttr("name");
    $campo_filtro_email_do_usuario.val("").change().removeAttr("name");
    $caixa_de_selecao_filtro_tipo_de_usuario.val("todos").change().removeAttr("name");
    $caixa_de_selecao_quantidade_por_segmento.val("padrao").change().removeAttr("name");
    $campo_ordenacao.val("padrao").change().removeAttr("name");
    
    window.location.href = "/usuarios";
  });
  
  /* Ordenação */
  $span_ordenacao.click(function(){
    var texto = $(this).text();
    
    /* Os valores dos filtros devem ser aqueles utilizados na busca ao invés do que foi digitado após */
    $campo_filtro_nome_de_usuario.val(backup_filtro_nome_de_usuario).change();
    $campo_filtro_email_do_usuario.val(backup_filtro_email_do_usuario).change();
    $caixa_de_selecao_filtro_tipo_de_usuario.val(backup_filtro_tipo_de_usuario).change();
    $caixa_de_selecao_quantidade_por_segmento.val(backup_quantidade_por_segmento).change();
    $campo_ordenacao.val(backup_ordenacao).change();
    
    /* Trocando o valor após o clique */
    switch (texto){
      case "Nome de Usuário":
        $campo_ordenacao.val("nome_de_usuario_em_ordem_alfabetica").change();
        break;
      case "Nome de Usuário ▲":
        $campo_ordenacao.val("nome_de_usuario_em_ordem_alfabetica_inversa").change();
        break;
      case "Nome de Usuário ▼":
        $campo_ordenacao.val("padrao").change();
        break;
      case "E-mail":
        $campo_ordenacao.val("email_em_ordem_alfabetica").change();
        break;
      case "E-mail ▲":
        $campo_ordenacao.val("email_em_ordem_alfabetica_inversa").change();
        break;
      case "E-mail ▼":
        $campo_ordenacao.val("padrao").change();
        break;
      case "Cadastrado em":
        $campo_ordenacao.val("momento_do_cadastro_em_ordem_cronologica").change();
        break;
      case "Cadastrado em ▲":
        $campo_ordenacao.val("momento_do_cadastro_em_ordem_cronologica_inversa").change();
        break;
      case "Cadastrado em ▼":
        $campo_ordenacao.val("padrao").change();
        break;
      case "Tipo":
        $campo_ordenacao.val("tipo_em_ordem_alfabetica").change();
        break;
      case "Tipo ▲":
        $campo_ordenacao.val("tipo_em_ordem_alfabetica_inversa").change();
        break;
      case "Tipo ▼":
        $campo_ordenacao.val("padrao").change();
        break;
    }
    
    /* Não enviar valores vazios ou padrões */
    let quantidade_nao_enviada = 0;
    
    if($campo_filtro_nome_de_usuario.val() == ""){
      $campo_filtro_nome_de_usuario.removeAttr("name");
      quantidade_nao_enviada++;
    }
    if($campo_filtro_email_do_usuario.val() == "" || 
       typeof($campo_filtro_email_do_usuario.val()) == "undefined"){
      $campo_filtro_email_do_usuario.removeAttr("name");
      quantidade_nao_enviada++;
    }
    if($caixa_de_selecao_filtro_tipo_de_usuario.val() == "todos"){
      $caixa_de_selecao_filtro_tipo_de_usuario.removeAttr("name");
      quantidade_nao_enviada++;
    }
    if($caixa_de_selecao_quantidade_por_segmento.val() == "padrao"){
      $caixa_de_selecao_quantidade_por_segmento.removeAttr("name");
      quantidade_nao_enviada++;
    }
    if($campo_ordenacao.val() == "padrao"){
      $campo_ordenacao.removeAttr("name");
      quantidade_nao_enviada++;
    }
    
    if(quantidade_nao_enviada == 5){
      window.location.href = "/usuarios";
    }else{
      $form_opcoes_de_filtro.submit();
    }
  });

});
