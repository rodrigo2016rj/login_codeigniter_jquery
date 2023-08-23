$(document).ready(function(){
  let durante_requisicao_ajax = false;
  
  const $campo_filtro_nome_de_usuario = $("#campo_filtro_nome_de_usuario");
  let filtro_nome_de_usuario = $campo_filtro_nome_de_usuario.val();
  
  const $campo_filtro_email_do_usuario = $("#campo_filtro_email_do_usuario");
  let filtro_email_do_usuario = $campo_filtro_email_do_usuario.val();
  
  const $caixa_de_selecao_filtro_tipo_de_usuario = $("#caixa_de_selecao_filtro_tipo_de_usuario");
  let filtro_tipo_de_usuario = $caixa_de_selecao_filtro_tipo_de_usuario.val();
  
  const $caixa_de_selecao_quantidade_por_segmento = $("#caixa_de_selecao_quantidade_por_segmento");
  let quantidade_por_segmento = $caixa_de_selecao_quantidade_por_segmento.val();
  
  const $campo_ordenacao = $("#campo_ordenacao");
  let ordenacao = $campo_ordenacao.val();
  
  const $corpo_da_tabela = $("#div_local_da_tabela_usuarios>#tabela_usuarios>tbody");
  const $div_link_carregar_mais_registros = $("#div_link_carregar_mais_registros");
  const $link_carregar_mais_registros = $("#link_carregar_mais_registros");
  const $span_segmento_atual = $("#span_segmento_atual");
  const $div_mensagem_de_carregamento_da_tabela = $("#div_mensagem_de_carregamento_da_tabela");
  const $div_mensagem_de_final_da_tabela = $("#div_mensagem_de_final_da_tabela");
  let segmento = $span_segmento_atual.text();
  
  $link_carregar_mais_registros.click(function(evento){
    evento.preventDefault();
    if(!durante_requisicao_ajax && !isNaN(segmento)){
      segmento = parseInt(segmento);
      segmento++;
      if(segmento >= 1){
        $div_link_carregar_mais_registros.addClass("tag_oculta");
        $div_mensagem_de_carregamento_da_tabela.removeClass("tag_oculta");
        $div_mensagem_de_final_da_tabela.addClass("tag_oculta");
        
        durante_requisicao_ajax = true;
        $.ajax({
          url: "usuarios/mostrar_usuarios_ajax",
          type: "GET",
          data: {filtro_nome_de_usuario: filtro_nome_de_usuario, 
                 filtro_email_do_usuario: filtro_email_do_usuario, 
                 filtro_tipo_de_usuario: filtro_tipo_de_usuario, 
                 quantidade_por_segmento: quantidade_por_segmento, 
                 ordenacao: ordenacao, segmento: segmento},
          success: function(resposta){
            $div_link_carregar_mais_registros.removeClass("tag_oculta");
            $div_mensagem_de_carregamento_da_tabela.addClass("tag_oculta");
            if(typeof resposta.mensagem_de_falha != "undefined"){
              alert(resposta.mensagem_de_falha);
              window.location.href = "usuarios";
            }else if(resposta.linhas_da_tabela !== ""){
              $corpo_da_tabela.append(resposta.linhas_da_tabela);
              $span_segmento_atual.html(segmento);
              
              /* Ajeitando as classes ímpar e par das linhas da tabela */
              const $linhas_da_tabela = $corpo_da_tabela.children("tr");
              var classe = "impar";
              $linhas_da_tabela.each(function(){
                $(this).attr("class", classe);
                if(classe == "impar"){
                  classe = "par";
                }else{
                  classe = "impar";
                }
              });
            }else{
              $div_link_carregar_mais_registros.addClass("tag_oculta");
              $div_mensagem_de_final_da_tabela.removeClass("tag_oculta");
            }
          },
          complete: function(){
            durante_requisicao_ajax = false;
          },
          dataType:"json"
        });
      }
    }
  });
});
