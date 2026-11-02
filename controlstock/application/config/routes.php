<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "sitio";

$route['404_override'] = '';

$route['enviar_contacto'] = "sitio/enviar_contacto";

$route['enviar_revendedores'] = "sitio/enviar_revendedores";

$route['enviar_consulta'] = "sitio/enviar_consulta";

$route['revendedores'] = "sitio/revendedores";

$route['imprenta_mar_del_plata'] = "sitio/imprenta_mar_del_plata";

$route['clientes'] = "sitio/clientes";

$route['detalles/(:any)'] = "sitio/detalles/$1";

$route['productos/detalles/(:any)/(:any)/(:num)/(:any)'] = "sitio/detalles/$1/$2/$3/$4";

$route['productos/(:any)'] = "sitio/productos/$1";

$route['productos/(:any)/marcas/(:any)'] = "sitio/productos/$1/marcas/$2";

$route['busqueda/(:any)'] = "sitio/busqueda/$1";

$route['busqueda/(:any)/(:any)/(:num)'] = "sitio/busqueda/$1/$2/$3";

$route['consulta/(:any)'] = "sitio/consulta/$1";

$route['login'] = "sitio/login";

$route['login_mayorista'] = "sitio/login_mayorista";

$route['logout'] = "sitio/logout";

$route['logout_mayorista'] = "sitio/logout_mayorista";

$route['registrarse'] = "sitio/registrarse";

$route['pedidos_todo_el_pais'] = "sitio/pedidos_todo_el_pais";

$route['loginProc'] = "sitio/login_proc";

$route['loginProc_mayorista'] = "sitio/login_proc_mayorista";

$route['registrarseProc'] = "sitio/registrarse_proc";

$route['do_upload'] = "sitio/do_upload";

$route['archivos'] = "sitio/archivos";

$route['calculadorprecios'] = "sitio/calculadorprecios";

$route['pdfpoint'] = "sitio/pdfpoint";

$route['pedidoRealizado'] = "sitio/pedido_realizado";

$route['pedido_fuera_mdp_realizado'] = "sitio/pedido_fuera_mdp_realizado";

$route['pedido_dentro_mdp_realizado'] = "sitio/pedido_dentro_mdp_realizado";

$route['hacerPedido'] = "sitio/hacer_pedido";

$route['subir_pedido'] = "sitio/subir_pedido";

$route['recordatorio'] = "sitio/recordatorio";

$route['enviar_recordatorio'] = "sitio/enviar_recordatorio";

$route['pedidoRechazado'] = "sitio/pedido_rechazado";

$route['blog'] = "sitio/blog";

$route['vista_blog/(:any)'] = "sitio/vista_blog/$1";

$route['comprar_fuera_mdp'] = "sitio/comprar_fuera_mdp";

$route['comprar_dentro_mdp'] = "sitio/comprar_dentro_mdp";

$route['mayorista/(:any)'] = "sitio/mayorista/$1";

$route['mayorista/(:any)/(:any)/(:num)'] = "sitio/mayorista/$1/$2/$3";

$route['mayorista'] = "sitio/mayorista";

$route['inicio'] = "sitio/inicio";

$route['modulo_inicial'] = "sitio/modulo_inicial";

$route['nueva_venta'] = "sitio/nueva_venta";

$route['enviar_pedido'] = "sitio/enviar_pedido";

$route['pedidos'] = "sitio/pedidos";

$route['pedido_detalles/(:any)'] = "sitio/pedido_detalles/$1";

$route['perfil'] = "sitio/perfil";

$route['cerrar_caja'] = "sitio/cerrar_caja";

$route['caja_cerrada'] = "sitio/caja_cerrada";



















/* End of file routes.php */
/* Location: ./application/config/routes.php */