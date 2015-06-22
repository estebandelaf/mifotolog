<?php

/**
 * MiFoToLoG
 * Copyright (C) 2005-2011 Esteban De La Fuente Rubio (esteban@delaf.cl)
 *
 * Este programa es software libre: usted puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General GNU publicada
 * por la Fundación para el Software Libre, ya sea la versión 3
 * de la Licencia, o (a su elección) cualquier versión posterior de la misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero
 * SIN GARANTÍA ALGUNA; ni siquiera la garantía implícita
 * MERCANTIL o de APTITUD PARA UN PROPÓSITO DETERMINADO.
 * Consulte los detalles de la Licencia Pública General GNU para obtener
 * una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General GNU
 * junto a este programa.
 * En caso contrario, consulte <http://www.gnu.org/licenses/gpl.html>.
 *
 */

date_default_timezone_set('America/Santiago');

define('MIFOTOLOG_TITLE', 'Fotos curiosas');
define('MIFOTOLOG_DIR', dirname(dirname(__FILE__)));
define('MIFOTOLOG_CACHE', MIFOTOLOG_DIR.'/cache');
define('MIFOTOLOG_PASS', '21232f297a57a5a743894a0e4a801fc3'); // hash md5, por defecto "admin"
define('MIFOTOLOG_THUMBNAIL_LIMIT', 10);
define('MIFOTOLOG_THUMBNAIL_WIDTH', 100); // tamaño en pixeles
define('MIFOTOLOG_CHMOD', 0666); // debe ser valor octal o sea con el 0 adelante del permiso
define('MIFOTOLOG_TEXT_GLUE', '</p><p>'); // con que se reemplazaran los \n de los pie de foto y comentarios
define('MIFOTOLOG_DATE_FORMAT', 'Y-m-d H:i'); // con que se reemplazaran los \n de los pie de foto y comentarios
define('MIFOTOLOG_TEMPLATE', 'default');
define('LANG', 'es');

// smiles
$smiles2img = array(
	'8)'=>'8)'
	, ':\'('=>':\'('
	, ':('=>':('
	, ':-('=>':('
	, ':)'=>':)'
	, ':-)'=>':)'
	, ':@'=>':@'
	, ':D'=>':D'
	, ':p'=>':p'
	, ':P'=>':p'
	, ':s'=>':s'
	, ':S'=>':s'
	, ';)'=>';)'
	, ';-)'=>';)'
	, '^_^'=>'^_^'
	, '¬¬'=>'¬¬'
	, 'o_o'=>'o_o'
);

?>
