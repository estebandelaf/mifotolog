/**
 * Ubica el smile indicado en un textarea
 * @param smile Codigo del smile para colocar en el textarea
 * @author DeLaF, esteban[at]delaf.cl
 * @date 2011-03-04
 */
function smile (smile) {
	document.formulario.msj.value += ' '+smile;
}

/**
 * Verifica si un campo esta vacio
 * @param texto String que se desea verificar que sea vacio
 * @return True si el string pasado es vacio
 * @author DeLaF, esteban[at]delaf.cl
 * @date 2010-05-23
 */
function vacio (texto) {
	for(i=0;i<texto.length;i++) {
		if(texto.charAt(i)!=" ")
			return false;
	}
	return true;
}

/**
 * Verifica que se hayan pasado los campos necesarios al formulario del mensaje
 * @param formulario Formulario de login
 * @return True si los campos necesarios han sido pasados
 * @author DeLaF, esteban[at]delaf.cl
 * @date 2011-03-04
 */
function validarMsj (formulario) {
	if(vacio(formulario.nick.value)) {
		alert(LANG_MSJ_ERROR_NICK);
		formulario.nick.focus();
		return false;
	}
	if(vacio(formulario.msj.value)) {
		alert(LANG_MSJ_ERROR_MSJ);
		formulario.msj.focus();
		return false;
	}
	return true;
}
