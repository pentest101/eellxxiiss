<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: en-GB (English - Great Britain) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Ioannis Sannos ( http://www.elxis.org )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$locale = array('es_ES.utf8', 'es_ES.UTF-8', 'en_GB', 'en', 'english', 'england'); //utf-8 locales array

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'd-m-Y'; //supported formats: d-m-Y, Y-m-d, d/m/Y, Y/m/d
$_lang['DATE_FORMAT_BOX_LONG'] = 'd-m-Y H:i:s'; //supported formats: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
$_lang['DATE_FORMAT_1'] = "%d/%m/%Y"; //example: 25/12/2010
$_lang['DATE_FORMAT_2'] = "%b %d, %Y"; //example: Dec 25, 2010
$_lang['DATE_FORMAT_3'] = "%B %d, %Y"; //example: December 25, 2010
$_lang['DATE_FORMAT_4'] = "%b %d, %Y %H:%M"; //example: Dec 25, 2010 12:34
$_lang['DATE_FORMAT_5'] = "%B %d, %Y %H:%M"; //example: December 25, 2010 12:34
$_lang['DATE_FORMAT_6'] = "%B %d, %Y %H:%M:%S"; //example: December 25, 2010 12:34:45
$_lang['DATE_FORMAT_7'] = "%a %b %d, %Y"; //example: Sat Dec 25, 2010
$_lang['DATE_FORMAT_8'] = "%A %b %d, %Y"; //example: Saturday Dec 25, 2010
$_lang['DATE_FORMAT_9'] = "%A %B %d, %Y"; //example: Saturday December 25, 2010
$_lang['DATE_FORMAT_10'] = "%A %B %d, %Y %H:%M"; //example: Saturday December 25, 2010 12:34
$_lang['DATE_FORMAT_11'] = "%A %B %d, %Y %H:%M:%S"; //example: Saturday December 25, 2010 12:34:45
$_lang['DATE_FORMAT_12'] = "%a %B %d, %Y %H:%M"; //example: Sat December 25, 2010 12:34
$_lang['DATE_FORMAT_13'] = "%a %B %d, %Y %H:%M:%S"; //example: Sat December 25, 2010 12:34:45
$_lang['THOUSANDS_SEP'] = '.';
$_lang['DECIMALS_SEP'] = ',';
//month names
$_lang['JANUARY'] = 'Enero';
$_lang['FEBRUARY'] = 'Febrero';
$_lang['MARCH'] = 'Marzo';
$_lang['APRIL'] = 'Abril';
$_lang['MAY'] = 'Mayo';
$_lang['JUNE'] = 'Junio';
$_lang['JULY'] = 'Julio';
$_lang['AUGUST'] = 'Agosto';
$_lang['SEPTEMBER'] = 'Septiembre';
$_lang['OCTOBER'] = 'Octubre';
$_lang['NOVEMBER'] = 'Noviembre';
$_lang['DECEMBER'] = 'Diciembre';
$_lang['JANUARY_SHORT'] = 'Ener';
$_lang['FEBRUARY_SHORT'] = 'Feb';
$_lang['MARCH_SHORT'] = 'Mar';
$_lang['APRIL_SHORT'] = 'Abr';
$_lang['MAY_SHORT'] = 'May';
$_lang['JUNE_SHORT'] = 'Jun';
$_lang['JULY_SHORT'] = 'Jul';
$_lang['AUGUST_SHORT'] = 'Ago';
$_lang['SEPTEMBER_SHORT'] = 'Sep';
$_lang['OCTOBER_SHORT'] = 'Oct';
$_lang['NOVEMBER_SHORT'] = 'Nov';
$_lang['DECEMBER_SHORT'] = 'Dic';
//day names
$_lang['MONDAY'] = 'Lunes';
$_lang['THUESDAY'] = 'Martes';
$_lang['WEDNESDAY'] = 'Mi??rcoles';
$_lang['THURSDAY'] = 'Jueves';
$_lang['FRIDAY'] = 'Viernes';
$_lang['SATURDAY'] = 'S??bado';
$_lang['SUNDAY'] = 'Domingo';
$_lang['MONDAY_SHORT'] = 'Lun';
$_lang['THUESDAY_SHORT'] = 'Mar';
$_lang['WEDNESDAY_SHORT'] = 'Mie';
$_lang['THURSDAY_SHORT'] = 'Jue';
$_lang['FRIDAY_SHORT'] = 'Vie';
$_lang['SATURDAY_SHORT'] = 'S??b';
$_lang['SUNDAY_SHORT'] = 'Dom';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Monitor de rendimiento de Elxis';
$_lang['ITEM'] = 'Elemento';
$_lang['INIT_FILE'] = 'Fichero de inicializaci??n';
$_lang['EXEC_TIME'] = 'Tiempo de ejecucaci??n';
$_lang['DB_QUERIES'] = 'Consultas a la BD';
$_lang['ERRORS'] = 'Errores';
$_lang['SIZE'] = 'Tama??o';
$_lang['ENTRIES'] = 'Entradas';

/* general */
$_lang['HOME'] = 'Inicio';
$_lang['YOU_ARE_HERE'] = 'Usted est?? en';
$_lang['CATEGORY'] = 'Categor??a';
$_lang['DESCRIPTION'] = 'Descripci??n';
$_lang['FILE'] = 'Archivo';
$_lang['IMAGE'] = 'Imagen';
$_lang['IMAGES'] = 'Im??genes';
$_lang['CONTENT'] = 'Contenido';
$_lang['DATE'] = 'Fecha';
$_lang['YES'] = 'S??';
$_lang['NO'] = 'No';
$_lang['NONE'] = 'Ningulo';
$_lang['SELECT'] = 'Seleccione';
$_lang['LOGIN'] = 'Entrar';
$_lang['LOGOUT'] = 'Salir';
$_lang['WEBSITE'] = 'Sitio Web';
$_lang['SECURITY_CODE'] = 'C??digo de seguridad';
$_lang['RESET'] = 'Restaurar';
$_lang['SUBMIT'] = 'Enviar';
$_lang['REQFIELDEMPTY'] = 'Uno o m??s de los campos obligatorios est?? vac??o!';
$_lang['FIELDNOEMPTY'] = "%s no puede estar vac??o!";
$_lang['FIELDNOACCCHAR'] = "%s contiene caracteres no aceptados!";
$_lang['INVALID_DATE'] = 'Fecha no v??lida!';
$_lang['INVALID_NUMBER'] = 'N??mero no v??lido!';
$_lang['INVALID_URL'] = 'Dircci??n URL no v??lida!';
$_lang['FIELDSASTERREQ'] = 'Los campos con asterisco * son obligatorios.';
$_lang['ERROR'] = 'Error';
$_lang['REGARDS'] = 'Saludos';
$_lang['NOREPLYMSGINFO'] = 'Por favor, no conteste a este mensaje, ha sido enviado ??nicamente para informarle.';
$_lang['LANGUAGE'] = 'Idioma';
$_lang['PAGE'] = 'P??gina';
$_lang['PAGEOF'] = "P??gina %s de %s";
$_lang['OF'] = 'de';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Mostrando %s de %s de los %s elementos";
$_lang['HITS'] = 'Vistas';
$_lang['PRINT'] = 'Imprimir';
$_lang['BACK'] = 'Volver';
$_lang['PREVIOUS'] = 'Anterior';
$_lang['NEXT'] = 'Siguiente';
$_lang['CLOSE'] = 'Cerrar';
$_lang['CLOSE_WINDOW'] = 'Cerrar ventana';
$_lang['COMMENTS'] = 'Comentarios';
$_lang['COMMENT'] = 'Commentario';
$_lang['PUBLISH'] = 'Publicar';
$_lang['DELETE'] = 'Borrar';
$_lang['EDIT'] = 'Editar';
$_lang['COPY'] = 'Copiar';
$_lang['SEARCH'] = 'Buscar';
$_lang['PLEASE_WAIT'] = 'Por favor, espere...';
$_lang['ANY'] = 'Cualquiera';
$_lang['NEW'] = 'Nuevo';
$_lang['ADD'] = 'A??adir';
$_lang['VIEW'] = 'Ver';
$_lang['MENU'] = 'Men??';
$_lang['HELP'] = 'Ayuda';
$_lang['TOP'] = 'Arriba';
$_lang['BOTTOM'] = 'Abajo';
$_lang['LEFT'] = 'Izquierda';
$_lang['RIGHT'] = 'Derecha';
$_lang['CENTER'] = 'Centro';

/* xml */
$_lang['CACHE'] = 'Cach??';
$_lang['ENABLE_CACHE_D'] = '??Permitir cach?? para este elemento?';
$_lang['YES_FOR_VISITORS'] = 'Si, para invitados';
$_lang['YES_FOR_ALL'] = 'S??, para todos';
$_lang['CACHE_LIFETIME'] = 'Vida de la Cach??';
$_lang['CACHE_LIFETIME_D'] = 'Tiempo en minutos hasta que la cach?? sea refrescada para este elemento.';
$_lang['NO_PARAMS'] = 'No hay par??meteros!';
$_lang['STYLE'] = 'Stilo';
$_lang['ADVANCED_SETTINGS'] = 'Configuraci??n avanzada';
$_lang['CSS_SUFFIX'] = 'Sufijo CSS';
$_lang['CSS_SUFFIX_D'] = 'Un sufijo que ser?? a??adido a la clase CSS del m??dulo.';
$_lang['MENU_TYPE'] = 'Tipo de Men??';
$_lang['ORIENTATION'] = 'Orientaci??n';
$_lang['SHOW'] = 'Mostrar';
$_lang['HIDE'] = 'Ocultar';
$_lang['GLOBAL_SETTING'] = 'Configuraci??n global';

/* users & authentication */
$_lang['USERNAME'] = 'Nombre de usuario';
$_lang['PASSWORD'] = 'Contrase??a';
$_lang['NOAUTHMETHODS'] = 'No ha sido configurado ning??n m??todo de autenticaci??n';
$_lang['AUTHMETHNOTEN'] = 'El m??todo de autenticaci??n %s no est?? habilitado';
$_lang['PASSTOOSHORT'] = 'Su clave es demasiado corta para ser aceptada';
$_lang['USERNOTFOUND'] = 'Usuario no encontrado';
$_lang['INVALIDUNAME'] = 'Usuario no v??lido';
$_lang['INVALIDPASS'] = 'Clave no v??lida';
$_lang['AUTHFAILED'] = 'Fall?? la autenticaci??n';
$_lang['YACCBLOCKED'] = 'Su cuenta est?? bloqueda';
$_lang['YACCEXPIRED'] = 'Su cuenta ha expirado';
$_lang['INVUSERGROUP'] = 'Grupo de usuarios no v??lido';
$_lang['NAME'] = 'Nombre';
$_lang['FIRSTNAME'] = 'Nombre';
$_lang['LASTNAME'] = 'Apellidos';
$_lang['EMAIL'] = 'E-mail';
$_lang['INVALIDEMAIL'] = 'Direcci??n de e-mail no v??lida';
$_lang['ADMINISTRATOR'] = 'Administrador';
$_lang['GUEST'] = 'Invitado';
$_lang['EXTERNALUSER'] = 'Usuario Externo';
$_lang['USER'] = 'Usuario';
$_lang['GROUP'] = 'Grupo';
$_lang['NOTALLOWACCPAGE'] = 'No est?? autorizado a acceder a esta p??gina!';
$_lang['NOTALLOWACCITEM'] = 'No est?? autorizado a acceder a este elemento!';
$_lang['NOTALLOWMANITEM'] = 'No est?? autorizado a administrar este elemento!';
$_lang['NOTALLOWACTION'] = 'No est?? autorizado para relizar esta acci??n!';
$_lang['NEED_HIGHER_ACCESS'] = 'Necesita un nivel de permisos m??s alto para relizar esta acci??n!';
$_lang['AREYOUSURE'] = 'Est?? seguro?';

/* highslide */
$_lang['LOADING'] = 'Cargando...';
$_lang['CLICK_CANCEL'] = 'Haga click para cancelar';
$_lang['MOVE'] = 'Mover';
$_lang['PLAY'] = 'Iniciar';
$_lang['PAUSE'] = 'Pausar';
$_lang['RESIZE'] = 'Cambiar tama??o';

/* admin */
$_lang['ADMINISTRATION'] = 'Administraci??n';
$_lang['SETTINGS'] = 'Configuraci??n';
$_lang['DATABASE'] = 'Base de datos';
$_lang['ON'] = 'Encendido';
$_lang['OFF'] = 'Apagado';
$_lang['WARNING'] = 'Advertencia';
$_lang['SAVE'] = 'Guardar';
$_lang['APPLY'] = 'Aplicar';
$_lang['CANCEL'] = 'Cancelar';
$_lang['LIMIT'] = 'L??mite';
$_lang['ORDERING'] = 'Orden';
$_lang['NO_RESULTS'] = 'No se encontraron resultados!';
$_lang['CONNECT_ERROR'] = 'Error de conexi??n';
$_lang['DELETE_SEL_ITEMS'] = '??Borrar los elementos seleccionados items?';
$_lang['TOGGLE_SELECTED'] = '??Cambiar los seleccionados?';
$_lang['NO_ITEMS_SELECTED'] = 'No hay elementos seleccionados!';
$_lang['ID'] = 'Id';
$_lang['ACTION_FAILED'] = 'La acci??n fall??!';
$_lang['ACTION_SUCCESS'] = 'Acci??n completada con ??xito!';
$_lang['NO_IMAGE_UPLOADED'] = 'No se subi?? ninguna imagen';
$_lang['NO_FILE_UPLOADED'] = 'No se subi?? ning??n archivo';
$_lang['MODULES'] = 'M??dulos';
$_lang['COMPONENTS'] = 'Componentes';
$_lang['TEMPLATES'] = 'Plantillas';
$_lang['SEARCH_ENGINES'] = 'Motores de b??squeda';
$_lang['AUTH_METHODS'] = 'M??todos de Autenticaci??n';
$_lang['CONTENT_PLUGINS'] = 'Plugins de contenido';
$_lang['PLUGINS'] = 'Plugins';
$_lang['PUBLISHED'] = 'Publicado';
$_lang['ACCESS'] = 'Acceso';
$_lang['ACCESS_LEVEL'] = 'Nivel de acceso';
$_lang['TITLE'] = 'T??tulo';
$_lang['MOVE_UP'] = 'Mover arriba';
$_lang['MOVE_DOWN'] = 'Mover abajo';
$_lang['WIDTH'] = 'Anchura';
$_lang['HEIGHT'] = 'Altura';
$_lang['ITEM_SAVED'] = 'Elemento salvado';
$_lang['FIRST'] = 'Primero';
$_lang['LAST'] = '??ltimo';
$_lang['SUGGESTED'] = 'Sugerido';
$_lang['VALIDATE'] = 'Validar';
$_lang['NEVER'] = 'Nunca';
$_lang['ALL'] = 'Todos';
$_lang['ALL_GROUPS_LEVEL'] = "Todos los grupos del nivel %s";
$_lang['REQDROPPEDSEC'] = 'Su petici??n fue eliminada por cuestiones de seguridad. Por favor, int??ntelo de nuevo.';
$_lang['PROVIDE_TRANS'] = 'Por favor, proporcione una traducci??n!';
$_lang['AUTO_TRANS'] = 'Traducci??n autom??tica';
$_lang['STATISTICS'] = 'Estad??sticas';
$_lang['UPLOAD'] = 'Subir';
$_lang['MORE'] = 'M??s';
//Elxis 4.2
$_lang['TRANSLATIONS'] = 'Traducciones';
$_lang['CHECK_UPDATES'] = 'Comprobar actualizaciones';
$_lang['TODAY'] = 'Hoy';
$_lang['YESTERDAY'] = 'Ayer';
//Elxis 4.3
$_lang['PUBLISH_ON'] = 'Publicar el';
$_lang['UNPUBLISHED'] = 'No publicado';
$_lang['UNPUBLISH_ON'] = 'Despublicar el';
$_lang['SCHEDULED_CRON_DIS'] = 'There are %s scheduled items but Cron Jobs are disabled!';
$_lang['CRON_DISABLED'] = 'Cron Jobs are disabled!';
$_lang['ARCHIVE'] = 'Archivo';
$_lang['RUN_NOW'] = '??jecutar ahora';
$_lang['LAST_RUN'] = '??ltima ejecuci??n';
$_lang['SEC_AGO'] = 'Hace %s seg';
$_lang['MIN_SEC_AGO'] = 'Hace %s min y %s seg';
$_lang['HOUR_MIN_AGO'] = 'Hace 1 hora y %s min';
$_lang['HOURS_MIN_AGO'] = 'Hace %s horas y %s min';
$_lang['CLICK_TOGGLE_STATUS'] = 'Haga clic para cambiar el estado';
//Elxis 4.5
$_lang['IAMNOTA_ROBOT'] = 'No soy un robot';
$_lang['VERIFY_NOROBOT'] = 'Por favor, confirma que no eres un robot!';
$_lang['CHECK_FS'] = 'Verificaci??n de archivos';
//Elxis 5.0
$_lang['TOTAL_ITEMS'] = '%s elementos';
$_lang['SEARCH_OPTIONS'] = 'Opciones de b??squeda';
$_lang['FILTERS_HAVE_APPLIED'] = 'Se han aplicado filtros';
$_lang['FILTER_BY_ITEM'] = 'Filtrar por este elemento';
$_lang['REMOVE_FILTER'] = 'Eliminar filtro';
$_lang['TOTAL'] = 'Total';
$_lang['OPCIONES'] = 'Opciones';
$_lang['DISABLE'] = 'Inhabilitar';
$_lang['REMOVE'] = 'Eliminar';
$_lang['ADD_ALL'] = 'Agregar todo';
$_lang['TOMORROW'] = 'Ma??ana';
$_lang['NOW'] = 'Ahora';
$_lang['MIN_AGO'] = 'Hace 1 minuto';
$_lang['MINS_AGO'] = 'Hace %s minutos';
$_lang['HOUR_AGO'] = 'Hace 1 hora';
$_lang['HOURS_AGO'] = 'Hace %s horas';
$_lang['IN_SEC'] = 'En %s sec';
$_lang['IN_MINUTE'] = 'En 1 minuto';
$_lang['IN_MINUTES'] = 'En %s minute';
$_lang['IN_HOUR'] = 'En 1 hora';
$_lang['IN_HOURS'] = 'En %s horas';
$_lang['OTHER'] = 'Otro';
$_lang['DELETE_CURRENT_IMAGE'] = 'Eliminar la imagen actual';
$_lang['NO_IMAGE_FILE'] = 'No hay archivo de imagen!';
$_lang['SELECT_FILE'] = 'Seleccionar archivo';

?>