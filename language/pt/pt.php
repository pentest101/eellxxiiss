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


$locale = array('pt_PT.utf8', 'pt_PT.UTF-8', 'pt_PT', 'pt', 'portugues', 'Portugal'); //utf-8 locales array

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
$_lang['THOUSANDS_SEP'] = ',';
$_lang['DECIMALS_SEP'] = '.';
//month names
$_lang['JANUARY'] = 'Janeiro';
$_lang['FEBRUARY'] = 'Fevereiro';
$_lang['MARCH'] = 'Mar??o';
$_lang['APRIL'] = 'Abril';
$_lang['MAY'] = 'Maio';
$_lang['JUNE'] = 'Junho';
$_lang['JULY'] = 'Julho';
$_lang['AUGUST'] = 'Agosto';
$_lang['SEPTEMBER'] = 'Setembro';
$_lang['OCTOBER'] = 'Outubro';
$_lang['NOVEMBER'] = 'Novembro';
$_lang['DECEMBER'] = 'Dezembro';
$_lang['JANUARY_SHORT'] = 'Jan';
$_lang['FEBRUARY_SHORT'] = 'Fev';
$_lang['MARCH_SHORT'] = 'Mar';
$_lang['APRIL_SHORT'] = 'Abr';
$_lang['MAY_SHORT'] = 'Mai';
$_lang['JUNE_SHORT'] = 'Jun';
$_lang['JULY_SHORT'] = 'Jul';
$_lang['AUGUST_SHORT'] = 'Ago';
$_lang['SEPTEMBER_SHORT'] = 'Set';
$_lang['OCTOBER_SHORT'] = 'Out';
$_lang['NOVEMBER_SHORT'] = 'Nov';
$_lang['DECEMBER_SHORT'] = 'Dez';
//day names
$_lang['MONDAY'] = 'Segunda-feira';
$_lang['THUESDAY'] = 'Ter??a-feira';
$_lang['WEDNESDAY'] = 'Quarta-feira';
$_lang['THURSDAY'] = 'Quinta-feira';
$_lang['FRIDAY'] = 'Sexta-feira';
$_lang['SATURDAY'] = 'S??bado';
$_lang['SUNDAY'] = 'Domingo';
$_lang['MONDAY_SHORT'] = 'Seg';
$_lang['THUESDAY_SHORT'] = 'Ter';
$_lang['WEDNESDAY_SHORT'] = 'Qua';
$_lang['THURSDAY_SHORT'] = 'Qui';
$_lang['FRIDAY_SHORT'] = 'Sex';
$_lang['SATURDAY_SHORT'] = 'S??b';
$_lang['SUNDAY_SHORT'] = 'Dom';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Monitor de desempenho do Elxis';
$_lang['ITEM'] = 'Item';
$_lang['INIT_FILE'] = 'Ficheiro de inicializa????o';
$_lang['EXEC_TIME'] = 'O tempo de execu????o';
$_lang['DB_QUERIES'] = 'Consultas DB ';
$_lang['ERRORS'] = 'Erros';
$_lang['SIZE'] = 'Tamanho';
$_lang['ENTRIES'] = 'Entradas';

/* general */
$_lang['HOME'] = 'Home';
$_lang['YOU_ARE_HERE'] = 'Voc?? est?? aqui';
$_lang['CATEGORY'] = 'Categoria';
$_lang['DESCRIPTION'] = 'Descri????o';
$_lang['FILE'] = 'Ficheiro';
$_lang['IMAGE'] = 'Imagem';
$_lang['IMAGES'] = 'Imagens';
$_lang['CONTENT'] = 'Conte??do';
$_lang['DATE'] = 'Data';
$_lang['YES'] = 'Sim';
$_lang['NO'] = 'N??o';
$_lang['NONE'] = 'Nenhum';
$_lang['SELECT'] = 'Selecione';
$_lang['LOGIN'] = 'Inicar sess??o';
$_lang['LOGOUT'] = 'Terminar saess??o';
$_lang['WEBSITE'] = 'P??gina web';
$_lang['SECURITY_CODE'] = 'C??digo de seguran??a';
$_lang['RESET'] = 'Restabelecer';
$_lang['SUBMIT'] = 'Submeter';
$_lang['REQFIELDEMPTY'] = 'Um ou mais campos obrigat??rios est??o vazios!';
$_lang['FIELDNOEMPTY'] = "%s n??o pode estar vazio!";
$_lang['FIELDNOACCCHAR'] = "%s cont??m caracteres n??o suportados!";
$_lang['INVALID_DATE'] = 'Data inv??lida!';
$_lang['INVALID_NUMBER'] = 'N??mero inv??lido!';
$_lang['INVALID_URL'] = 'Endere??o de URL inv??lido!';
$_lang['FIELDSASTERREQ'] = 'Campos com asterisco * s??o obrigat??rios.';
$_lang['ERROR'] = 'Erro';
$_lang['REGARDS'] = 'Cumprimentos';
$_lang['NOREPLYMSGINFO'] = 'Por favor, n??o responda a esta mensagem que lhe foi enviada, a mesma ?? apenas para fins informativos.';
$_lang['LANGUAGE'] = 'L??ngua';
$_lang['PAGE'] = 'P??gina';
$_lang['PAGEOF'] = "P??gina %s de %s";
$_lang['OF'] = 'de';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Mostre %s at?? %s de %s itens";
$_lang['HITS'] = 'Cliques';
$_lang['PRINT'] = 'Imprimir';
$_lang['BACK'] = 'Atr??s';
$_lang['PREVIOUS'] = 'Anterior';
$_lang['NEXT'] = 'Pr??ximo';
$_lang['CLOSE'] = 'Fechar';
$_lang['CLOSE_WINDOW'] = 'Fechar janela';
$_lang['COMMENTS'] = 'Coment??rios';
$_lang['COMMENT'] = 'Coment??rio';
$_lang['PUBLISH'] = 'Publicar';
$_lang['DELETE'] = 'Apagar';
$_lang['EDIT'] = 'Editar';
$_lang['COPY'] = 'Copiar';
$_lang['SEARCH'] = 'Pesquisar';
$_lang['PLEASE_WAIT'] = 'Por favor, espere ...';
$_lang['ANY'] = 'Qualquer';
$_lang['NEW'] = 'Novo';
$_lang['ADD'] = 'Adicione';
$_lang['VIEW'] = 'Ver';
$_lang['MENU'] = 'Menu';
$_lang['HELP'] = 'Ajuda';
$_lang['TOP'] = 'Parte superior';
$_lang['BOTTOM'] = 'Parte inferior';
$_lang['LEFT'] = 'Esquerda';
$_lang['RIGHT'] = 'Direita';
$_lang['CENTER'] = 'Centro';

/* xml */
$_lang['CACHE'] = 'Cache';
$_lang['ENABLE_CACHE_D'] = 'Ativar a cache para este item?';
$_lang['YES_FOR_VISITORS'] = 'Sim, para visitantes';
$_lang['YES_FOR_ALL'] = 'Sim, para todos';
$_lang['CACHE_LIFETIME'] = 'Tempo de cache';
$_lang['CACHE_LIFETIME_D'] = 'Tempo, em minutos, at?? que a cache ?? atualizada para este item.';
$_lang['NO_PARAMS'] = 'N??o existem par??metros!';
$_lang['STYLE'] = 'Estilo';
$_lang['ADVANCED_SETTINGS'] = 'Configura????es avan??adas';
$_lang['CSS_SUFFIX'] = 'CSS sufixo';
$_lang['CSS_SUFFIX_D'] = 'Um sufixo que ser?? adicionado ao m??dulo da categoria do CSS.';
$_lang['MENU_TYPE'] = 'Tipo de menu';
$_lang['ORIENTATION'] = 'Orienta????o';
$_lang['SHOW'] = 'Mostrar';
$_lang['HIDE'] = 'Esconder';
$_lang['GLOBAL_SETTING'] = 'Configura????o global';

/* users & authentication */
$_lang['USERNAME'] = 'Nome de utilizador';
$_lang['PASSWORD'] = 'Palavra passe';
$_lang['NOAUTHMETHODS'] = 'N??o foram definidos nenhuns m??todos de autentica????o';
$_lang['AUTHMETHNOTEN'] = 'M??todo de autentica????o %s n??o foi ativado';
$_lang['PASSTOOSHORT'] = 'A sua palavra passe ?? muito curta para ser aceite';
$_lang['USERNOTFOUND'] = 'Utilizador n??o encontrado';
$_lang['INVALIDUNAME'] = 'Nome de utilizador inv??lido';
$_lang['INVALIDPASS'] = 'Palavra passe inv??lidaInvalid password';
$_lang['AUTHFAILED'] = 'Autentica????o falhou';
$_lang['YACCBLOCKED'] = 'Sua conta est?? bloqueada';
$_lang['YACCEXPIRED'] = 'Sua conta expirou';
$_lang['INVUSERGROUP'] = 'Grupo de utilizadores inv??lido';
$_lang['NAME'] = 'Nome';
$_lang['FIRSTNAME'] = 'Nome pr??prio';
$_lang['LASTNAME'] = '??ltimo nome';
$_lang['EMAIL'] = 'Email';
$_lang['INVALIDEMAIL'] = 'Endere??o de email inv??lido';
$_lang['ADMINISTRATOR'] = 'Administrador';
$_lang['GUEST'] = 'Convidado';
$_lang['EXTERNALUSER'] = 'Utilizador externo';
$_lang['USER'] = 'Utilizador';
$_lang['GROUP'] = 'Grupo';
$_lang['NOTALLOWACCPAGE'] = 'Voc?? n??o tem permiss??o para aceder a esta p??gina!';
$_lang['NOTALLOWACCITEM'] = 'Voc?? n??o tem permiss??o para aceder este item!';
$_lang['NOTALLOWMANITEM'] = 'Voc?? n??o est?? autorizado para gerir este item!';
$_lang['NOTALLOWACTION'] = 'Voc?? n??o est?? autorizado para executar esta a????o!';
$_lang['NEED_HIGHER_ACCESS'] = 'Voc?? precisa de um n??vel de acesso superior para esta a????o!';
$_lang['AREYOUSURE'] = 'Voc?? tem a certeza?';

/* highslide */
$_lang['LOADING'] = 'Carregando...';
$_lang['CLICK_CANCEL'] = 'Clique para cancelar';
$_lang['MOVE'] = 'Mover';
$_lang['PLAY'] = 'Reproduzir';
$_lang['PAUSE'] = 'Parar';
$_lang['RESIZE'] = 'Redimensionar';

/* admin */
$_lang['ADMINISTRATION'] = 'Administra????o';
$_lang['SETTINGS'] = 'Configura????es';
$_lang['DATABASE'] = 'Base de dados';
$_lang['ON'] = 'Online';
$_lang['OFF'] = 'Offline';
$_lang['WARNING'] = 'Aviso';
$_lang['SAVE'] = 'Guardar';
$_lang['APPLY'] = 'Aplicar';
$_lang['CANCEL'] = 'Cancelar';
$_lang['LIMIT'] = 'Limitar';
$_lang['ORDERING'] = 'Ordenar';
$_lang['NO_RESULTS'] = 'Nenhuns resultados encontrados!';
$_lang['CONNECT_ERROR'] = 'Erro de liga????o';
$_lang['DELETE_SEL_ITEMS'] = 'Apagar itens selecionados?';
$_lang['TOGGLE_SELECTED'] = 'Modo alternar selecionado';
$_lang['NO_ITEMS_SELECTED'] = 'Nenhuns itens selecionados!';
$_lang['ID'] = 'Identifica????o';
$_lang['ACTION_FAILED'] = 'A????o falhou!';
$_lang['ACTION_SUCCESS'] = 'A????o conclu??da com sucesso!';
$_lang['NO_IMAGE_UPLOADED'] = 'Nenhuma imagem carregada';
$_lang['NO_FILE_UPLOADED'] = 'Nenhum ficheiro carregado';
$_lang['MODULES'] = 'M??dulos';
$_lang['COMPONENTS'] = 'Componentes';
$_lang['TEMPLATES'] = 'Templates';
$_lang['SEARCH_ENGINES'] = 'Motores de busca';
$_lang['AUTH_METHODS'] = 'M??todos de autentica????o';
$_lang['CONTENT_PLUGINS'] = 'Plugins de conte??do';
$_lang['PLUGINS'] = 'Plugins';
$_lang['PUBLISHED'] = 'Publicado';
$_lang['ACCESS'] = 'Aceder';
$_lang['ACCESS_LEVEL'] = 'N??vel de acesso';
$_lang['TITLE'] = 'T??tulo';
$_lang['MOVE_UP'] = 'Mover para cima';
$_lang['MOVE_DOWN'] = 'Mover para baixo';
$_lang['WIDTH'] = 'Largura';
$_lang['HEIGHT'] = 'Altura';
$_lang['ITEM_SAVED'] = 'Item guardado';
$_lang['FIRST'] = 'Primeiro';
$_lang['LAST'] = '??ltimo';
$_lang['SUGGESTED'] = 'Sugerido';
$_lang['VALIDATE'] = 'Validar';
$_lang['NEVER'] = 'Nunca';
$_lang['ALL'] = 'Todos';
$_lang['ALL_GROUPS_LEVEL'] = "Todos os grupos do n??vel %s";
$_lang['REQDROPPEDSEC'] = 'A sua solicita????o caiu por raz??es de seguran??a. Por favor, tente novamente.';
$_lang['PROVIDE_TRANS'] = 'Por favor, forne??a uma tradu????o!';
$_lang['AUTO_TRANS'] = 'Tradu????o autom??tica';
$_lang['STATISTICS'] = 'Estat??sticas';
$_lang['UPLOAD'] = 'Carregar';
$_lang['MORE'] = 'Mais';
//Elxis 4.2
$_lang['TRANSLATIONS'] = 'Tradu????es';
$_lang['CHECK_UPDATES'] = 'Verificar atualiza????es';
$_lang['TODAY'] = 'Hoje';
$_lang['YESTERDAY'] = 'Ontem';
//Elxis 4.3
$_lang['PUBLISH_ON'] = 'Publicar em';
$_lang['UNPUBLISHED'] = 'In??dito';
$_lang['UNPUBLISH_ON'] = 'Despublicar em';
$_lang['SCHEDULED_CRON_DIS'] = 'There are %s scheduled items but Cron Jobs are disabled!';
$_lang['CRON_DISABLED'] = 'Cron Jobs are disabled!';
$_lang['ARCHIVE'] = 'Arquivo';
$_lang['RUN_NOW'] = '??xecutar agora';
$_lang['LAST_RUN'] = '??ltima execu????o';
$_lang['SEC_AGO'] = '%s seg atr??s';
$_lang['MIN_SEC_AGO'] = '%s min antes e %s seg atr??s';
$_lang['HOUR_MIN_AGO'] = '1 hora antes e %s min atr??s';
$_lang['HOURS_MIN_AGO'] = '%s horas antes e %s min atr??s';
$_lang['CLICK_TOGGLE_STATUS'] = 'Clique aqui para alternar status';
//Elxis 4.5
$_lang['IAMNOTA_ROBOT'] = 'Eu n??o sou um rob??';
$_lang['VERIFY_NOROBOT'] = 'Por favor, confirme que voc?? n??o ?? um rob??!';
$_lang['CHECK_FS'] = 'Arquivos de verificar';
//Elxis 5.0
$_lang['TOTAL_ITEMS'] = '%s itens';
$_lang['SEARCH_OPTIONS'] = 'Op????es de busca';
$_lang['FILTERS_HAVE_APPLIED'] = 'Filtros foram aplicados';
$_lang['FILTER_BY_ITEM'] = 'Filtrar por este item';
$_lang['REMOVE_FILTER'] = 'Remova o filtro';
$_lang['TOTAL'] = 'Total';
$_lang['OPTIONS'] = 'Op????es';
$_lang['DISABLE'] = 'Desabilitar';
$_lang['REMOVE'] = 'Remover';
$_lang['ADD_ALL'] = 'Adicione tudo';
$_lang['TOMORROW'] = 'Amanh??';
$_lang['NOW'] = 'Agora';
$_lang['MIN_AGO'] = '1 minuto atr??s';
$_lang['MINS_AGO'] = '%s minutos atr??s';
$_lang['HOUR_AGO'] = '1 hora atr??s';
$_lang['HOURS_AGO'] = '%s horas atr??s';
$_lang['IN_SEC'] = 'Em %s seg';
$_lang['IN_MINUTE'] = 'Em 1 minuto';
$_lang['IN_MINUTES'] = 'Em %s minutos';
$_lang['IN_HOUR'] = 'Em 1 hora';
$_lang['IN_HOURS'] = 'Em %s horas';
$_lang['OTHER'] = 'De outros';
$_lang['DELETE_CURRENT_IMAGE'] = 'Excluir imagem atual';
$_lang['NO_IMAGE_FILE'] = 'Nenhum arquivo de imagem!';
$_lang['SELECT_FILE'] = 'Selecione o arquivo';

?>