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


$_lang = array();
$_lang['INSTALLATION'] = 'Instalación';
$_lang['STEP'] = 'Paso';
$_lang['VERSION'] = 'Versión';
$_lang['VERSION_CHECK'] = 'Comprobar Versión';
$_lang['STATUS'] = 'Estado';
$_lang['REVISION_NUMBER'] = 'Número de revisión';
$_lang['RELEASE_DATE'] = 'Fecha de salida';
$_lang['ELXIS_INSTALL'] = 'Instalación de Elxis';
$_lang['LICENSE'] = 'Licencia';
$_lang['VERSION_PROLOGUE'] = 'Está usted a punto de instalar Elxis CMS. La versión exacta de la copia de Elxis que va a instalar es mostrada abajo. Por favor, asegúrese de que es la última versión que hay en la página oficial <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = 'Antes de empezar';
$_lang['BEFORE_DESC'] = 'Antes de ir más allá debe leer con detenimiento lo siguiente.';
$_lang['DATABASE'] = 'Base de datos';
$_lang['DATABASE_DESC'] = 'Cree una base de datos para que Elxis almacene en ella sus datos. Recomendamos encarecidamente una base de datos <strong>MySQL</strong>. Aunque Elxis soporta otros tipos de bases de datos como PostgreSQL y SQLite3 el sistema ha sido testado ampliamente con MYSQL. Para crear una base de datos vacía vaya al panel de contral de hosting (CPanel, Plesk, ISP Config, etc) o desde phpMyAdmin u otras herramientas para el control de las bases de datos. Simplmente proporcione un <strong>nombre</strong> para su base de datos y creela. Después, cree un <strong>usuario</strong> y asígnelo a la recién creada base de datos. Tome nota del nombre de la base de datos, el usuario y la clave; necesitará estos datos más adelante para realizar la instalación.';
$_lang['REPOSITORY'] = 'Repositorio';
$_lang['REPOSITORY_DESC'] = 'Elxis usa un directorio especial para almacenar las páginas en caché, los ficheros log, las sesiones, los ficheros de respaldo y más elementos. Por defecto este directorio se llama <strong>repository</strong> y está situado entro del directorio raíz de Elxis. Este directorio <strong>debe tener permisos de escritura</strong>! Recomendamos encarecidamente <strong>renombrar</strong> este directorio y moverlo a un lugar no accesible desde la web. Después de moverlo, si usted ha habilitado la protección <strong>open basedir</strong> en PHP, deberá también incluir la ruta del repositorio en sus rutas permitidas.';
$_lang['REPOSITORY_DEFAULT'] = 'El repositorio está en su localización por defecto!';
$_lang['SAMPLE_ELXPATH'] = 'Ruta de ejemplo de Elxis';
$_lang['DEF_REPOPATH'] = 'Ruta por defecto del repositorio';
$_lang['REQ_REPOPATH'] = 'Ruta recomendada para el repositorio';
$_lang['CONTINUE'] = 'Continuar';
$_lang['I_AGREE_TERMS'] = 'He leído, comprendido y acepto los terminos y condiciones de EPL';
$_lang['LICENSE_NOTES'] = 'Elxis CMS es un software gratuíto bajo la <strong>Licencia Pública de Elxis</strong> (EPL). 
	Para continuar con la instalación y usar Elxis, usted debe aceptar los términos y condiciones de de la EPL. Lea detenidamente 
	la licencia de Elxis y si la acepta marque la casilla al final de la página y haga click en Continuar. Si no, 
	detenga la instalación y borre los ficheros de Elxis.';
$_lang['SETTINGS'] = 'Configuraciones';
$_lang['SITE_URL'] = 'URL del sitio';
$_lang['SITE_URL_DESC'] = 'Sin barra (ej. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'La ruta absoluta al repositorio de Elxis. Déjelo vacío para la ruta y nombre por defecto.';
$_lang['SETTINGS_DESC'] = 'Configure los parámetros de configuración necesarios de Elxis. Es necesario que algunos parámetros sean configurados antes de la instalación de Elxis. Una vez se haya completado la instalación, entre en la consola de administración y configure el resto de parámetros. Esa debería ser su primera tarea como administrador.';
$_lang['DEF_LANG'] = 'Idioma por defecto';
$_lang['DEFLANG_DESC'] = 'El contenido está en escrito en el idioma por defecto. El contenido en otros idiomas es traducido del original en el idioma por defecto.';
$_lang['ENCRYPT_METHOD'] = 'Método de encriptación';
$_lang['ENCRYPT_KEY'] = 'Clave de encriptación';
$_lang['AUTOMATIC'] = 'Automática';
$_lang['GEN_OTHER'] = 'Generar otra';
$_lang['SITENAME'] = 'Nombre del Sitio';
$_lang['TYPE'] = 'Tipo';
$_lang['DBTYPE_DESC'] = 'Recomendamos encarecidamente MySQL. Sólo los seleccionables son soportados por su sistema y el instalador de Elxis.';
$_lang['HOST'] = 'Host';
$_lang['TABLES_PREFIX'] = 'Prefijo de las tablas';
$_lang['DSN_DESC'] = 'En su lugar puede proporcionar el nombre de una fuente de datos preparada para conectarse a la base de datos.';
$_lang['SCHEME'] = 'Modo';
$_lang['SCHEME_DESC'] = 'Ruta absoluta al fichero de base de datos si usted usa una base de datos como SQLite.';
$_lang['PORT'] = 'Puerto';
$_lang['PORT_DESC'] = 'El puerto por defecto de MySQL is 3306. Déjelo a 0 para selección automática.';
$_lang['FTPPORT_DESC'] = 'El puerto de salida para el FTP es 21. Déjelo a 0 para autoselección.';
$_lang['USE_FTP'] = 'Uar FTP';
$_lang['PATH'] = 'Ruta';
$_lang['FTP_PATH_INFO'] = 'La ruta relativa para el directorio FTP raíz de la instación de Elxis (por ejemplo: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'Comprobar la configuración FTP';
$_lang['CHECK_DB_SETS'] = 'Comprobar la configuración de la base de datos';
$_lang['DATA_IMPORT'] = 'Importar datos';
$_lang['SETTINGS_ERRORS'] = 'La configuración proporcionada contiene errores!';
$_lang['NO_QUERIES_WARN'] = 'Los datos iniciales fueron importados a la base de datos pero parece que no se ejecutó ninguna consulta. Asegúrese de que los datos fueron efectivamente importados antes de continuar.';
$_lang['RETRY_PREV_STEP'] = 'Reintentar paso anterior';
$_lang['INIT_DATA_IMPORTED'] = 'Datos iniciales importados a la base de datos.';
$_lang['QUERIES_EXEC'] = "%s consultas SQL ejecutadas."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Cuenta de administrador';
$_lang['CONFIRM_PASS'] = 'Confirmar contraseña';
$_lang['AVOID_COMUNAMES'] = 'Evite nombres comunes como admin o administrador.';
$_lang['YOUR_DETAILS'] = 'Sus datos';
$_lang['PASS_NOMATCH'] = 'Las contraseñas no coinciden!';
$_lang['REPOPATH_NOEX'] = 'La ruta del repositorio no existe!';
$_lang['FINISH'] = 'Finalizar';
$_lang['FRIENDLY_URLS'] = 'URLs amigables';
$_lang['FRIENDLY_URLS_DESC'] = 'Recomendamos encarecidamente su activación. Para funcionar, Elxis tratará de renombrar el fichero htaccess.txt a <strong>.htaccess</strong>. Si ya existe otro fichero .htaccess en el mismo directorio, será borrado.';
$_lang['GENERAL'] = 'General';
$_lang['ELXIS_INST_SUCC'] = 'Instalación de Elxis completada con éxito.';
$_lang['ELXIS_INST_WARN'] = 'Instalación de Elxis completada con advertencias.';
$_lang['CNOT_CREA_CONFIG'] = 'No se pudo crear el fichero <strong>configuration.php</strong> en el directorio raiz de Elxis.';
$_lang['CNOT_REN_HTACC'] = 'No se pudo renombrar el fichero <strong>htaccess.txt</strong> a <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = 'Fichero de configuración';
$_lang['CONFIG_FILE_MANUAL'] = 'Cree manualmente el fichero configuration.php, copie el siguiente código y péguelo dentro.';
$_lang['REN_HTACCESS_MANUAL'] = 'Por favor, renombre manualmente el fichero <strong>htaccess.txt</strong> a <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = '¿Qué desea hacer ahora?';
$_lang['RENAME_ADMIN_FOLDER'] = 'Para aumentar la seguridad puede cambiar el nombre del directorio de administración por defecto (<em>estia</em>) por lo que usted desee. 
	Si lo hace, debe también cambiarlo el fichero .htaccess.';
$_lang['LOGIN_CONFIG'] = 'Entre al panel de administración y configure correctamente el resto de opciones.';
$_lang['VISIT_NEW_SITE'] = 'Visitar su nuevo sitio web';
$_lang['VISIT_ELXIS_SUP'] = 'Visitar el centro de soporte de Elxis';
$_lang['THANKS_USING_ELXIS'] = 'Gracias por usar Elxis CMS.';
//Elxis 5.0
$_lang['OTHER_LANGS'] = 'Otros idiomas';
$_lang['OTHER_LANGS_DESC'] = '¿Qué otros idiomas, excepto el predeterminado, desea que estén disponibles?';
$_lang['ALL_LANGS'] = 'Todos';
$_lang['NONE_LANGS'] = 'Ninguno';
$_lang['REMOVE'] = 'Eliminar';
$_lang['CONFIG_EMAIL_DISPATCH'] = 'Configurar envío de correo electrónico (opcional)';
$_lang['SEND_METHOD'] = 'Método de envío';
$_lang['RECOMENDADO'] = 'recomendado';
$_lang['SECURE_CONNECTION'] = 'Conexión segura';
$_lang['AUTH_REQUIRED'] = 'Se requiere autenticación';
$_lang['AUTH_METHOD'] = 'Método de autenticación';
$_lang['DEFAULT_METHOD'] = 'Defecto';

?>