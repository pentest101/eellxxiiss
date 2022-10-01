<?php 
/**
* @version: 5.2
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( https://www.elxis.org )
* @copyright: (C) 2006-2021 Elxis.org. All rights reserved.
* @description: es-ES (Spanish - Spain) language for component CPanel
* @license: Elxis public license https://www.elxis.org/elxis-public-license.html
* @translator: Ioannis Sannos ( https://www.elxis.org )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['CONTROL_PANEL'] = 'Panel de control';
$_lang['GENERAL_SITE_SETS'] = 'Configuración general del sitio';
$_lang['LANGS_MANAGER'] = 'Administrador de idiomas';
$_lang['MANAGE_SITE_LANGS'] = 'Administrar idiomas del sitio';
$_lang['USERS'] = 'Usuarios';
$_lang['MANAGE_USERS'] = 'Crear, editar y borrar cuentas de usuario';
$_lang['USER_GROUPS'] = 'Grupos de Usuarios';
$_lang['MANAGE_UGROUPS'] = 'Administrar grupos de usuarios';
$_lang['MEDIA_MANAGER'] = 'Administrador multimedia';
$_lang['MEDIA_MANAGER_INFO'] = 'Administrador archivos multimedia';
$_lang['ACCESS_MANAGER'] = 'Administrador de accesos';
$_lang['MANAGE_ACL'] = 'Administrador Listas de Control de Aceesos';
$_lang['MENU_MANAGER'] = 'Administrador de menús';
$_lang['MANAGE_MENUS_ITEMS'] = 'Administrar menús y elementos de menú';
$_lang['FRONTPAGE'] = 'Portada';
$_lang['DESIGN_FRONTPAGE'] = 'Diseñar la portada del sitio';
$_lang['CATEGORIES_MANAGER'] = 'Administrador de categorías';
$_lang['MANAGE_CONT_CATS'] = 'Administrar categorías de contenido';
$_lang['CONTENT_MANAGER'] = 'Administrador de contenido';
$_lang['MANAGE_CONT_ITEMS'] = 'Administrar elementos de contenido';
$_lang['MODULES_MANAGE_INST'] = 'Administrar módulos e instalar nuevos.';
$_lang['PLUGINS_MANAGE_INST'] = 'Administrar plugins de contenido e instalar nuevos.';
$_lang['COMPONENTS_MANAGE_INST'] = 'Administrar componentes e instalar nuevos.';
$_lang['TEMPLATES_MANAGE_INST'] = 'Administrar plantillas e instalar nuevas.';
$_lang['SENGINES_MANAGE_INST'] = 'Administrar motores de búsqueda e instalar nuevos.';
$_lang['MANAGE_WAY_LOGIN'] = 'Administrar las diferentes formas para acceder al sitio de los usuarios.';
$_lang['TRANSLATOR'] = 'Traductor';
$_lang['MANAGE_MLANG_CONTENT'] = 'Administrar contenido multimedia';
$_lang['LOGS'] = 'Registros Log';
$_lang['VIEW_MANAGE_LOGS'] = 'Ver y administrar registros log';
$_lang['GENERAL'] = 'General';
$_lang['WEBSITE_STATUS'] = 'Estado del sitio web';
$_lang['ONLINE'] = 'Online';
$_lang['OFFLINE'] = 'Fuera de línea';
$_lang['ONLINE_ADMINS'] = 'Online sólo para administradores';
$_lang['OFFLINE_MSG'] = 'Mensaje de sitio offline';
$_lang['OFFLINE_MSG_INFO'] = 'Deje este campo vació para mostrar un mensaje multilenguaje automático';
$_lang['SITENAME'] = 'Nombre del sitio';
$_lang['URL_ADDRESS'] = 'Dirección URL';
$_lang['REPO_PATH'] = 'Ruta del repositorio';
$_lang['REPO_PATH_INFO'] = 'Ruta completa al directorio repositorio de Elxis. Déjelo vacío para la localización por 
	defecto (raíz_elxis/repository/). Recomendamos encarecidamente que mueva este directorio por encima del WWW y 
	lo renombre a algo que no sea predecible!';
$_lang['FRIENDLY_URLS'] = 'URLs amigables';
$_lang['SEF_INFO'] = 'Si elige SÍ (recomendado) renombre el archivo htaccess.txt a .htaccess';
$_lang['STATISTICS_INFO'] = '¿Habilitar la colección de estadísticas de tráfico del sitio?';
$_lang['GZIP_COMPRESSION'] = 'Compresión GZip';
$_lang['GZIP_COMPRESSION_DESC'] = 'Elxis comprimirá el documento con GZIP antes de enviarlo al navegador, ahorrando un 70%-80% de ancho de banda.';
$_lang['DEFAULT_ROUTE'] = 'Ruta por defecto';
$_lang['DEFAULT_ROUTE_INFO'] = 'Una URI de Elxis formateada que será usada como portada del sitio';
$_lang['META_DATA'] = 'META datos';
$_lang['META_DATA_INFO'] = 'Una breve descripción para el sitio web';
$_lang['KEYWORDS'] = 'Palabras clave';
$_lang['KEYWORDS_INFO'] = 'Unas pocas palabras clave separadas por comas';
$_lang['STYLE_LAYOUT'] = 'Estilo y disposición';
$_lang['SITE_TEMPLATE'] = 'Plantilla del sitio';
$_lang['ADMIN_TEMPLATE'] = 'Administración de la plantilla';
$_lang['ICONS_PACK'] = 'Paquete de Iconos';
$_lang['LOCALE'] = 'Local';
$_lang['TIMEZONE'] = 'Zona horaria';
$_lang['MULTILINGUISM'] = 'Multilingüismo';
$_lang['MULTILINGUISM_INFO'] = 'Le permite introducir elementos de texto en más de un idioma (traducciones). No lo habilite si 
	no va a usarlo, ya que el sitio se ralentizará sin razón. El interfaz de Elxis seguirá siendo multi-idioma incluso 
	si esta opción está puesta a No.';
$_lang['CHANGE_LANG'] = 'Cambiar idioma';
$_lang['LANG_CHANGE_WARN'] = 'Si cambiar el idioma por defecto podría haber inconsistencias entre los indicadores de 
	idiomas y las traducciones en la tabla de traducciones.';
$_lang['CACHE'] = 'Caché';
$_lang['CACHE_INFO'] = 'Elxis puede guardar el código HTML generado por elementos individuales en la memoria caché para una posterior recuperación más rápida. 
	Esta es una configuración general, debe también habilitar la caché en los elementos (ej. módulos) que desee que sean guardados en memoria caché.';
$_lang['APC_INFO'] = 'La caché alternativa de PHP (APC) es una caché opcode para PHP. Debe ser soportada por su servidor web. 
	No está recomendada en un hosting compartido. Elxis la usará en páginas especiales para mejorar el rendimiento.';
$_lang['APC_ID_INFO'] = 'En caso de más de 1 sitio hospedado en el servidor identifíquelo proporcionando un número 
	entero único para este sitio.';
$_lang['USERS_AND_REGISTRATION'] = 'Usuarios y Registro';
$_lang['PRIVACY_PROTECTION'] = 'Protección de privacidad';
$_lang['PASSWORD_NOT_SHOWN'] = 'La clave actual no es mostrada por motivos de seguridad. 
	Rellene este campo sólo si desea cambiar la clave actual.';
$_lang['DB_TYPE'] = 'Tipo de base de datos';
$_lang['ALERT_CON_LOST'] = 'Si lo cambia la conexión con la base de datos actual se perderá!';
$_lang['HOST'] = 'Host';
$_lang['PORT'] = 'Puerto';
$_lang['PERSISTENT_CON'] = 'Conexión constante';
$_lang['DB_NAME'] = 'Nombre de DB';
$_lang['TABLES_PREFIX'] = 'Prefijo de tablas';
$_lang['DSN_INFO'] = 'Un nombre de fuente de datos ya preparado para conectarse a la base de datos.';
$_lang['SCHEME'] = 'Modo';
$_lang['SCHEME_INFO'] = 'La ruta absoluta a un fichero de base de datos si usa una base de datos como SQLite.';
$_lang['SEND_METHOD'] = 'Método de envío';
$_lang['SMTP_OPTIONS'] = 'Opciones SMTP';
$_lang['AUTH_REQ'] = 'Autenticación requerida';
$_lang['SECURE_CON'] = 'Conexión segura';
$_lang['SENDER_NAME'] = 'Nombre del remitente';
$_lang['SENDER_EMAIL'] = 'E-mail del remitente';
$_lang['RCPT_NAME'] = 'Nombre del destinatario';
$_lang['RCPT_EMAIL'] = 'E-mail del destinatario';
$_lang['TECHNICAL_MANAGER'] = 'Administrador Técnico';
$_lang['TECHNICAL_MANAGER_INFO'] = 'El administrador técnico recibe las alertas de errores y seguridad.';
$_lang['USE_FTP'] = 'Usar FTP';
$_lang['PATH'] = 'Ruta';
$_lang['FTP_PATH_INFO'] = 'La ruta relativa del directorio raíz de la instalación de Elxis (ejemplo: /public_html).';
$_lang['SESSION'] = 'Sesión';
$_lang['HANDLER'] = 'Manejador';
$_lang['HANDLER_INFO'] = 'Elxis puede guardar las sesiones como archivos en el Repositorio o en la base de datos. También puede elegir 
	Ninguno para permitir a PHP guardar las sesiones en el directorio por defecto local del servidor.';
$_lang['FILES'] = 'Archivos';
$_lang['LIFETIME'] = 'Vida de la caché';
$_lang['SESS_LIFETIME_INFO'] = 'Tiempo para que la sesión expire cuando usted está inactivo.';
$_lang['CACHE_TIME_INFO'] = 'Después de este tiempo los elementos que están en la memoria caché serán regenerados.';
$_lang['MINUTES'] = 'minutos';
$_lang['HOURS'] = 'horas';
$_lang['MATCH_IP'] = 'Obtener IP';
$_lang['MATCH_BROWSER'] = 'Obtener navegador';
$_lang['MATCH_REFERER'] = 'Obtener HTTP Referrer';
$_lang['MATCH_SESS_INFO'] = 'Habilita una rutina avanzada de validación de sesión.';
$_lang['ENCRYPTION'] = 'Encriptación';
$_lang['ENCRYPT_SESS_INFO'] = '¿Encriptar los datos de la sesión?';
$_lang['ERRORS'] = 'Errores';
$_lang['WARNINGS'] = 'Advertencias';
$_lang['NOTICES'] = 'Avisos';
$_lang['NOTICE'] = 'Aviso';
$_lang['REPORT'] = 'Reportes';
$_lang['REPORT_INFO'] = 'Nivel de reporte de errores. En los sitios en produccicón se recomienda dejar apagado.';
$_lang['LOG'] = 'Registro Log';
$_lang['LOG_INFO'] = 'Nivel de registro de errores en los logs. Seleccione que errores desea que Elxis escriba en los 
	logs del sistema (repository/logs/).';
$_lang['ALERT'] = 'Alerta';
$_lang['ALERT_INFO'] = 'Enviar errores fatales al administrador técnico.';
$_lang['ROTATE'] = 'Rotación';
$_lang['ROTATE_INFO'] = 'Rotar errores al final de cada mes. Recomendado.';
$_lang['DEBUG'] = 'Depuración';
$_lang['MODULE_POS'] = 'Posiciones de los módulos';
$_lang['MINIMAL'] = 'Mínimo';
$_lang['FULL'] = 'Completo';
$_lang['DISPUSERS_AS'] = 'Mostrar usuarios como';
$_lang['USERS_REGISTRATION'] = 'Registro de usuarios';
$_lang['ALLOWED_DOMAIN'] = 'Dominio permitido';
$_lang['ALLOWED_DOMAIN_INFO'] = 'Escriba un nombre de dominio (ej. elxis.org). Sólo los correos con ese dominio 
	serán aceptados para el registro de usuarios.';
$_lang['EXCLUDED_DOMAINS'] = 'Excluir dominios';
$_lang['EXCLUDED_DOMAINS_INFO'] = 'Lista de nombres de dominio separados por coma (Ej. badsite.com,hacksite.com). Los correos con esos 
	dominios no podrán usarse para el registro de usuarios.';
$_lang['ACCOUNT_ACTIVATION'] = 'Activación de cuenta';
$_lang['DIRECT'] = 'Directa';
$_lang['MANUAL_BY_ADMIN'] = 'Manual por el administrador';
$_lang['PASS_RECOVERY'] = 'Recuperación de contraseña';
$_lang['SECURITY'] = 'Seguridad';
$_lang['SECURITY_LEVEL'] = 'Nivel de seguridad';
$_lang['SECURITY_LEVEL_INFO'] = 'Incrementando el nivel de seguridad algunas opciones son habilitadas a la fuerza mientras que otras 
	características pueden ser desabilitadas. Consulte la documentación de Elxis para saber más.';
$_lang['NORMAL'] = 'Normal';
$_lang['HIGH'] = 'Alta';
$_lang['INSANE'] = 'Demencial';
$_lang['ENCRYPT_METHOD'] = 'Método de encriptación';
$_lang['AUTOMATIC'] = 'Automático';
$_lang['ENCRYPTION_KEY'] = 'Clave de encriptación';
$_lang['ELXIS_DEFENDER'] = 'Defensor Elxis';
$_lang['ELXIS_DEFENDER_INFO'] = 'El defensor Elxis protege su sitio web de ataques XSS y SQL injection.
	Esta poderosa herramienta filtra las peticiones de usuario y bloquea los ataques a su sitio. También le notificará 
    de los ataques y los registrará. Puede seleccionar el tipo de filtros que puede aplicar o incluso blindar los
	ficheros cruciales de su sistema para modificaciones no autorizadas. Cuantos más filtros active más lento correrá su sistema.
	Recomentadmos que habilite las opcion G. Consulte la documentación de Elxis para saber más';
$_lang['SSL_SWITCH'] = 'Cambiar a SSL';
$_lang['SSL_SWITCH_INFO'] = 'Elxis cambiará automáticamente de HTTP to HTTPS en las páginas donde la privacidad es importante. 
	Para el área de administración el modo HTTPS será permanente. Requiere un certificado SSL!';
$_lang['PUBLIC_AREA'] = 'Área pública';
$_lang['GENERAL_FILTERS'] = 'Reglas generales';
$_lang['CUSTOM_FILTERS'] = 'Reglas personalizadas';
$_lang['FSYS_PROTECTION'] = 'Protección del sistema de ficheros';
$_lang['CHECK_FTP_SETS'] = 'Comprobar la configuración FTP';
$_lang['FTP_CON_SUCCESS'] = 'La conexión al servidor FTP fue correcta.';
$_lang['ELXIS_FOUND_FTP'] = 'La instalación de Elxis se encontró en el FTP.';
$_lang['ELXIS_NOT_FOUND_FTP'] = 'La instalación de Elxis no se encontró en el FTP! Revise la opción valor de la ruta FTP.';
$_lang['CAN_NOT_CHANGE'] = 'No puede cambiar esto.';
$_lang['SETS_SAVED_SUCC'] = 'Configuración guardada correctamente';
$_lang['ACTIONS'] = 'Acciones';
$_lang['BAN_IP_REQ_DEF'] = 'Para banear una dirección IP se requiere habilitar al menos una opción del defensor de Elxis!';
$_lang['BAN_YOURSELF'] = '¿Está intentando banearse usted mismo?';
$_lang['IP_AL_BANNED'] = 'Esta IP ya ha sido baneada!';
$_lang['IP_BANNED'] = 'La dirección IP %s fue baneada!';
$_lang['BAN_FAILED_NOWRITE'] = 'La prohibición falló! El archivo repository/logs/defender_ban.php no es reescribible.';
$_lang['ONLY_ADMINS_ACTION'] = 'Sólo los administradores pueden llevar a cabo esta acción!';
$_lang['CNOT_LOGOUT_ADMIN'] = 'No puede expulsar a un administrador!';
$_lang['USER_LOGGED_OUT'] = 'El usuario fue expulsado!';
$_lang['SITE_STATISTICS'] = 'Estadísticas del sitio';
$_lang['SITE_STATISTICS_INFO'] = 'Ver las estadísticas de tráfico del sitio';
$_lang['BACKUP'] = 'Respaldar';
$_lang['BACKUP_INFO'] = 'Realizar un respaldo completo y administrar los respaldos existentes.';
$_lang['BACKUP_FLIST'] = 'Lista de archivos de respaldo existentes';
$_lang['TYPE'] = 'Tipo';
$_lang['FILENAME'] = 'Nombre del archivo';
$_lang['SIZE'] = 'Tamaño';
$_lang['NEW_DB_BACKUP'] = 'Nuevo respaldo de la base de datos';
$_lang['NEW_FS_BACKUP'] = 'Nuevo respaldo del sistema de archivos';
$_lang['FILESYSTEM'] = 'Sistema de archivos';
$_lang['DOWNLOAD'] = 'Descargar';
$_lang['TAKE_NEW_BACKUP'] = '¿Realizar un nuevo respaldo?\nPuede llevar un rato, por favor sea paciente!';
$_lang['FOLDER_NOT_EXIST'] = "El directorio %s no existe!";
$_lang['FOLDER_NOT_WRITE'] = "El directorio %s no es reescribible!";
$_lang['BACKUP_SAVED_INTO'] = "Los archivos de respaldo se guardan en %s";
$_lang['CACHE_SAVED_INTO'] = "Los archivos de caché se guardan en %s";
$_lang['CACHED_ITEMS'] = 'Elementos en caché';
$_lang['ELXIS_ROUTER'] = 'Ruta de Elxis';
$_lang['ROUTING'] = 'Enrutamiento';
$_lang['ROUTING_INFO'] = 'Re-enrutar las peticiones de los usuarios a direcciones personalizadas.';
$_lang['SOURCE'] = 'Fuente';
$_lang['ROUTE_TO'] = 'Enrutar a';
$_lang['REROUTE'] = "Re-enrutar %s";
$_lang['DIRECTORY'] = 'Directorio';
$_lang['SET_FRONT_CONF'] = 'Seleccione la portada del sitio en la configuración de Elxis!';
$_lang['ADD_NEW_ROUTE'] = 'Añadir una nueva ruta';
$_lang['OTHER'] = 'Otro';
$_lang['LAST_MODIFIED'] = 'Última modificación';
$_lang['PERIOD'] = 'Periodo'; //time period
$_lang['ERROR_LOG_DISABLED'] = 'El registro de errores está desabilitado!';
$_lang['LOG_ENABLE_ERR'] = 'Los registros Log sólo están habilitados para los errores fatales.';
$_lang['LOG_ENABLE_ERRWARN'] = 'Los registros Log están habilitados para errores y advertencias.';
$_lang['LOG_ENABLE_ERRWARNNTC'] = 'Los registros Log están habilitados para errores, advertencias y avisos.';
$_lang['LOGROT_ENABLED'] = 'La rotación de registros logs está activada.';
$_lang['LOGROT_DISABLED'] = 'La rotación de los registros logs está desactivada!';
$_lang['SYSLOG_FILES'] = 'Registros log del sistema';
$_lang['DEFENDER_BANS'] = 'Prohibiciones IP del Defendesor';
$_lang['LAST_DEFEND_NOTIF'] = 'Última notificación del Defensor';
$_lang['LAST_ERROR_NOTIF'] = 'Última notificación de error';
$_lang['TIMES_BLOCKED'] = 'Número de veces bloqueado';
$_lang['REFER_CODE'] = 'Código de referencia';
$_lang['CLEAR_FILE'] = 'Limpiar archivo';
$_lang['CLEAR_FILE_WARN'] = 'Los contenidos del archivo serán borrados. ¿Continuar?';
$_lang['FILE_NOT_FOUND'] = 'Archivo no encontrado!';
$_lang['FILE_CNOT_DELETE'] = 'Este archivo no puede ser borrado!';
$_lang['ONLY_LOG_DOWNLOAD'] = 'Sólo los archvios con extensión .log pueden ser descargados!';
$_lang['SYSTEM'] = 'Sistema';
$_lang['PHP_INFO'] = 'Información PHP';
$_lang['PHP_VERSION'] = 'Versión PHP';
$_lang['ELXIS_INFO'] = 'Información Elxis';
$_lang['VERSION'] = 'Versión';
$_lang['REVISION_NUMBER'] = 'Número de revisión';
$_lang['STATUS'] = 'Estado';
$_lang['CODENAME'] = 'Pseudónimo';
$_lang['RELEASE_DATE'] = 'Fecha de salida';
$_lang['COPYRIGHT'] = 'Copyright';
$_lang['POWERED_BY'] = 'Creado con';
$_lang['AUTHOR'] = 'Autor';
$_lang['PLATFORM'] = 'Plataforma';
$_lang['HEADQUARTERS'] = 'Central';
$_lang['ELXIS_ENVIROMENT'] = 'EntonorElxis';
$_lang['DEFENDER_LOGS'] = 'Registros logsdel defensor';
$_lang['ADMIN_FOLDER'] = 'Directorio de administración';
$_lang['DEF_NAME_RENAME'] = 'Nombre por defecto, renómbrelo!';
$_lang['INSTALL_PATH'] = 'Ruta de instalación';
$_lang['IS_PUBLIC'] = 'Es público!';
$_lang['CREDITS'] = 'Créditos';
$_lang['LOCATION'] = 'Localización';
$_lang['CONTRIBUTION'] = 'Contribución';
$_lang['LICENSE'] = 'Licencia';
$_lang['MULTISITES'] = 'Multisitios';
$_lang['MULTISITES_DESC'] = 'Administrar múltiples sitios bajo una única instalación de Elxis.';
$_lang['MULTISITES_WARN'] = 'Usted puede tener varios sitios funcionando bajo una misma instalación de Elxis. Trabajar con multisitios
	es una tarea que requiere conocimientos avanzados de Elxis CMS. Antes de importar datos a un nuevo
	multisitio, asegúrese de que la base de datos existe. Después de crear un nuevo multisitio edite el archivo htaccess
	con las instrucciones recibidas. Borrar un multisitio no borra la base de datos relacionada. Consulte a un
	técnico experimentado si necesita ayuda.';
$_lang['MULTISITES_DISABLED'] = 'Los multisitios están desabilitados!';
$_lang['ENABLE'] = 'Habilitar';
$_lang['ACTIVE'] = 'Activar';
$_lang['URL_ID'] = 'Identificador URL';
$_lang['MAN_MULTISITES_ONLY'] = "Usted únicamente puede administrar los multisitios desde el sitio %s";
$_lang['LOWER_ALPHANUM'] = 'Caracteres alfanuméricos sin espacios';
$_lang['IMPORT_DATA'] = 'Importar datos';
$_lang['CNOT_CREATE_CFG_NEW'] = "No se puede crear el archivo de configuración %s en el nuevo sitio!";
$_lang['DATA_IMPORT_FAILED'] = 'Falló la importación de datos!';
$_lang['DATA_IMPORT_SUC'] = 'Datos importados correctamente!';
$_lang['ADD_RULES_HTACCESS'] = 'Añada las siguientes reglas al archivo htaccess';
$_lang['CREATE_REPOSITORY_NOTE'] = 'Se recomienda encarecidamente crear un repositorio separado para cada subsitio!';
$_lang['NOT_SUP_DBTYPE'] = 'Tipo de base de datos no soportada!';
$_lang['DBTYPES_MUST_SAME'] = 'El tipo de base de datos de este sitio y del nuevo deben ser iguales!';
$_lang['DISABLE_MULTISITES'] = 'Desactivar multisitios';
$_lang['DISABLE_MULTISITES_WARN'] = 'Todos los sitios salvo el que tiene id 1 serán borrados!';
$_lang['VISITS_PER_DAY'] = "Visitas por día en %s"; //translators help: ... for {MONTH YEAR}
$_lang['CLICKS_PER_DAY'] = "Clicks por día en %s"; //translators help: ... for {MONTH YEAR}
$_lang['VISITS_PER_MONTH'] = "Visitas por mes en %s"; //translators help: ... for {YEAR}
$_lang['CLICKS_PER_MONTH'] = "Clicks por mes en %s"; //translators help: ... for {YEAR}
$_lang['LANGS_USAGE_FOR'] = "Porcentaje de idiomas usados en %s"; //translators help: ... for {MONTH YEAR}
$_lang['UNIQUE_VISITS'] = 'Visitas únicas';
$_lang['PAGE_VIEWS'] = 'Páginas vistas';
$_lang['TOTAL_VISITS'] = 'Visitas totales';
$_lang['TOTAL_PAGE_VIEWS'] = 'Páginas vistas';
$_lang['LANGS_USAGE'] = 'Uso de idiomas';
$_lang['LEGEND'] = 'Leyenda';
$_lang['USAGE'] = 'Uso';
$_lang['VIEWS'] = 'Vistas';
$_lang['OTHER'] = 'Otros';
$_lang['NO_DATA_AVAIL'] = 'No hay datos disponibles';
$_lang['PERIOD'] = 'Periodo';
$_lang['YEAR_STATS'] = 'Estadísticas anuales';
$_lang['MONTH_STATS'] = 'Estadísiticas mensuales';
$_lang['PREVIOUS_YEAR'] = 'Año anterior';
$_lang['NEXT_YEAR'] = 'Año siguiente';
$_lang['STATS_COL_DISABLED'] = 'La recopilación de datos estadísticos está desabilitada! Habilite las estadísticas en la configuración de Elxis.';
$_lang['DOCTYPE'] = 'Tipo de documento';
$_lang['DOCTYPE_INFO'] = 'La opción recomendada es HTML5. Elxis generará la salida XHTML incluso si usted marca el DOCTYPE a HTML5. 
En los documentos XHTML, Elxis sirve documentos con el tipo mime application/xhtml+xml en los navegadores modernos y con text/html en los antiguos.';
$_lang['ABR_SECONDS'] = 'seg';
$_lang['ABR_MINUTES'] = 'min';
$_lang['HOUR'] = 'hora';
$_lang['HOURS'] = 'horas';
$_lang['DAY'] = 'día';
$_lang['DAYS'] = 'días';
$_lang['UPDATED_BEFORE'] = 'Actualizado antes de';
$_lang['CACHE_INFO'] = 'Ver y borrar los elementeos guardados en caché.';
$_lang['ELXISDC'] = 'Centro de Descargas Elxis';
$_lang['ELXISDC_INFO'] = 'Explorar el EDC online y ver las extensiones disponibles';
$_lang['SITE_LANGS'] = 'Idiomas del sitio';
$_lang['SITE_LANGS_DESC'] = 'Por defecto todos los idiomas están disponibles en la parte pública del sitio. 
	Usted puede cambiar ésto seleccionando aquí sólo los idiomas que desea que estén disponibles.';
//Elxis 4.1
$_lang['PERFORMANCE'] = 'Rendimiento';
$_lang['MINIFIER_CSSJS'] = 'Minimizador CSS/Javascript';
$_lang['MINIFIER_INFO'] = 'Elxis puede unificar los archivos CSS y JS locales y, opcionalmente, comprimirlos. El archivo unificado será guardado en caché.
De esta forma, en lugar de tener múltiples archivos CSS/JS en el Head de sus páginas, sólo tendrá un único archivo unificado.';
$_lang['MOBILE_VERSION'] = 'Versión móvil';
$_lang['MOBILE_VERSION_DESC'] = '¿Habilitar una versión amigable para dispositivos móviles?';
//Elxis 4.2
$_lang['SEND_TEST_EMAIL'] = 'Enviar correo electrónico de prueba';
$_lang['ONLINE_USERS'] = 'Online for users';
$_lang['CRONJOBS'] = 'Cron jobs';
$_lang['CRONJOBS_INFO'] = 'Enable cron jobs if you want to run automated tasks like scheduled articles publishing.';
$_lang['LANG_DETECTION'] = 'Detección de idioma';
$_lang['LANG_DETECTION_INFO'] = 'Νative language detection and redirection to the proper language version of the site on first visit to frontpage.';
//Elxis 4.4
$_lang['DEFENDER_NOTIFS'] = 'Notificaciones Defender';
$_lang['XFRAMEOPT_HELP'] = 'HTTP header that controls if the browser will accept or refuse displaying pages from this site inside an frame. Helps avoiding clickjacking attacks.';
$_lang['ACCEPT_XFRAME'] = 'Acepte X-Frame';
$_lang['DENY'] = 'Negar';
$_lang['SAMEORIGIN'] = 'Same origin';
$_lang['ALLOW_FROM'] = 'Allow from';
$_lang['ALLOW_FROM_ORIGIN'] = 'Allow from origin';
$_lang['CONTENT_SEC_POLICY'] = 'Content Security Policy';
$_lang['IP_RANGES'] = 'IP Ranges';
$_lang['UPDATED_AUTO'] = 'Actualización automática';
$_lang['CHECK_IP_MOMENT'] = 'Compruebe momento IP';
$_lang['BEFORE_LOAD_ELXIS'] = 'Before loading Elxis core';
$_lang['AFTER_LOAD_ELXIS'] = 'After loading Elxis core';
$_lang['CHECK_IP_MOMENT_HELP'] = 'BEFORE: Defender checks IPs on each click. Bad IPs dont reach Elxis core. 
	AFTER: Defender checks IPs only once per session (performance improvement). Bad IPs reach Elxis core before they get blocked.';
$_lang['SECURITY'] = 'Seguridad';
$_lang['EVERYTHING'] = 'Todo';
$_lang['ONLY_ATTACKS'] = 'Solamente los ataques';
$_lang['CRONJOBS_PROB'] = 'Cron jobs probabilidad';
$_lang['CRONJOBS_PROB_INFO'] = 'The percentage probability of executing Cron jobs on each user click. Affects only cron jobs executed internally by Elxis. For best performance the more visitors your site has the lower this value should be. The default value is 10%.';
$_lang['EXTERNAL'] = 'Externo';
$_lang['SEO_TITLES_MATCH'] = 'SEO Títulos partido';
$_lang['SEO_TITLES_MATCH_HELP'] = 'Controls the generation of SEO Titles from normal titles. Exact creates SEO Titles that match exactly the original titles ones transliterated.';
$_lang['EXACT'] = 'Exacto';
//Elxis 4.6
$_lang['CONFIG_FOR_GMAIL'] = 'Configuración de Gmail';
$_lang['AUTH_METHOD'] = 'Método de autentificación';
$_lang['DEFAULT'] = 'Defecto';
$_lang['BACKUP_EXCLUDE_PATHS_HELP'] = 'You can exclude some folders from the filesystem backup procedure. This is extremely 
	usefull if you have a large filesystem and backup fails to complete due to memory issues. Provide below the folders you want 
	to exclude by giving their relative paths. Example: media/videos/';
$_lang['PATHS_EXCLUDED_FSBK'] = 'Rutas excluidas de la copia de seguridad del sistema de archivos';
$_lang['EXCLUSIONS'] = 'Exclusiones';
//Elxis 5.0
$_lang['BACKUP_FOLDER_TABLE_TIP'] = 'For file system backup you can select to backup the whole Elxis installation or a specific folder. 
	For database you can backup the whole database or a specific table. In case you get timeout or memory errors during backup (especially 
	on large sites) select to backup individual folders or tables instead.';
$_lang['FOLDER'] = 'Folder';
$_lang['TABLE'] = 'Table';
$_lang['INACTIVE'] = 'Inactivo';
$_lang['DEPRECATED'] = 'Deprecated';
$_lang['ALL_AVAILABLE'] = 'Todos disponibles';
$_lang['NO_PROTECTION'] = 'Sin protección';
$_lang['NEWER_VERSION_FOR'] = 'Hay una versión más nueva (%s) para  %s';
$_lang['NEWER_VERSIONS_FOR'] = 'Hay nuevas versiones para %s';
$_lang['NEWER_VERSIONS_FOR_EXTS'] = 'Hay nuevas versiones para %s extensiones';
$_lang['OUTDATED_ELXIS_UPDATE_TO'] = '¡Usas una versión anticuada de Elxis! Actualizar lo antes posible a %s';
$_lang['NO_BACKUPS'] = '¡No tiene copias de seguridad!';
$_lang['LONGTIME_TAKE_BACKUP'] = 'You have a long time to take a site backup';
$_lang['DELETE_OLD_LOGS'] = 'Eliminar archivos de registro antiguos';
$_lang['DEFENDER_IS_DISABLED'] = 'Elxis Defender está deshabilitado';
$_lang['REPO_DEF_PATH'] = 'El repositorio está en la ruta predeterminada';
$_lang['CHANGE_MAIL_TO_SMTP'] = 'Cambiar el correo PHP a SMTP o si no';
$_lang['DISABLE_MULTILINGUISM'] = 'Deshabilitar el multilingüismo';
$_lang['ENABLE_MULTILINGUISM'] = 'Habilitar el multilingüismo';
//Elxis 5.2
$_lang['NOTFOUND'] = 'Extraviado';
$_lang['EXTENSION'] = 'Extensión';
$_lang['CODE_EDITOR_WARN'] = 'We strongly recommend not to modify extensions\'s files because you will lose your changes after an update. 
	Add your custom or overwrite CSS rules on <strong>user.config</strong> files instead.';
$_lang['EDIT_CODE'] = 'Editar código';
$_lang['EXCLUDED_IPS'] = 'IP excluidas';

?>