$(document).ready(function(){
  const $rotulo_da_aba = $(".rotulo_da_aba");
  const $div_rotulo_da_aba_preferencias = $("#div_rotulo_da_aba_preferencias");
  const $div_rotulo_da_aba_perfil = $("#div_rotulo_da_aba_perfil");
  const $div_rotulo_da_aba_seguranca = $("#div_rotulo_da_aba_seguranca");
  
  const $conteudo_da_aba = $(".conteudo_da_aba");
  const $div_aba_preferencias = $("#div_aba_preferencias");
  const $div_aba_perfil = $("#div_aba_perfil");
  const $div_aba_seguranca = $("#div_aba_seguranca");
  
  let aba_inicial = $("#span_aba_inicial").text();
  $rotulo_da_aba.each(function(){
    $(this).removeClass("rotulo_da_aba_selecionada");
  });
  $("#div_rotulo_da_"+aba_inicial).addClass("rotulo_da_aba_selecionada");
  
  $conteudo_da_aba.each(function(){
    $(this).addClass("tag_oculta");
  });
  $("#div_"+aba_inicial).removeClass("tag_oculta");
  
  $div_rotulo_da_aba_preferencias.click(function(){
    $rotulo_da_aba.each(function(){
      $(this).removeClass("rotulo_da_aba_selecionada");
    });
    $div_rotulo_da_aba_preferencias.addClass("rotulo_da_aba_selecionada");
    
    $conteudo_da_aba.each(function(){
      $(this).addClass("tag_oculta");
    });
    $div_aba_preferencias.removeClass("tag_oculta");
  });
  
  $div_rotulo_da_aba_perfil.click(function(){
    $rotulo_da_aba.each(function(){
      $(this).removeClass("rotulo_da_aba_selecionada");
    });
    $div_rotulo_da_aba_perfil.addClass("rotulo_da_aba_selecionada");
    
    $conteudo_da_aba.each(function(){
      $(this).addClass("tag_oculta");
    });
    $div_aba_perfil.removeClass("tag_oculta");
  });
  
  $div_rotulo_da_aba_seguranca.click(function(){
    $rotulo_da_aba.each(function(){
      $(this).removeClass("rotulo_da_aba_selecionada");
    });
    $div_rotulo_da_aba_seguranca.addClass("rotulo_da_aba_selecionada");
    
    $conteudo_da_aba.each(function(){
      $(this).addClass("tag_oculta");
    });
    $div_aba_seguranca.removeClass("tag_oculta");
  });
});
