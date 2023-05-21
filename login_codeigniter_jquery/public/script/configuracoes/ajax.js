$(document).ready(function(){
  const $span_escolher_fuso_horario = $("#span_escolher_fuso_horario");
  const $caixa_de_selecao_fuso_horario = $("#caixa_de_selecao_fuso_horario");
  const $span_escolher_visual = $("#span_escolher_visual");
  const $caixa_de_selecao_visual = $("#caixa_de_selecao_visual");
  const $span_editar_nome_de_usuario = $("#span_editar_nome_de_usuario");
  const $campo_editar_nome_de_usuario = $("#campo_editar_nome_de_usuario");
  const $botao_salvar_nome_de_usuario = $("#botao_salvar_nome_de_usuario");
  const $span_exibir_nao_exibir_sexo = $("#span_exibir_nao_exibir_sexo");
  const $caixa_de_checagem_exibir_nao_exibir_sexo = $("#caixa_de_checagem_exibir_nao_exibir_sexo");
  const $span_exibir_nao_exibir_email = $("#span_exibir_nao_exibir_email");
  const $caixa_de_checagem_exibir_nao_exibir_email = $("#caixa_de_checagem_exibir_nao_exibir_email");
  const $campo_senha_atual = $("#campo_senha_atual");
  const $campo_nova_senha = $("#campo_nova_senha");
  const $campo_nova_senha_novamente = $("#campo_nova_senha_novamente");
  const $botao_mudar_senha = $("#botao_mudar_senha");
  const $span_mudar_senha = $("#span_mudar_senha");
  
  let contador_ajax_da_acao_escolher_fuso_horario = 0;
  $caixa_de_selecao_fuso_horario.on("change", function(){
    $span_escolher_fuso_horario.html("Salvando...");
    
    let fuso_horario = $(this).val();
    let chave_anti_csrf = $("#campo_anti_csrf").val();
    
    contador_ajax_da_acao_escolher_fuso_horario++;
    const numero_desta_acao_ajax = contador_ajax_da_acao_escolher_fuso_horario;
    
    $.ajax({
      url: "configuracoes/escolher_fuso_horario_ajax",
      type: "POST",
      data: {fuso_horario: fuso_horario, chave_anti_csrf: chave_anti_csrf},
      success: function(resposta){
        if(numero_desta_acao_ajax >= contador_ajax_da_acao_escolher_fuso_horario){
          if(typeof resposta.mensagem_de_falha != "undefined"){
            $span_escolher_fuso_horario.html(resposta.mensagem_de_falha);
          }else{
            $span_escolher_fuso_horario.html("Salvo!");
          }
        }
      },
      error: function(erro){
        window.location.href = "configuracoes";
      },
      dataType: "json"
    });
  });
  
  let contador_ajax_da_acao_escolher_visual = 0;
  $caixa_de_selecao_visual.on("change", function(){
    $span_escolher_visual.html("Salvando...");
    
    let visual = $(this).val();
    let chave_anti_csrf = $("#campo_anti_csrf").val();
    
    contador_ajax_da_acao_escolher_visual++;
    const numero_desta_acao_ajax = contador_ajax_da_acao_escolher_visual;
    
    $.ajax({
      url: "configuracoes/escolher_visual_ajax",
      type: "POST",
      data: {visual: visual, chave_anti_csrf: chave_anti_csrf},
      success: function(resposta){
        if(numero_desta_acao_ajax >= contador_ajax_da_acao_escolher_visual){
          if(typeof resposta.mensagem_de_falha != "undefined"){
            $span_escolher_visual.html(resposta.mensagem_de_falha);
          }else{
            $span_escolher_visual.html("Salvo!");
          }
        }
      },
      error: function(erro){
        window.location.href = "configuracoes";
      },
      dataType: "json"
    });
  });
  
  let contador_ajax_da_acao_editar_nome_de_usuario = 0;
  $botao_salvar_nome_de_usuario.on("click", function(){
    $span_editar_nome_de_usuario.html("Salvando...");
    
    let nome_de_usuario = $campo_editar_nome_de_usuario.val();
    let chave_anti_csrf = $("#campo_anti_csrf").val();
    
    contador_ajax_da_acao_editar_nome_de_usuario++;
    const numero_desta_acao_ajax = contador_ajax_da_acao_editar_nome_de_usuario;
    
    $.ajax({
      url: "configuracoes/editar_nome_de_usuario_ajax",
      type: "POST",
      data: {nome_de_usuario: nome_de_usuario, chave_anti_csrf: chave_anti_csrf},
      success: function(resposta){
        if(numero_desta_acao_ajax >= contador_ajax_da_acao_editar_nome_de_usuario){
          if(typeof resposta.mensagem_de_falha != "undefined"){
            $span_editar_nome_de_usuario.html(resposta.mensagem_de_falha);
          }else{
            $span_editar_nome_de_usuario.html("Salvo!");
            $campo_editar_nome_de_usuario.val(resposta.nome_de_usuario);
          }
        }
      },
      error: function(erro){
        window.location.href = "configuracoes";
      },
      dataType: "json"
    });
  });
  
  let contador_ajax_da_acao_exibir_nao_exibir_sexo = 0;
  $caixa_de_checagem_exibir_nao_exibir_sexo.on("change", function(){
    $span_exibir_nao_exibir_sexo.html("Salvando...");
    
    let opcao_escolhida = "nao";
    if($(this).is(":checked")){
      opcao_escolhida = "sim";
    }
    
    let chave_anti_csrf = $("#campo_anti_csrf").val();
    
    contador_ajax_da_acao_exibir_nao_exibir_sexo++;
    const numero_desta_acao_ajax = contador_ajax_da_acao_exibir_nao_exibir_sexo;
    
    $.ajax({
      url: "configuracoes/exibir_nao_exibir_sexo_no_perfil_ajax",
      type: "POST",
      data: {opcao_escolhida: opcao_escolhida, chave_anti_csrf: chave_anti_csrf},
      success: function(resposta){
        if(numero_desta_acao_ajax >= contador_ajax_da_acao_exibir_nao_exibir_sexo){
          if(typeof resposta.mensagem_de_falha != "undefined"){
            $span_exibir_nao_exibir_sexo.html(resposta.mensagem_de_falha);
          }else{
            $span_exibir_nao_exibir_sexo.html("Salvo!");
          }
        }
      },
      error: function(erro){
        window.location.href = "configuracoes";
      },
      dataType: "json"
    });
  });
  
  let contador_ajax_da_acao_exibir_nao_exibir_email = 0;
  $caixa_de_checagem_exibir_nao_exibir_email.on("change", function(){
    $span_exibir_nao_exibir_email.html("Salvando...");
    
    let opcao_escolhida = "nao";
    if($(this).is(":checked")){
      opcao_escolhida = "sim";
    }
    
    let chave_anti_csrf = $("#campo_anti_csrf").val();
    
    contador_ajax_da_acao_exibir_nao_exibir_email++;
    const numero_desta_acao_ajax = contador_ajax_da_acao_exibir_nao_exibir_email;
    
    $.ajax({
      url: "configuracoes/exibir_nao_exibir_email_no_perfil_ajax",
      type: "POST",
      data: {opcao_escolhida: opcao_escolhida, chave_anti_csrf: chave_anti_csrf},
      success: function(resposta){
        if(numero_desta_acao_ajax >= contador_ajax_da_acao_exibir_nao_exibir_email){
          if(typeof resposta.mensagem_de_falha != "undefined"){
            $span_exibir_nao_exibir_email.html(resposta.mensagem_de_falha);
          }else{
            $span_exibir_nao_exibir_email.html("Salvo!");
          }
        }
      },
      error: function(erro){
        window.location.href = "configuracoes";
      },
      dataType: "json"
    });
  });
  
  let contador_ajax_da_acao_mudar_senha = 0;
  $botao_mudar_senha.on("click", function(){
    $span_mudar_senha.html("Salvando...");
    
    let senha_atual = $campo_senha_atual.val();
    let nova_senha = $campo_nova_senha.val();
    let nova_senha_novamente = $campo_nova_senha_novamente.val();
    
    let chave_anti_csrf = $("#campo_anti_csrf").val();
    
    contador_ajax_da_acao_mudar_senha++;
    const numero_desta_acao_ajax = contador_ajax_da_acao_mudar_senha;
    
    $.ajax({
      url: "configuracoes/mudar_senha_ajax",
      type: "POST",
      data: {senha_atual: senha_atual, nova_senha: nova_senha, 
             nova_senha_novamente: nova_senha_novamente, chave_anti_csrf: chave_anti_csrf},
      success: function(resposta){
        if(numero_desta_acao_ajax >= contador_ajax_da_acao_mudar_senha){
          if(typeof resposta.mensagem_de_falha != "undefined"){
            $span_mudar_senha.html(resposta.mensagem_de_falha);
          }else{
            $span_mudar_senha.html("Salvo!");
          }
        }
      },
      error: function(erro){
        window.location.href = "configuracoes";
      },
      dataType: "json"
    });
  });
  
});
