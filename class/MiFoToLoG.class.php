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

/**
 * Fotolog
 * MiFoToLoG v3.0
 * @author DeLaF, esteban[at]delaf.cl
 * @version 2011-03-05
 */
class MiFoToLoG {

    /**
     * Buscar las imagenes existentes en el directorio de cache
     * @return Arreglo con las imagenes disponibles
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2015-06-22
     */
    private static function buscarImagenes()
    {
        $imagenes = array();
        if (is_dir(MIFOTOLOG_CACHE) and $gestor = opendir(MIFOTOLOG_CACHE)) { // abrir directorio
            while (($archivo = readdir($gestor)) != false) { // leer directorio
                if (preg_match('/\.gif|jpe?g|png/', strtolower($archivo))) {
                    $time = date('U', filemtime(MIFOTOLOG_CACHE.'/'.$archivo));
                    $imagenes[$time] = $archivo; // guarda el nombre del archivo
                }
            }
            closedir($gestor); // cerrar gestor
        }
        unset($gestor, $archivo);
        krsort($imagenes); // ordenar resultado de menor a mayor por fecha de moficacion
        return $imagenes;
    }

    /**
     * Buscar los smiles disponibles
     * @return Arreglo con los smiles disponibles
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-04
     */
    private static function buscarSmiles() {
        $smiles = array();
        if ($gestor = opendir(MIFOTOLOG_DIR.'/template/'.MIFOTOLOG_TEMPLATE.'/img/smiles')) { // abrir directorio
            while (($archivo = readdir($gestor)) != false) { // leer directorio
                if(preg_match('/\.gif$/', strtolower($archivo))) {
                    array_push($smiles, substr($archivo, 0, -4)); // guarda el nombre del archivo
                }
            }
            closedir($gestor); // cerrar gestor
        }
        unset($gestor, $archivo);
        sort($smiles); // ordenar resultado de menor a mayor por fecha de moficacion
        return $smiles;
    }

    /**
     * Generá el código html para las imágenes smiles
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-04
     */
    private static function smilesHTML () {
        $smiles = self::buscarSmiles();
        $smilesHTML = '';
        foreach($smiles as &$smile) {
            $smilesHTML .= self::generar('smile.html', array('smile'=>$smile, 'smile_js'=>str_replace("'", "\'", $smile)));
        }
        return $smilesHTML;
    }

    /**
     * Genera la página principal del fotolog
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-04
     */
    private static function fotolog () {
        session_start();
        // buscar imagenes
        $imagenes = self::buscarImagenes();
        $thumbnailsHTML = '';
        if(count($imagenes)) {
            $contador = 0;
            foreach($imagenes as &$imagen) {
                ++$contador;
                if($contador>MIFOTOLOG_THUMBNAIL_LIMIT) break;
                $thumbnailsHTML .= self::generar('thumbnail.html', array('img'=>$imagen, 'img_url'=>urlencode($imagen)));
            }
            // definir imagen que se mostrara
            $imagen = !empty($_GET['img']) ? urldecode($_GET['img']) : array_shift($imagenes);
            $size = sprintf('%.2f',(filesize(MIFOTOLOG_CACHE.'/'.$imagen))/1024);
            $tam = getimagesize(MIFOTOLOG_CACHE.'/'.$imagen);
            unset($imagenes);
            // buscar pie de foto y comentarios de la misma
            global $smiles2img;
            $textos = explode('<separador>', file_get_contents(MIFOTOLOG_CACHE.'/'.substr($imagen, 0, strrpos($imagen, '.')).'.txt'));
            $lineas = explode("\n", array_shift($textos));
            $fechaImagen = date(MIFOTOLOG_DATE_FORMAT, array_shift($lineas));
            $pieHTML = implode(MIFOTOLOG_TEXT_GLUE, $lineas);
            foreach($smiles2img as $key=>$value)
                $pieHTML = str_replace($key, self::generar('smileComentario.html', array('template'=>MIFOTOLOG_TEMPLATE, 'smile'=>$value)), $pieHTML);
            $comentariosHTML = '';
            $comentarioId = 0;
            foreach($textos as &$texto) {
                $lineas = explode("\n", trim($texto));
                $autor = htmlspecialchars(array_shift($lineas));
                $fecha = date(MIFOTOLOG_DATE_FORMAT, array_shift($lineas));
                foreach($lineas as &$linea)
                    $linea = htmlspecialchars($linea);
                $comentario = implode(MIFOTOLOG_TEXT_GLUE, $lineas);
                foreach($smiles2img as $key=>$value)
                    $comentario = str_replace($key, self::generar('smileComentario.html', array('template'=>MIFOTOLOG_TEMPLATE, 'smile'=>$value)), $comentario);
                if(!empty($autor) && !empty($fecha) && !empty($comentario)) {
                    ++$comentarioId;
                    if(isset($_SESSION['login']) && $_SESSION['login']) $eliminarComentario = self::generar('eliminarComentario.html', array('imagen_url'=>urlencode($imagen), 'comentario'=>$comentarioId, 'eliminar_comentario'=>MIFOTOLOG_LANG_DELETE_MSJ, 'template'=>MIFOTOLOG_TEMPLATE));
                    else $eliminarComentario = '';
                    $comentariosHTML .= self::generar('comentario.html', array('autor'=>$autor, 'fecha'=>$fecha, 'comentario'=>$comentario, 'eliminarComentario'=>$eliminarComentario));
                }
            }
            unset($textos, $texto, $lineas, $autor, $fecha, $comentario);
            // determinar link de borrado de la imagen
            if(isset($_SESSION['login']) && $_SESSION['login']) $eliminarImagen = self::generar('eliminarImagen.html', array('imagen_url'=>urlencode($imagen), 'eliminar_imagen'=>MIFOTOLOG_LANG_DELETE_IMG, 'template'=>MIFOTOLOG_TEMPLATE));
            else $eliminarImagen = '';
            // crear pagina
            $imagenHTML = self::generar('verImagen.html', array('img'=>$imagen, 'size'=>$size, 'x'=>$tam[0], 'y'=>$tam[1], 'fechaImagen'=>$fechaImagen, 'pie'=>$pieHTML, 'comentarios'=>$comentariosHTML, 'nick'=>MIFOTOLOG_LANG_FORM_NICK, 'msj'=>MIFOTOLOG_LANG_FORM_MSJ, 'submit'=>MIFOTOLOG_LANG_FORM_SUBMIT, 'smiles'=>self::smilesHTML(), 'eliminarImagen'=>$eliminarImagen));
        } else {
            $imagenHTML = self::generar('vacia.html', array('vacia'=>MIFOTOLOG_LANG_EMPTY));
        }
        // mostrar pagina
        echo self::generar('fotolog.html', array('lang'=>LANG, 'titulo'=>MIFOTOLOG_TITLE, 'thumbnails'=>$thumbnailsHTML, 'ver_todas'=>MIFOTOLOG_LANG_SEE_ALL, 'admin'=>MIFOTOLOG_LANG_ADMIN, 'imagen'=>$imagenHTML));
    }

    /**
     * Genera la página de administración del fotolog
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-04
     */
    private static function admin () {
        session_start();
        if(isset($_GET['logout'])) self::adminLogout();
        $login = isset($_SESSION['login']) ? $_SESSION['login'] : false;
        if($login) {
            if(isset($_POST['subirImagen'])) self::subirImagen();
            if(isset($_GET['eliminar']) && !empty($_GET['imagen']) && empty($_GET['comentario'])) self::eliminarImagen(urldecode($_GET['imagen']));
            if(isset($_GET['eliminar']) && !empty($_GET['imagen']) && !empty($_GET['comentario'])) self::eliminarComentario(urldecode($_GET['imagen']), $_GET['comentario']);
            $admin = self::generar('adminSubir.html', array('imagen'=>MIFOTOLOG_LANG_FORM_IMAGEN, 'pie'=>MIFOTOLOG_LANG_FORM_PIE, 'submit'=>MIFOTOLOG_LANG_FORM_SUBMIT, 'smiles'=>self::smilesHTML()));
            $adminEnlaces = self::generar('adminEnlacesLogueado.html', array('titulo'=>MIFOTOLOG_TITLE, 'salir'=>MIFOTOLOG_LANG_LOGOUT));
        } else {
            if(isset($_POST['adminLogin'])) self::adminLogin();
            $admin = self::generar('adminPass.html', array('clave'=>MIFOTOLOG_LANG_FORM_CLAVE, 'submit'=>MIFOTOLOG_LANG_FORM_SUBMIT));
            $adminEnlaces = self::generar('adminEnlacesNoLogueado.html', array('titulo'=>MIFOTOLOG_TITLE));
        }
        echo self::generar('admin.html', array('titulo'=>MIFOTOLOG_TITLE, 'admin'=>$admin, 'lang'=>LANG, 'adminEnlaces'=>$adminEnlaces));
    }

    /**
     * Inicia la sesión para el administrador
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-04
     */
    private static function adminLogin () {
        if(md5($_POST['clave'])==MIFOTOLOG_PASS) $_SESSION['login'] = true;
        else $_SESSION['login'] = false;
        header('location: ?admin');
    }

    /**
     * Cierra la sesión para el administrador
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-04
     */
    private static function adminLogout () {
        $_SESSION['login'] = false;
        header('location: ?admin');
    }

    /**
     * Genera la página que muestra todas las imagenes del fotolog
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-04
     */
    private static function imagenes () {
        $imagenes = self::buscarImagenes();
        $imagenesHTML = '';
        foreach($imagenes as &$imagen) {
            $imagenesHTML .= self::generar('imagen.html', array('img'=>$imagen, 'img_url'=>urlencode($imagen)));
        }
        echo self::generar('imagenes.html', array('titulo'=>MIFOTOLOG_TITLE, 'imagenes'=>$imagenesHTML, 'lang'=>LANG));
    }

    /**
     * Sube una imagen al fotolog
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2015-06-22
     */
    private static function subirImagen()
    {
        if (is_uploaded_file($_FILES['imagen']['tmp_name'])) {
            // verificar existencia de directorio para caché
            if (!is_dir(MIFOTOLOG_CACHE)) {
                if (is_writeable(dirname(MIFOTOLOG_CACHE))) {
                    mkdir(MIFOTOLOG_CACHE);
                    mkdir(MIFOTOLOG_CACHE.'/thumbnails/');
                } else {
                    die('No es posible crear directorio cache ni cache/thumbnails. Creelos manualmente y de permisos al usuario web.');
                }
            }
            // subir foto al directorio de cache
            move_uploaded_file($_FILES['imagen']['tmp_name'], MIFOTOLOG_CACHE.'/'.$_FILES['imagen']['name']);
            // subir thumbnail
            require(MIFOTOLOG_DIR.'/class/thumbnail.class.php');
            $thumb = new thumbnail(MIFOTOLOG_CACHE.'/'.$_FILES['imagen']['name']);
            $thumb->size_width(MIFOTOLOG_THUMBNAIL_WIDTH);
            $thumb->jpeg_quality(90);
            $thumb->save(MIFOTOLOG_CACHE.'/thumbnails/'.$_FILES['imagen']['name']);
            // colocar comentario inicial (pie de pagina)
            if($archivo = fopen(MIFOTOLOG_CACHE.'/'.substr($_FILES['imagen']['name'], 0, strrpos($_FILES['imagen']['name'], '.')).'.txt', 'w')) {
                fputs ($archivo, date('U')."\n");
                fputs ($archivo, $_POST['msj']."\n");
                fclose($archivo);
            }
            // cambiar permisos a los archivos subidos por el servidor
            chmod(MIFOTOLOG_CACHE.'/'.$_FILES['imagen']['name'], MIFOTOLOG_CHMOD);
            chmod(MIFOTOLOG_CACHE.'/thumbnails/'.$_FILES['imagen']['name'], MIFOTOLOG_CHMOD);
            chmod(MIFOTOLOG_CACHE.'/'.substr($_FILES['imagen']['name'], 0, strrpos($_FILES['imagen']['name'], '.')).'.txt', MIFOTOLOG_CHMOD);
        }
        // redireccionar
        header('location: .');
    }

    /**
     * Publica un comentario en una de las imagenes del fotolog
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-04
     */
    private static function comentario () {
        if(!empty($_POST['nick']) && !empty($_POST['msj'])) {
            if($archivo = fopen(MIFOTOLOG_CACHE.'/'.substr($_POST['img'], 0, strrpos($_POST['img'], '.')).'.txt', 'a')) {
                fputs ($archivo, '<separador>'."\n");
                fputs ($archivo, $_POST['nick']."\n");
                fputs ($archivo, date('U')."\n");
                fputs ($archivo, $_POST['msj']."\n");
                fclose($archivo);
            }
        }
        header('location: ?img='.$_POST['img']);
    }

    /**
     * Elimina una imágen desde el fotolog
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-05
     */
    private static function eliminarImagen ($imagen) {
        unlink(MIFOTOLOG_CACHE.'/'.$imagen);
        unlink(MIFOTOLOG_CACHE.'/thumbnails/'.$imagen);
        unlink(MIFOTOLOG_CACHE.'/'.substr($imagen, 0, strrpos($imagen, '.')).'.txt');
        header('location: .');
    }

    /**
     * Elimina un comentario de una de las imágenes del fotolog
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-05
     */
    private static function eliminarComentario ($imagen, $comentario) {
        $texto = file_get_contents(MIFOTOLOG_CACHE.'/'.substr($imagen, 0, strrpos($imagen, '.')).'.txt');
        $comentarios = explode('<separador>', $texto);
        unset($comentarios[$comentario]);
        $texto = implode('<separador>', $comentarios);
        unset($comentarios, $comentario);
        file_put_contents(MIFOTOLOG_CACHE.'/'.substr($imagen, 0, strrpos($imagen, '.')).'.txt', $texto);
        header('location: ?img='.$imagen);
    }

    /**
     * Muestra el fotolog o la pantalla de administración según corresponda
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-03
     */
    public static function mostrar () {
        if(isset($_GET['admin'])) self::admin();
        else if(isset($_GET['imagenes'])) self::imagenes();
        else if(isset($_POST['comentario'])) self::comentario();
        else self::fotolog();
    }

    /**
     * Esta método permite utilizar plantillas html en la aplicacion, estas deberán
     * estar ubicadas en la carpeta template del directorio raiz (de la app)
     * @param nombrePlantila Nombre del archivo html que se utilizara como plantilla
     * @param variables Arreglo con las variables a reemplazar en la plantilla
     * @param tab Si es que se deberán añadir tabuladores al inicio de cada linea de la plantilla
     * @return String Plantilla ya formateada con las variables correspondientes
     * @author DeLaF, esteban[at]delaf.cl
     * @version 2011-03-03
     */
    private static function generar ($nombrePlantilla, $variables = null, $tab = 0) {

        // definir donde se encuentra la plantilla
        $archivoPlantilla = MIFOTOLOG_DIR.'/template/'.MIFOTOLOG_TEMPLATE.'/'.$nombrePlantilla;

        // cargar plantilla
        $plantilla = file_get_contents($archivoPlantilla);

        // añadir tabuladores delante de cada linea
        if($tab) {
            $lineas = explode("\n", $plantilla);
            foreach($lineas as &$linea) {
                if(!empty($linea)) $linea = constant('TAB'.$tab).$linea;
            }
            $plantilla = implode("\n", $lineas);
            unset($lineas, $linea);
        }

        // reemplazar variables en la plantilla
        if($variables) {
            foreach($variables as $key => $valor)
                $plantilla = str_replace('{'.$key.'}', $valor, $plantilla);
        }

        // retornar plantilla ya procesada
        return $plantilla;

        }

}

?>
