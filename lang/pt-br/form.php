<?php
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return array(

	'name'      => 'Nome',
	'name_help' => 'Digite um nome descritivo para a sua página.',

	'slug'      => 'Nome curto',
	'slug_help' => 'única palavra , sem espaços. Sublinhados e hífens são permitidos.',

	'uri'      => 'Uri',
	'uri_help' => 'Url da sua pagina.',

	'enabled'      => 'Status',
	'enabled_help' => 'Qual é o status desta página ?',

	'type'      => 'Tipo de armazenamento',
	'type_help' => 'Como é que você deseja armazenar e editar esta página ?',

	'visibility' => array(
		'legend' => 'Visibilidade',

		'always'     => 'Mostrar Sempre',
		'logged_in'  => 'Conectado',
		'logged_out' => 'Desconectado',
		'admin'      => 'Apenas Administrador',
	),
	'visibility_help' => 'Quando é que esta página será vista? ',

	'groups'      => 'Grupos ',
	'groups_help' => 'Que grupos de usuários devem ser capazes de ver esta página?',

	'template'      => 'Modelo',
	'template_help' => 'modelo de página para usar.',

	'meta_title'      => 'Meta Título',
	'meta_title_help' => 'Título Meta tag.',

	'meta_description'      => 'Meta Descrição',
	'meta_description_help' => 'Meta Descrição tag.',

	'section'      => 'Seção',
	'section_help' => 'Que @section() deve injetar este valor?',

	'value'      => 'Valor',
	'value_help' => "O valor da página. @content é permitido.",

	'file'      => 'Arquivo',
	'file_help' => 'Arquivo a ser usado.',

	'create' => array(
		'legend'  => 'Adicionar Página',
		'summary' => 'Por favor, forneça as informações a seguir..',
	),

	'update' => array(
		'legend'  => 'Editar Página',
		'summary' => 'Por favor, forneça as informações a seguir..',
	),

	'copy' => array(
		'legend'  => 'Copiar Página',
		'summary' => 'Por favor, forneça as informações a seguir..',
	),

);
