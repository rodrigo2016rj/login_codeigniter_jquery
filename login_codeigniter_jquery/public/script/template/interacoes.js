$(window).on("load", function(){
  /* Ajustando altura do tronco para preencher a parte vertical visível da tela */
  const $body = $("body");
  const $div_cabecalho_template = $("#div_cabecalho_template");
  const $div_tronco_template = $("#div_tronco_template");
  
  const altura_minima_da_janela = window.innerHeight;
  
  let altura_minima_do_tronco = altura_minima_da_janela;
  
  const margem_de_cima_do_body = $body.css("margin-top");
  const borda_de_cima_do_body = $body.css("border-top-width");
  const padding_de_cima_do_body = $body.css("padding-top");
  altura_minima_do_tronco -= parseInt(margem_de_cima_do_body, 10);
  altura_minima_do_tronco -= parseInt(borda_de_cima_do_body, 10);
  altura_minima_do_tronco -= parseInt(padding_de_cima_do_body, 10);
  
  altura_minima_do_tronco -= $div_cabecalho_template.outerHeight(true);
  
  const altura_do_tronco_todo = $div_tronco_template.outerHeight(true);
  const altura_interna_do_tronco = $div_tronco_template.height();
  var diferenca = altura_do_tronco_todo - altura_interna_do_tronco;
  altura_minima_do_tronco -= diferenca;
  
  $div_tronco_template.css("min-height", altura_minima_do_tronco);
  
  /* Removendo o foco dos botões quando o cursor sai de cima deles e após o clique */
  const $botoes = $('input[type="submit"], input[type="reset"], button');
  
  $botoes.mouseleave(function(){
    $(this).blur();
  });
  $botoes.click(function(){
    $(this).blur();
  });
});
