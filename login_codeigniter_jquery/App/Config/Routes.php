<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
/* Página Padrão */
$routes->get('/', 'PaginaInicialController::index');

/* Página Inicial */
$routes->get('/pagina_inicial', 'PaginaInicialController::index');
$routes->post('/pagina_inicial/entrar', 'PaginaInicialController::entrar');
$routes->get('/pagina_inicial/sair', 'PaginaInicialController::sair');
$routes->get('/pagina_inicial/confirmar_conta', 'PaginaInicialController::confirmar_conta');

/* Cadastrar-se */
$routes->get('/cadastre-se', 'CadastreSeController::index');
$routes->post('/cadastre-se/entrar', 'CadastreSeController::entrar');
$routes->get('/cadastre-se/sair', 'CadastreSeController::sair');
$routes->post('/cadastre-se/cadastrar', 'CadastreSeController::cadastrar');

/* Usuários */
$routes->get('/usuarios', 'UsuariosController::index');
$routes->post('/usuarios/entrar', 'UsuariosController::entrar');
$routes->get('/usuarios/sair', 'UsuariosController::sair');
$routes->get('/usuarios/mostrar_usuarios_ajax', 'UsuariosController::mostrar_usuarios_ajax');

/* Perfil */
$routes->get('/perfil', 'PerfilController::index');
$routes->post('/perfil/entrar', 'PerfilController::entrar');
$routes->get('/perfil/sair', 'PerfilController::sair');

/* Editar tipo de usuário */
$routes->get('/editar_tipo_de_usuario', 'EditarTipoDeUsuarioController::index');
$routes->post('/editar_tipo_de_usuario/entrar', 'EditarTipoDeUsuarioController::entrar');
$routes->get('/editar_tipo_de_usuario/sair', 'EditarTipoDeUsuarioController::sair');
$routes->post('/editar_tipo_de_usuario/editar', 'EditarTipoDeUsuarioController::editar');

/* Configurações */
$routes->get('/configuracoes', 'ConfiguracoesController::index');
$routes->post('/configuracoes/entrar', 'ConfiguracoesController::entrar');
$routes->get('/configuracoes/sair', 'ConfiguracoesController::sair');
$routes->post('/configuracoes/escolher_fuso_horario_ajax', 'ConfiguracoesController::escolher_fuso_horario_ajax');
$routes->post('/configuracoes/escolher_visual_ajax', 'ConfiguracoesController::escolher_visual_ajax');
$routes->post('/configuracoes/editar_nome_de_usuario_ajax', 'ConfiguracoesController::editar_nome_de_usuario_ajax');
$routes->post('/configuracoes/exibir_nao_exibir_sexo_no_perfil_ajax', 'ConfiguracoesController::exibir_nao_exibir_sexo_no_perfil_ajax');
$routes->post('/configuracoes/exibir_nao_exibir_email_no_perfil_ajax', 'ConfiguracoesController::exibir_nao_exibir_email_no_perfil_ajax');
$routes->post('/configuracoes/mudar_senha_ajax', 'ConfiguracoesController::mudar_senha_ajax');
