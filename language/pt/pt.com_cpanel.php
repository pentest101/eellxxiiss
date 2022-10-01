<?php 
/**
* @version: 5.2
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( https://www.elxis.org )
* @copyright: (C) 2006-2021 Elxis.org. All rights reserved.
* @description: pt-PT (Portuguese - Portugal) language for component CPanel
* @license: Elxis public license https://www.elxis.org/elxis-public-license.html
* @translator: Luciano Neves ( luckybano@gmail.com )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['CONTROL_PANEL'] = 'Painel de controle';
$_lang['GENERAL_SITE_SETS'] = 'Definições gerais do web site';
$_lang['LANGS_MANAGER'] = 'Gestor de línguas';
$_lang['MANAGE_SITE_LANGS'] = 'Gerir línguas do site';
$_lang['USERS'] = 'Utilizadores';
$_lang['MANAGE_USERS'] = 'Crie, edite e exclua as contas de utilizador';
$_lang['USER_GROUPS'] = 'Gupos de utilizador';
$_lang['MANAGE_UGROUPS'] = 'Gerir grupos de utilizador';
$_lang['MEDIA_MANAGER'] = 'Gestor de mídia';
$_lang['MEDIA_MANAGER_INFO'] = 'Gerir ficheiros multimídia';
$_lang['ACCESS_MANAGER'] = 'Aceder ao gestor';
$_lang['MANAGE_ACL'] = 'Gerir as Listas de Controle';
$_lang['MENU_MANAGER'] = 'Gestor de menu';
$_lang['MANAGE_MENUS_ITEMS'] = 'Gerir menus e itens de menu';
$_lang['FRONTPAGE'] = 'Frontpage';
$_lang['DESIGN_FRONTPAGE'] = 'Design do frontpage do site';
$_lang['CATEGORIES_MANAGER'] = 'Gestor de categorias';
$_lang['MANAGE_CONT_CATS'] = 'Gerir conteúdo das categorias';
$_lang['CONTENT_MANAGER'] = 'Gestor de conteúdos';
$_lang['MANAGE_CONT_ITEMS'] = 'Gerir itens de conteúdo';
$_lang['MODULES_MANAGE_INST'] = 'Gerir módulos e instalar novos.';
$_lang['PLUGINS_MANAGE_INST'] = 'Gerir os plugins de conteúdo e instalar novos.';
$_lang['COMPONENTS_MANAGE_INST'] = 'Gerir componentes e instalar novos.';
$_lang['TEMPLATES_MANAGE_INST'] = 'Gerir os templates e instalar novos.';
$_lang['SENGINES_MANAGE_INST'] = 'Gerir motores de pesquisa e instalar novos.';
$_lang['MANAGE_WAY_LOGIN'] = 'Gerir as maneiras como  os utilizadores podem fazer o login no site.';
$_lang['TRANSLATOR'] = 'Tradutor';
$_lang['MANAGE_MLANG_CONTENT'] = 'Gerir conteúdos multilingues';
$_lang['LOGS'] = 'Logins';
$_lang['VIEW_MANAGE_LOGS'] = 'Visualizar e gerir ficheiros de login';
$_lang['GENERAL'] = 'Geral';
$_lang['WEBSITE_STATUS'] = 'Status do site';
$_lang['ONLINE'] = 'Online';
$_lang['OFFLINE'] = 'Offline';
$_lang['ONLINE_ADMINS'] = 'Online apenas para administradores';
$_lang['OFFLINE_MSG'] = 'Mensagem Offline';
$_lang['OFFLINE_MSG_INFO'] = 'Deixe este campo vazio para exibir uma mensagem multilingue automática';
$_lang['SITENAME'] = 'Nome do site';
$_lang['URL_ADDRESS'] = 'Endereço URL';
$_lang['REPO_PATH'] = 'Caminho do repositório';
$_lang['REPO_PATH_INFO'] = 'O caminho completo para a pasta do repositório do Elxis. Deixe-o vazio para usar a localização 
	predefinda (elxis_root/repository/). Nós recomendamos fortemente que você mova essa pasta acima da pasta WWWW 
	e renomeie-a para algo que não seja previsível!';
$_lang['FRIENDLY_URLS'] = 'ULs de fácil utilização';
$_lang['SEF_INFO'] = 'Se for definido para Sim (recomendamos) que mude o nome do ficheiro htaccess.txt para .htaccess';
$_lang['STATISTICS_INFO'] = 'Permitir a recolha de estatísticas de tráfego do site?';
$_lang['GZIP_COMPRESSION'] = 'GZip compressão';
$_lang['GZIP_COMPRESSION_DESC'] = 'Elxis irá comprimir o documento com GZIP antes de enviá-lo para o navegador e, assim, poupa-lhe 70% a 80% da largura da banda.';
$_lang['DEFAULT_ROUTE'] = 'Rota de padrão';
$_lang['DEFAULT_ROUTE_INFO'] = 'Um URL formatado do ELxis que será usado como o frontpage do site';
$_lang['META_DATA'] = 'Dados Meta';
$_lang['META_DATA_INFO'] = 'Uma pequena descrição do web site';
$_lang['KEYWORDS'] = 'Palavras chave';
$_lang['KEYWORDS_INFO'] = 'Algumas palavras chave separadas por vírgulas';
$_lang['STYLE_LAYOUT'] = 'Estilo e layout';
$_lang['SITE_TEMPLATE'] = 'Template do site';
$_lang['ADMIN_TEMPLATE'] = 'Template da administração';
$_lang['ICONS_PACK'] = 'Pacote de ícons';
$_lang['LOCALE'] = 'Localidade';
$_lang['TIMEZONE'] = 'Fuso horário';
$_lang['MULTILINGUISM'] = 'Multilinguismo';
$_lang['MULTILINGUISM_INFO'] = 'Permite-lhe introduzir elementos de texto em mais do que um idioma (traduções). 
	Não o ative se não o utilizar porque isso vai deixar o site mais lento sem necessidade. A interface do ELxis  
	continuará a ser multi-lingual mesmo se esta opção tiver definida como Não.';
$_lang['CHANGE_LANG'] = 'Alterar língua';
$_lang['LANG_CHANGE_WARN'] = 'Se você alterar o idioma predefinido poderá haver inconsistências 
	entre os índices do idioma e as traduções que estão na tabela das traduções.';
$_lang['CACHE'] = 'Cache';
$_lang['CACHE_INFO'] = 'Elxis pode salvar o código HTML gerado por elementos individuais dentro da cache para uma recuperação posterior. 
	Isto é uma configuração geral, você deve também ativar a cache dos elementos (eg. modules) se deseja armazena-la em cache.';
$_lang['APC_INFO'] = 'A Cache Alternativa do PHP (APC) é uma cache com código operacional para PHP. Qual deve ser suportada pelo seu servidor web. 
	Não é recomendado para ambientes alojamento partilhado. Elxis vai usá-lo em páginas especiais para melhorar o desempenho do site.';
$_lang['APC_ID_INFO'] = 'No caso de houver mais do que um site hospedado no mesmo servidor, deve identificá-lo, fornecendo  
	um número inteiro que seja únicamente para este site.';
$_lang['USERS_AND_REGISTRATION'] = 'Utilizadores e Registros';
$_lang['PRIVACY_PROTECTION'] = 'Proteção de privacidade';
$_lang['PASSWORD_NOT_SHOWN'] = 'A senha atual não é exibida por razões de segurança. 
	Preencha este campo apenas se você deseja alterar a senha atual.';
$_lang['DB_TYPE'] = 'Tipo de base dados';
$_lang['ALERT_CON_LOST'] = 'Caso alterar a ligação do atual banco de dados perder-se-á o mesmo!';
$_lang['HOST'] = 'Alojamento';
$_lang['PORT'] = 'Porta';
$_lang['PERSISTENT_CON'] = 'Ligação persistente';
$_lang['DB_NAME'] = 'DB Nome';
$_lang['TABLES_PREFIX'] = 'Prefixo das tabelas';
$_lang['DSN_INFO'] = 'Um nome de dados de origem que pode ser usado de imediato para a conecção ao banco de dados.';
$_lang['SCHEME'] = 'Esquema';
$_lang['SCHEME_INFO'] = 'O caminho absoluto para um ficheiro de banco de dados, no caso de você usar uma base de dados SQLite.';
$_lang['SEND_METHOD'] = 'Método de envio';
$_lang['SMTP_OPTIONS'] = 'Opções SMTP';
$_lang['AUTH_REQ'] = 'É necessária a autenticação';
$_lang['SECURE_CON'] = 'Ligação segura';
$_lang['SENDER_NAME'] = 'Nome do remetente';
$_lang['SENDER_EMAIL'] = 'Email do remetente';
$_lang['RCPT_NAME'] = 'Nome do destinatário';
$_lang['RCPT_EMAIL'] = 'Email do destinatário';
$_lang['TECHNICAL_MANAGER'] = 'Gestor técnico';
$_lang['TECHNICAL_MANAGER_INFO'] = 'O gestor técnico recebe os erros e as alertas de segurança relacionados.';
$_lang['USE_FTP'] = 'Use o FTP';
$_lang['PATH'] = 'Caminho';
$_lang['FTP_PATH_INFO'] = 'O caminho que corresponde ao ficheiro de raiz do FTP até à pasta de instalação (example: /public_html).';
$_lang['SESSION'] = 'Sessão';
$_lang['HANDLER'] = 'Manipulador';
$_lang['HANDLER_INFO'] = 'Elxis pode guardar sessões em formato de ficheiros para dentro do repositório ou dentro da base de dados. 
	Você também pode escolher Nenhum para deixar o PHP guardar as sessões dentro da localização predefinida do servidor.';
$_lang['FILES'] = 'Ficheiros';
$_lang['LIFETIME'] = 'Vitalício';
$_lang['SESS_LIFETIME_INFO'] = 'O tempo até que a sessão expira quando você está inativo.';
$_lang['CACHE_TIME_INFO'] = 'Após este tempo os itens armazenados em cache serão recuperados.';
$_lang['MINUTES'] = 'minutos';
$_lang['HOURS'] = 'horas';
$_lang['MATCH_IP'] = 'Combinar IP';
$_lang['MATCH_BROWSER'] = 'Combinar o browser';
$_lang['MATCH_REFERER'] = 'Combinar referenciador HTTP';
$_lang['MATCH_SESS_INFO'] = 'Permite uma sessão avançada da rotina de validação.';
$_lang['ENCRYPTION'] = 'Encriptação';
$_lang['ENCRYPT_SESS_INFO'] = 'Encriptar dados de sessões?';
$_lang['ERRORS'] = 'Erros';
$_lang['WARNINGS'] = 'Avisos';
$_lang['NOTICES'] = 'Notificações';
$_lang['NOTICE'] = 'Notificar';
$_lang['REPORT'] = 'Reportar';
$_lang['REPORT_INFO'] = 'Nível de erros denunciados. Nos sites em produção nós recomendamos que configure-o para desligado.';
$_lang['LOG'] = 'Entrar';
$_lang['LOG_INFO'] = 'Nível do registo de erros. Selecione quais os erros que você deseja que a Elxis escreva no sistema
	log (repository/logs/).';
$_lang['ALERT'] = 'Alerta';
$_lang['ALERT_INFO'] = 'Envie um email dos erros fatais para o gestor técnico do site.';
$_lang['ROTATE'] = 'Rodar';
$_lang['ROTATE_INFO'] = 'Recomendamos, de rodar o registo de erro no final de cada mês.';
$_lang['DEBUG'] = 'Depurar';
$_lang['MODULE_POS'] = 'Posições dos módulos';
$_lang['MINIMAL'] = 'Mínimo';
$_lang['FULL'] = 'Completo';
$_lang['DISPUSERS_AS'] = 'Mostrar utilizadores como';
$_lang['USERS_REGISTRATION'] = 'Registro de utilizadores';
$_lang['ALLOWED_DOMAIN'] = 'Domínio permitido';
$_lang['ALLOWED_DOMAIN_INFO'] = 'Escreva o nome de um domínio (i.e. elxis.org) sómente para que o sistema 
	possa aceitar o registo de endereços de email.';
$_lang['EXCLUDED_DOMAINS'] = 'Domínios excluidos';
$_lang['EXCLUDED_DOMAINS_INFO'] = 'Lista de nomes dos domínios separados por vírgulas (i.e. badsite.com,hacksite.com) 
	de quais os endereços de email não são aceites durante o registo.';
$_lang['ACCOUNT_ACTIVATION'] = 'Ativação da conta';
$_lang['DIRECT'] = 'Direto';
$_lang['MANUAL_BY_ADMIN'] = 'Manual pelo administrador';
$_lang['PASS_RECOVERY'] = 'Recuperação da palavra passe';
$_lang['SECURITY'] = 'Segurança';
$_lang['SECURITY_LEVEL'] = 'Nível de segurança';
$_lang['SECURITY_LEVEL_INFO'] = 'Ao aumentar o nível de segurança algumas das opções serão ativadas automáticamente 
    enquanto algumas funções podem ser desativadas. Consulte a documentação da Elxis para mais.';
$_lang['NORMAL'] = 'Normal';
$_lang['HIGH'] = 'Elevado';
$_lang['INSANE'] = 'Insane';
$_lang['ENCRYPT_METHOD'] = 'Método de encriptação';
$_lang['AUTOMATIC'] = 'Automático';
$_lang['ENCRYPTION_KEY'] = 'Chave de encriptação';
$_lang['ELXIS_DEFENDER'] = 'Guardião da Elxis';
$_lang['ELXIS_DEFENDER_INFO'] = 'O guarda da Elxis protege o seu web site de ataques injeção de XSS e SQL. 
	Esta poderos ferramente filtra as solicitações do usuário e bloqueia os ataques ao seu site. Ele Também irá notificá-lo de um ataque e registrá-lo
	um ataque e registrá-lo. Você pode selecionar qual o tipo de filtros a serem aplicados ou até mesmo bloquear o seu sistema  
	para proteger os arquivos importantes altereraçãoes não autorizadas. Quanto mais filtros você ativar mais lento correrá o seu site. 
	Nós recomendamos a ativar as opções G, C e F. Consulte a documentação da Elxis para mais.';
$_lang['SSL_SWITCH'] = 'SSL interruptor';
$_lang['SSL_SWITCH_INFO'] = 'Elxis irá automaticamente mudar de HTTP para HTTPS em páginas em que a privacidade é importante. 
	Na área de administração o sistema de HTTPS será permanente. O que requer um certificado SSL!';
$_lang['PUBLIC_AREA'] = 'Área pública';
$_lang['GENERAL_FILTERS'] = 'Regras gerais';
$_lang['CUSTOM_FILTERS'] = 'Regras personalizadas';
$_lang['FSYS_PROTECTION'] = 'Proteção do sistema de arquivos';
$_lang['CHECK_FTP_SETS'] = 'Verificar configurações FTP';
$_lang['FTP_CON_SUCCESS'] = 'A ligação para o servidor FTP foi bem sucedido.';
$_lang['ELXIS_FOUND_FTP'] = 'A instalação da Elxis foi encontrada no FTP.';
$_lang['ELXIS_NOT_FOUND_FTP'] = 'A instalação da Elxis não foi encontrada no FTP! Verifique o valor da opção do caminho do FTP.';
$_lang['CAN_NOT_CHANGE'] = 'Você não pode alterar isto.';
$_lang['SETS_SAVED_SUCC'] = 'Configurações guardados com sucesso';
$_lang['ACTIONS'] = 'Ações';
$_lang['BAN_IP_REQ_DEF'] = 'Para banir un endereço de IP é necessário permitir ao guardião da ELxis uma opção!';
$_lang['BAN_YOURSELF'] = 'Está a tentar a excluir-se a si mesmo?';
$_lang['IP_AL_BANNED'] = 'Este IP já foi banido!';
$_lang['IP_BANNED'] = 'Endereço de IP %s banido!';
$_lang['BAN_FAILED_NOWRITE'] = 'Exclusão falhou! O ficheiro repository/logs/defender_ban.php não pode ser substituído.';
$_lang['ONLY_ADMINS_ACTION'] = 'Apenas os administradores é que podem realizar esta ação!';
$_lang['CNOT_LOGOUT_ADMIN'] = 'Você não pode terminar a sessão de um administrador!';
$_lang['USER_LOGGED_OUT'] = 'O usuário foi desconectado!';
$_lang['SITE_STATISTICS'] = 'Estatísticas do site';
$_lang['SITE_STATISTICS_INFO'] = 'Veja as estatísticas do tráfego do site';
$_lang['BACKUP'] = 'Backup';
$_lang['BACKUP_INFO'] = 'Faça um backup completamente novo do site e gerencie os existente.';
$_lang['BACKUP_FLIST'] = 'Lista existente dos ficheiros do backup';
$_lang['TYPE'] = 'Tipo';
$_lang['FILENAME'] = 'Nome do ficheiro';
$_lang['SIZE'] = 'Tamanho';
$_lang['NEW_DB_BACKUP'] = 'Novo backup da base de dados';
$_lang['NEW_FS_BACKUP'] = 'Novo backup do sistema de arquivos';
$_lang['FILESYSTEM'] = 'Sistema de arquivos';
$_lang['DOWNLOAD'] = 'Baixar';
$_lang['TAKE_NEW_BACKUP'] = 'Quer fazer um novo backup?\nIsso pode demorar algum tempo,por favor, seja paciente!';
$_lang['FOLDER_NOT_EXIST'] = "Pasta %s não existe!";
$_lang['FOLDER_NOT_WRITE'] = "Pasta %s não é gravável!";
$_lang['BACKUP_SAVED_INTO'] = "Os ficheiros do backup são guardados dentro do %s";
$_lang['CACHE_SAVED_INTO'] = "Os ficheiros da cache são guardados dentro do %s";
$_lang['CACHED_ITEMS'] = 'Itens armazenados em cache';
$_lang['ELXIS_ROUTER'] = 'Elxis router';
$_lang['ROUTING'] = 'Encaminhar';
$_lang['ROUTING_INFO'] = 'Reencaminhar os pedidos do utlizador para o endereço de URL personalizado.';
$_lang['SOURCE'] = 'Origem';
$_lang['ROUTE_TO'] = 'Encaminhar para';
$_lang['REROUTE'] = "Reencaminhar %s";
$_lang['DIRECTORY'] = 'Diretório';
$_lang['SET_FRONT_CONF'] = 'Definir o frontpage do site na configuração da ELxis!';
$_lang['ADD_NEW_ROUTE'] = 'Adicionar novo caminho';
$_lang['OTHER'] = 'Outro';
$_lang['LAST_MODIFIED'] = 'Última modificação';
$_lang['PERIOD'] = 'Período'; //time period
$_lang['ERROR_LOG_DISABLED'] = 'Ocorreu um erro, o registo está desativado!';
$_lang['LOG_ENABLE_ERR'] = 'O registro está ativo apenas para erros fatais.';
$_lang['LOG_ENABLE_ERRWARN'] = 'O registro está ativo para erros e avisos.';
$_lang['LOG_ENABLE_ERRWARNNTC'] = 'O registro está ativo para erros, avisos e notificações.';
$_lang['LOGROT_ENABLED'] = 'A rotação dos registros está ativa.';
$_lang['LOGROT_DISABLED'] = 'A rotação dos registros está desativa!';
$_lang['SYSLOG_FILES'] = 'Sistema de registro de ficheiros';
$_lang['DEFENDER_BANS'] = 'O guardião exclui';
$_lang['LAST_DEFEND_NOTIF'] = 'Última notificação do guardião';
$_lang['LAST_ERROR_NOTIF'] = 'Última notificação de erro';
$_lang['TIMES_BLOCKED'] = 'Hora bloqueada';
$_lang['REFER_CODE'] = 'Código de referência';
$_lang['CLEAR_FILE'] = 'Limpar ficheiro';
$_lang['CLEAR_FILE_WARN'] = 'O conteúdo do ficheiro será removido. Quer continuar?';
$_lang['FILE_NOT_FOUND'] = 'Ficheiro não encontrado!';
$_lang['FILE_CNOT_DELETE'] = 'Este ficheiro não pode se apagado!';
$_lang['ONLY_LOG_DOWNLOAD'] = 'Apenas os ficheiros com a extensão .log é que podem ser descarregados!';
$_lang['SYSTEM'] = 'Sistema';
$_lang['PHP_INFO'] = 'Informação PHP';
$_lang['PHP_VERSION'] = 'Versão do PHP';
$_lang['ELXIS_INFO'] = 'Informação da Exis';
$_lang['VERSION'] = 'Versão';
$_lang['REVISION_NUMBER'] = 'Número de revisão';
$_lang['STATUS'] = 'Estado';
$_lang['CODENAME'] = 'Codename';
$_lang['RELEASE_DATE'] = 'Data de lançamento';
$_lang['COPYRIGHT'] = 'Direitos de autor';
$_lang['POWERED_BY'] = 'Fornecido por';
$_lang['AUTHOR'] = 'Autor';
$_lang['PLATFORM'] = 'Plataforma';
$_lang['HEADQUARTERS'] = 'Sede';
$_lang['ELXIS_ENVIROMENT'] = 'Meio de Ambiente da ELxis';
$_lang['DEFENDER_LOGS'] = 'Registros do guardião';
$_lang['ADMIN_FOLDER'] = 'Pasta da administração';
$_lang['DEF_NAME_RENAME'] = 'Renomeie, o nome padrão!';
$_lang['INSTALL_PATH'] = 'Caminho de instalação';
$_lang['IS_PUBLIC'] = 'É público!';
$_lang['CREDITS'] = 'Créditos';
$_lang['LOCATION'] = 'Localização';
$_lang['CONTRIBUTION'] = 'Contribuição';
$_lang['LICENSE'] = 'Licença';
$_lang['MULTISITES'] = 'Vários sites';
$_lang['MULTISITES_DESC'] = 'Gerenciar multi-sites sob uma instalação do Elxis.';
$_lang['MULTISITES_WARN'] = 'Você pode ter os multi-sites com uma instalação. Trabalhar com multi-sites
	éuma tarefa que exige algum conhecimento do CMS da ELxis. Antes de importar os dados para o multi-site
	certifiques-se da existência da base de dados. Após de criar um novo multi-site deve editar o ficheiro htaccess 
	baseando-se nas instruções fornecidas. Eliminando um multi-site não exclui o banco de dados vinculado. Consulte um técnico experiente, no caso 
	de você precisar de ajuda.';
$_lang['MULTISITES_DISABLED'] = 'Multisites estão desativados!';
$_lang['ENABLE'] = 'Ativar';
$_lang['ACTIVE'] = 'Ativo';
$_lang['URL_ID'] = 'Identificador do URL';
$_lang['MAN_MULTISITES_ONLY'] = "Você pode administrar os multi-sites só a partir do site %s";
$_lang['LOWER_ALPHANUM'] = 'Minúsculas e caracteres alfanúmericos sem espaços';
$_lang['IMPORT_DATA'] = 'Importar dados';
$_lang['CNOT_CREATE_CFG_NEW'] = "Não foi possível criar o ficheiro de configuração %s para o novo site!";
$_lang['DATA_IMPORT_FAILED'] = 'Importação dos dados falhou!';
$_lang['DATA_IMPORT_SUC'] = 'Dado importados com sucesso!';
$_lang['ADD_RULES_HTACCESS'] = 'Adicione as seguintes regras no ficheiro de htaccess';
$_lang['CREATE_REPOSITORY_NOTE'] = 'É altamente recomendável criar um repositório separado para cada sub-site!';
$_lang['NOT_SUP_DBTYPE'] = 'Tipo de banco de dados não suportado!';
$_lang['DBTYPES_MUST_SAME'] = 'Tipos de de banco de dados do site e do novo site deve ser o mesmo!';
$_lang['DISABLE_MULTISITES'] = 'Desativar os multi-sites';
$_lang['DISABLE_MULTISITES_WARN'] = 'Todos os sites excepto o com id irão ser removidos!';
$_lang['VISITS_PER_DAY'] = "Visitas por dia de %s"; //translators help: ... for {MONTH YEAR}
$_lang['CLICKS_PER_DAY'] = "Cliques por dia de %s"; //translators help: ... for {MONTH YEAR}
$_lang['VISITS_PER_MONTH'] = "Visitas por mês de %s"; //translators help: ... for {YEAR}
$_lang['CLICKS_PER_MONTH'] = "Cliques por mês de %s"; //translators help: ... for {YEAR}
$_lang['LANGS_USAGE_FOR'] = "Uso de idioma percentual para %s"; //translators help: ... for {MONTH YEAR}
$_lang['UNIQUE_VISITS'] = 'Visitas únicas';
$_lang['PAGE_VIEWS'] = 'Visualizações de página';
$_lang['TOTAL_VISITS'] = 'Total de visitas';
$_lang['TOTAL_PAGE_VIEWS'] = 'Visualizações de página';
$_lang['LANGS_USAGE'] = 'Utilização das línguas';
$_lang['LEGEND'] = 'Legenda';
$_lang['USAGE'] = 'Uso';
$_lang['VIEWS'] = 'Visualizações';
$_lang['OTHER'] = 'Outro';
$_lang['NO_DATA_AVAIL'] = 'Nenhuns dados disponíveis';
$_lang['PERIOD'] = 'Tempo';
$_lang['YEAR_STATS'] = 'Estatísticas anuais';
$_lang['MONTH_STATS'] = 'Estatísticas mensais';
$_lang['PREVIOUS_YEAR'] = 'Ano anterior';
$_lang['NEXT_YEAR'] = 'Ano seguinte';
$_lang['STATS_COL_DISABLED'] = 'A recolha de dados de estatísticas está desativado! Permitir estatísticas na configuração da Elxis.';
$_lang['DOCTYPE'] = 'Tipo de documento';
$_lang['DOCTYPE_INFO'] = 'A opção recomendada é o HTML5. Elxis irá gerar uma saída XHTML mesmo que você defina DOCTYPE para HTML5. 
	No tipo de documento de XHTML a Elxis apresenta os documentos com application/xhtml+xml de tipo mime a modernos browsers e os de text/html aos mais velhos.';
$_lang['ABR_SECONDS'] = 'segundo';
$_lang['ABR_MINUTES'] = 'minuto';
$_lang['HOUR'] = 'hora';
$_lang['HOURS'] = 'horas';
$_lang['DAY'] = 'dia';
$_lang['DAYS'] = 'dias';
$_lang['UPDATED_BEFORE'] = 'Atualizado antes';
$_lang['CACHE_INFO'] = 'Exibir e eliminar os itens guardados no cache.';
$_lang['ELXISDC'] = 'Centro de download da Elxis';
$_lang['ELXISDC_INFO'] = 'Navegue ao vivo na EDC e veja as extensões disponíveis';
$_lang['SITE_LANGS'] = 'Línguas do site';
$_lang['SITE_LANGS_DESC'] = 'Por norma, todos os idiomas instalados estão disponíveis na área do frontend do site. Você pode alterar essa definição
	sselccionando abaixo os idiomas que deseja que apareçam disponíveis no frontend.';
//Elxis 4.1
$_lang['PERFORMANCE'] = 'Execução';
$_lang['MINIFIER_CSSJS'] = 'CSS/Javascript minimizador';
$_lang['MINIFIER_INFO'] = 'Elxis pode unir os ficheiros individuais de CSS e JS e opcionalmente, comprimi-los. O ficheiro unificado será guardado na cache. 
	Assim em vez de ter vários ficheiros CSS/JS no cabeçalho da sua página você terá apenas um minimizado.';
$_lang['MOBILE_VERSION'] = 'Versão móvel';
$_lang['MOBILE_VERSION_DESC'] = 'Quer ativar a versão mobile-friendly para dispositivos portáteis?';
//Elxis 4.2
$_lang['SEND_TEST_EMAIL'] = 'Enviar email de teste';
$_lang['ONLINE_USERS'] = 'On-line para os usuários';
$_lang['CRONJOBS'] = 'Cron jobs';
$_lang['CRONJOBS_INFO'] = 'Enable cron jobs if you want to run automated tasks like scheduled articles publishing.';
$_lang['LANG_DETECTION'] = 'Detecção de idioma';
$_lang['LANG_DETECTION_INFO'] = 'Νative language detection and redirection to the proper language version of the site on first visit to frontpage.';
//Elxis 4.4
$_lang['DEFENDER_NOTIFS'] = 'Notificações defensor';
$_lang['XFRAMEOPT_HELP'] = 'HTTP header that controls if the browser will accept or refuse displaying pages from this site inside an frame. Helps avoiding clickjacking attacks.';
$_lang['ACCEPT_XFRAME'] = 'Aceitar X-Frame';
$_lang['DENY'] = 'Negar';
$_lang['SAMEORIGIN'] = 'Mesma origem';
$_lang['ALLOW_FROM'] = 'Permitir que a partir de';
$_lang['ALLOW_FROM_ORIGIN'] = 'Allow from origin';
$_lang['CONTENT_SEC_POLICY'] = 'Content Security Policy';
$_lang['IP_RANGES'] = 'IP Ranges';
$_lang['UPDATED_AUTO'] = 'Atualizados automaticamente';
$_lang['CHECK_IP_MOMENT'] = 'Verifique momento IP';
$_lang['BEFORE_LOAD_ELXIS'] = 'Before loading Elxis core';
$_lang['AFTER_LOAD_ELXIS'] = 'After loading Elxis core';
$_lang['CHECK_IP_MOMENT_HELP'] = 'BEFORE: Defender checks IPs on each click. Bad IPs dont reach Elxis core. 
	AFTER: Defender checks IPs only once per session (performance improvement). Bad IPs reach Elxis core before they get blocked.';
$_lang['SECURITY'] = 'Segurança';
$_lang['EVERYTHING'] = 'Tudo';
$_lang['ONLY_ATTACKS'] = 'Somente ataques';
$_lang['CRONJOBS_PROB'] = 'Cron jobs probabilidade';
$_lang['CRONJOBS_PROB_INFO'] = 'The percentage probability of executing Cron jobs on each user click. Affects only cron jobs executed internally by Elxis. For best performance the more visitors your site has the lower this value should be. The default value is 10%.';
$_lang['EXTERNAL'] = 'Externo';
$_lang['SEO_TITLES_MATCH'] = 'SEO Títulos combinar';
$_lang['SEO_TITLES_MATCH_HELP'] = 'Controls the generation of SEO Titles from normal titles. Exact creates SEO Titles that match exactly the original titles ones transliterated.';
$_lang['EXACT'] = 'Exato';
//Elxis 4.6
$_lang['CONFIG_FOR_GMAIL'] = 'Configuração para o Gmail';
$_lang['AUTH_METHOD'] = 'Método de autenticação';
$_lang['DEFAULT'] = 'Padrão';
$_lang['BACKUP_EXCLUDE_PATHS_HELP'] = 'You can exclude some folders from the filesystem backup procedure. This is extremely 
	usefull if you have a large filesystem and backup fails to complete due to memory issues. Provide below the folders you want 
	to exclude by giving their relative paths. Example: media/videos/';
$_lang['PATHS_EXCLUDED_FSBK'] = 'Caminhos excluídos do backup do sistema de arquivos';
$_lang['EXCLUSIONS'] = 'Exclusões';
//Elxis 5.0
$_lang['BACKUP_FOLDER_TABLE_TIP'] = 'For file system backup you can select to backup the whole Elxis installation or a specific folder. 
	For database you can backup the whole database or a specific table. In case you get timeout or memory errors during backup (especially 
	on large sites) select to backup individual folders or tables instead.';
$_lang['FOLDER'] = 'Pasta';
$_lang['TABLE'] = 'Tabela';
$_lang['INACTIVE'] = 'Inativo';
$_lang['DEPRECATED'] = 'Deprecado';
$_lang['ALL_AVAILABLE'] = 'Todos disponíveis';//translators help: "All available languages"
$_lang['NO_PROTECTION'] = 'No protection';//translators help: No Elxis Defender filters enabled
$_lang['NEWER_VERSION_FOR'] = 'Existe uma versão mais recente (%s) para o %s';
$_lang['NEWER_VERSIONS_FOR'] = 'Existem versões mais recentes para o %s';
$_lang['NEWER_VERSIONS_FOR_EXTS'] = 'Existem versões mais recentes para %s extensões';
$_lang['OUTDATED_ELXIS_UPDATE_TO'] = 'Você usa uma versão desatualizada do Elxis! Atualize o mais rápido possível para a %s';
$_lang['NO_BACKUPS'] = 'Você não tem backups!';
$_lang['LONGTIME_TAKE_BACKUP'] = 'Você tem muito tempo para fazer um backup do site';
$_lang['DELETE_OLD_LOGS'] = 'Exclua arquivos de log antigos';
$_lang['DEFENDER_IS_DISABLED'] = 'O Elxis Defender está desativado';
$_lang['REPO_DEF_PATH'] = 'Repositório está no caminho padrão';
$_lang['CHANGE_MAIL_TO_SMTP'] = 'Altere o PHP Mail para SMTP ou outro';
$_lang['DISABLE_MULTILINGUISM'] = 'Desabilite o multilangualismo';
$_lang['ENABLE_MULTILINGUISM'] = 'Ativar multilangualismo';
//Elxis 5.2
$_lang['NOTFOUND'] = 'Não encontrado';
$_lang['EXTENSION'] = 'Extensão';
$_lang['CODE_EDITOR_WARN'] = 'We strongly recommend not to modify extensions\'s files because you will lose your changes after an update. 
	Add your custom or overwrite CSS rules on <strong>user.config</strong> files instead.';
$_lang['EDIT_CODE'] = 'Editar código';
$_lang['EXCLUDED_IPS'] = 'IPs excluídos';

?>