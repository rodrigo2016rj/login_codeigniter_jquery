$(document).ready(function(){
  /* Funcionamento da div_mensagem_do_sistema_template */
  const $div_mensagem_do_sistema_template = $("#div_mensagem_do_sistema_template");
  const $span_mensagem_do_sistema_template = $("#span_mensagem_do_sistema_template");
  
  let ocultar_div_mensagem_do_sistema_template = true;
  
  $div_mensagem_do_sistema_template.click(function(){
    ocultar_div_mensagem_do_sistema_template = false;
  });
  
  if($span_mensagem_do_sistema_template.html() !== ""){
    if($div_mensagem_do_sistema_template.hasClass("tag_oculta")){
      $div_mensagem_do_sistema_template.removeClass("tag_oculta");
      
      $div_mensagem_do_sistema_template.css("top", 100);
      
      const largura_da_janela = $(window).width();
      const largura_da_div_mensagem_do_sistema_template = $div_mensagem_do_sistema_template.outerWidth(false);
      let posicao_horizontal = largura_da_janela / 2 - largura_da_div_mensagem_do_sistema_template / 2;
      posicao_horizontal = Math.floor(posicao_horizontal);
      $div_mensagem_do_sistema_template.css("right", posicao_horizontal);
      $div_mensagem_do_sistema_template.css("margin-left", posicao_horizontal);
    }
  }
  
  /* Funcionamento da div_entrar_template */
  const $div_entrar_template = $("#div_entrar_template");
  const $link_entrar_template = $("#link_entrar_template");
  
  let ocultar_div_entrar_template = true;
  
  $div_entrar_template.click(function(){
    ocultar_div_entrar_template = false;
  });
  
  $link_entrar_template.click(function(evento){
    evento.preventDefault();
    
    if($div_entrar_template.hasClass("tag_oculta")){
      ocultar_div_entrar_template = false;
      $div_entrar_template.removeClass("tag_oculta");
      
      const largura_da_janela = $(window).width();
      const largura_da_div_entrar_template = $div_entrar_template.outerWidth(false);
      let posicao_direita = largura_da_janela / 2 - largura_da_div_entrar_template / 2;
      $div_entrar_template.css("top", 100);
      $div_entrar_template.css("right", posicao_direita);
    }else{
      $div_entrar_template.addClass("tag_oculta");
    }
  });
  
  /* Ocultando popups */
  $(document).click(function(){
    if(ocultar_div_mensagem_do_sistema_template){
      $div_mensagem_do_sistema_template.addClass("tag_oculta");
    }else{
      ocultar_div_mensagem_do_sistema_template = true;
    }
    
    if(ocultar_div_entrar_template){
      $div_entrar_template.addClass("tag_oculta");
    }else{
      ocultar_div_entrar_template = true;
    }
  });
  
  /* Comportamento dos popups quando a janela é redimensionada */
  $(window).on("resize", function(){
    const largura_da_janela = $(window).width();
    
    if(!$div_mensagem_do_sistema_template.hasClass("tag_oculta")){
      $div_mensagem_do_sistema_template.addClass("tag_oculta");
      $div_mensagem_do_sistema_template.css("right", 10);
      $div_mensagem_do_sistema_template.css("margin-left", 10);
      $div_mensagem_do_sistema_template.removeClass("tag_oculta");
      
      const largura_da_div_mensagem_do_sistema_template = $div_mensagem_do_sistema_template.outerWidth(false);
      var posicao_horizontal = largura_da_janela / 2 - largura_da_div_mensagem_do_sistema_template / 2;
      posicao_horizontal = Math.floor(posicao_horizontal);
      $div_mensagem_do_sistema_template.css("right", posicao_horizontal);
      $div_mensagem_do_sistema_template.css("margin-left", posicao_horizontal);
    }
    
    if(!$div_entrar_template.hasClass("tag_oculta")){
      const largura_da_div_entrar_template = $div_entrar_template.outerWidth(false);
      var posicao_direita = largura_da_janela / 2 - largura_da_div_entrar_template / 2;
      $div_entrar_template.css("right", posicao_direita);
    }
  });
});
