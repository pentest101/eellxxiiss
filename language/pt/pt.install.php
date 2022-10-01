<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: pt-PT (Portuguese - Portugal) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Luciano Neves ( luckybano@gmail.com )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['INSTALLATION'] = 'Instalação';
$_lang['STEP'] = 'Passo';
$_lang['VERSION'] = 'Versão';
$_lang['VERSION_CHECK'] = 'Verificar versão';
$_lang['STATUS'] = 'Status';
$_lang['REVISION_NUMBER'] = 'Número de revisão';
$_lang['RELEASE_DATE'] = 'Release date';
$_lang['ELXIS_INSTALL'] = 'Elxis instalação';
$_lang['LICENSE'] = 'Licença';
$_lang['VERSION_PROLOGUE'] = 'Você está prestes a instalar CMS do ELxis. A versão exata da cópia do Elxis que você está prestes a instalar é mostrada abaixo.
	a instalar é mostrada abaixo. Por favor certifique-se de que esta é a versão mais recente do Elxis lançada
	em <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = 'Antes de começar';
$_lang['BEFORE_DESC'] = 'Antes de prosseguir, por favor leia atentamente o seguinte.';
$_lang['DATABASE'] = 'Base de dados';
$_lang['DATABASE_DESC'] = 'Crie uma base de dados vazia qual será usada por o ELxis para armazenar os seu dados. Nós 
	recomendamos  a usar uma base de dado <strong>MySQL</strong>. Embora o Lexis tenha suporte de backend para outros tipos de banco de dados 
	para outros tipos de banco de dados tais como PostgreSQL e SQLite 3 ele só funciona mesmo bem com MySQL. Para criar uma 
	base de dados vazia de MySQL você deve fazê-lo a partir do seu painel de controle de alojamento (CPanel, Plesk, ISP Config, etc) ou a partir de 
	phpMyAdmin ou outras ferramentes de gestão de base de dados. Simplesmente forneça um <strong>nome</strong> para a sua base de dados e crie uma. 
	Depois disso, crie um banco de dados e <strong>utilizador</strong> e atribuía-o à sua base de dados recentemente criada. Anote o nome da basede dados,
	o nome de usuário e a senha, você vai precisar deles mais tarde durante a instalação.';
$_lang['REPOSITORY'] = 'Repositório';
$_lang['REPOSITORY_DESC'] = 'Elxis usa uma pasta especial para armazenar páginas em cache, arquivos de log, sessões, backups e muito mais. Por 
	esta pasta é nomeada <strong>repository</strong> e é colocado dentro da pasta de raiz do Elxis. Esta pasta 
	<strong>deve ser gravável</strong>! Recomendamos fortemente a  <strong>renomear</strong> esta pasta e a <strong>movê-la</strong>
	num lugar que não seja acessível a partir da web. Após este passo se você ativar <strong>abrir basedir</strong> proteção no PHP 
	Você poderá precisar de incluir o caminho do repositório dentro dos caminhos permitidos.';
$_lang['REPOSITORY_DEFAULT'] = 'Repositório está dentro da sua localização predefinida!';
$_lang['SAMPLE_ELXPATH'] = 'Amostra do caminho do Elxis';
$_lang['DEF_REPOPATH'] = 'Caminho predefinido do repositório';
$_lang['REQ_REPOPATH'] = 'Caminho recomendado para o repositório';
$_lang['CONTINUE'] = 'Continuar';
$_lang['I_AGREE_TERMS'] = 'Eu li, entendi e concordo com os termos e condições EPL.';
$_lang['LICENSE_NOTES'] = 'Elxis CMS é um software grátis e lançado atravéd do <strong>Elxis Public License</strong> (EPL). 
	Para continuar esta instalação e utilização do Elxis você deve concordar com os termos e condições da EPL. Leia atentamente
	a licença Elxis e, se concordar marque a caixa de seleção na parte inferior da página e clique em Continuar.  Se não, 
	pare a instalação e exclua os arquivos Elxis.';
$_lang['SETTINGS'] = 'Configurações';
$_lang['SITE_URL'] = 'URL do site';
$_lang['SITE_URL_DESC'] = 'Sem barra diagonal no final (eg. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'O caminho completo para a pasta de repositório do Elxis. Deixe-o vazio para o caminho e o nome predefinido.';
$_lang['SETTINGS_DESC'] = 'Definir parâmetros de configuração necessários do Elxis. Alguns parâmetros têm que ser definidos antes da instalação do ELxis
	instalação do ELxis. Depois de concluir a instalação incicia a sessão na consola da administração para configurar os restantes parâmetros. 
	Esta deve ser sua primeira tarefa de administrador.';
$_lang['DEF_LANG'] = 'Língua predefinida';
$_lang['DEFLANG_DESC'] = 'O conteúdo é escrito na língua predefinda. Conteúdo de outras línguas é traduzido de 
	do conteúdo original da língua predefenida.';
$_lang['ENCRYPT_METHOD'] = 'Métode de encriptação';
$_lang['ENCRYPT_KEY'] = 'Chave de encriptação';
$_lang['AUTOMATIC'] = 'Automático';
$_lang['GEN_OTHER'] = 'Gerar outro';
$_lang['SITENAME'] = 'Nome do site';
$_lang['TYPE'] = 'Tipo';
$_lang['DBTYPE_DESC'] = 'Nós recomendamos fortemente o MySQL. Sómente os drivers suportados e o instalador do seu sitema do elxis é q são selecionáves.';
$_lang['HOST'] = 'Host';
$_lang['TABLES_PREFIX'] = 'Tabelas de terminações';
$_lang['DSN_DESC'] = 'Você pode em vez disso indicar um nome da fonte de dados para uso imediato, conectando-se assim ao banco de dados.';
$_lang['SCHEME'] = 'Esquema';
$_lang['SCHEME_DESC'] = 'O caminho completo para o+uma base de dados em caso de usar uma base de dados tal como SQLite.';
$_lang['PORT'] = 'Porta';
$_lang['PORT_DESC'] = 'A porta predefinida para o MySQL é 3306. Deixe-a com um 0 para a seleção automática.';
$_lang['FTPPORT_DESC'] = 'A porta predefinida para FTP é 21. Deixe-o com um 0 para a seleção automática.';
$_lang['USE_FTP'] = 'Usar FTP';
$_lang['PATH'] = 'Caminho';
$_lang['FTP_PATH_INFO'] = 'O caminho relacionado com a pasta raiz do FTP até à pasta de instalação do ELxis (example: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'Verificar configurações do FTP';
$_lang['CHECK_DB_SETS'] = 'Verificar configurações da base de dados';
$_lang['DATA_IMPORT'] = 'Dados de importação';
$_lang['SETTINGS_ERRORS'] = 'As configurações que você definiu contém erros!';
$_lang['NO_QUERIES_WARN'] = 'Os dados iniciais  que foram importados, aparentam não ter sido consultados. Certifique-se 
	de que os dados sejam realmente importados antes de prosseguir.';
$_lang['RETRY_PREV_STEP'] = 'Repetir passo anterior';
$_lang['INIT_DATA_IMPORTED'] = 'Os dados iniciais forma importados para dentro da base de dados.';
$_lang['QUERIES_EXEC'] = "%s SQL consultas executadas."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Conta de administrador';
$_lang['CONFIRM_PASS'] = 'Confirmar palavra passe';
$_lang['AVOID_COMUNAMES'] = 'Evite nomes comuns, como admin e administrador.';
$_lang['YOUR_DETAILS'] = 'Seus detalhes';
$_lang['PASS_NOMATCH'] = 'As senhas não correspondem!';
$_lang['REPOPATH_NOEX'] = 'Caminho do repositório não existe!';
$_lang['FINISH'] = 'Terminar';
$_lang['FRIENDLY_URLS'] = 'URLs amigáveis';
$_lang['FRIENDLY_URLS_DESC'] = 'Recomendamos fortemente a sua ativação. Para poder trabalhar, o Elxis vai tentar renomear o ficheiro htaccess.txt para 
	<strong>.htaccess</strong> . No caso de já existir um ficheiro .htaccess na mesma pasta os dois serão eliminados.';
$_lang['GENERAL'] = 'Geral';
$_lang['ELXIS_INST_SUCC'] = 'Instalação do Elxis concluída com sucesso.';
$_lang['ELXIS_INST_WARN'] = 'Instalação do Elxis concluída com alguns avisos.';
$_lang['CNOT_CREA_CONFIG'] = 'Não foi possível criar o ficheiro <strong>configuration.php</strong> na pasta de raiz do Elxis.';
$_lang['CNOT_REN_HTACC'] = 'Não foi possível renomear o ficheiro <strong>htaccess.txt</strong> para <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = 'Ficheiro de configuração';
$_lang['CONFIG_FILE_MANUAL'] = 'Crie manulamente o ficheiro configuration.php , copie o seguinte código e cole-o dentro da pasta.';
$_lang['REN_HTACCESS_MANUAL'] = 'Por favor renomeie manualmente o ficheiro <strong>htaccess.txt</strong> para <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = 'O que fazer a seguir?';
$_lang['RENAME_ADMIN_FOLDER'] = 'Para reforçar a segurança, você pode renomear a pasta de administração (<em>estia</em>) para o que você quiser.. 
	Ao fazê-lo, você deve também atualizar o ficheiro .htaccess para o novo nome.';
$_lang['LOGIN_CONFIG'] = 'Entrar na seção de administração e configurar adequadamente as restantes opções de configuração.';
$_lang['VISIT_NEW_SITE'] = 'Visite o seu novo site';
$_lang['VISIT_ELXIS_SUP'] = 'Visite o site de supporte do Elxis';
$_lang['THANKS_USING_ELXIS'] = 'Obrigado por usar o Elxis CMS.';
//Elxis 5.0
$_lang['OTHER_LANGS'] = 'Outros idiomas';
$_lang['OTHER_LANGS_DESC'] = 'Quais outros idiomas, exceto o padrão, você quer estar disponível?';
$_lang['ALL_LANGS'] = 'Todos';
$_lang['NONE_LANGS'] = 'Nenhum';
$_lang['REMOVE'] = 'Remover';
$_lang['CONFIG_EMAIL_DISPATCH'] = 'Configurar envio de email (opcional)';
$_lang['SEND_METHOD'] = 'Enviar método';
$_lang['RECOMMENDED'] = 'recomendado';
$_lang['SECURE_CONNECTION'] = 'Conexão segura';
$_lang['AUTH_REQUIRED'] = 'Autenticação requerida';
$_lang['AUTH_METHOD'] = 'Método de autenticação';
$_lang['DEFAULT_METHOD'] = 'Padrão';

?>