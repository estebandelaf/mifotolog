MiFoToLoG
=========

MiFoToLoG es un fotolog mono usuario que no requiere de una base de datos para su funcionamiento. Este es un nuevo desarrollo a partir de la idea de la [versión del 2005](https://github.com/estebandelaf/mitotolog-2005).

Puedes ver la versión del fotolog funcionando en [aquí](http://mi.delaf.cl/mifotolog), en caso de querer probar la [versión original](https://github.com/estebandelaf/mitotolog-2005) se deberá bajar e instalar en un servidor propio.

Se requiere que existe el directorio *cache* y *cache/thumbnails* ambos con permisos de escritura para el usuario que ejecuta la página web.

Características
---------------

- Permite solo un usuario por fotolog.
- Fotos que se suban pueden tener un pie de imágen e ilimitados comentarios.
- No hay límite en las imágenes que se puedan subir.
- Imágenes y comentarios son almacenados en un directorio de cache en el servidor.
- No posee autenticación de los usuarios que postean.
- Vista previa de las imágenes mediante *thumbnails*.
- Se pueden eliminar imágenes o bien comentarios de las mismas.
- Permite insertar *smiles* en los comentarios de las imágenes.
- Se puede ingresar código HTML en el pie de la imágen.
- Bloquea código HTML de los comentarios.
- Soporte de internacionalización.
- Utiliza plantillas para separar el diseño de la lógica.
