$(window).on("load", function(){
  /* Ajustando a div_mensagem para que cada linha de texto tenha mais ou menos mesmo tamanho */
  const $div_mensagem = $("#div_mensagem");
  const $span_mensagem = $("#span_mensagem");
  
  if($div_mensagem.css("white-space") === "nowrap"){
    const largura_da_div_mensagem = $div_mensagem.width();
    const largura_do_span_mensagem = $span_mensagem.width();
    const texto_da_mensagem = $span_mensagem.text();
    
    let nova_largura = 0;
    if(largura_do_span_mensagem > largura_da_div_mensagem){
      const quantidade_de_linhas = Math.ceil(largura_do_span_mensagem / largura_da_div_mensagem);
      
      nova_largura = Math.ceil(texto_da_mensagem.length / quantidade_de_linhas) + "ex";
      
    }else{
      nova_largura = largura_do_span_mensagem + 1 + "px";
    }
    $div_mensagem.css("white-space", "normal");
    $div_mensagem.css("max-width", nova_largura);
  }
});
