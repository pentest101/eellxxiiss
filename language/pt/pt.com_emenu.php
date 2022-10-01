<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: pt-PT (Portuguese - Portugal) language for component eMenu
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Luciano Neves ( luckybano@gmail.com )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['MENU'] = 'Menu';
$_lang['MENU_MANAGER'] = 'Gestor do menu';
$_lang['MENU_ITEM_COLLECTIONS'] = 'Coleções dos itens do menu';
$_lang['SN'] = 'S/N'; //serial number
$_lang['MENU_ITEMS'] = 'Itens do menu';
$_lang['COLLECTION'] = 'Coleção';
$_lang['WARN_DELETE_COLLECT'] = 'Isto irá apagar a coleção, e TODOS os seus itens de menu e do módulo associados a ele!';
$_lang['CNOT_DELETE_MAINMENU'] = 'Você não pode excluir a coleção do menu principal!';
$_lang['MODULE_TITLE'] = 'Título do módulo';
$_lang['COLLECT_NAME_INFO'] = 'O nome da coleção deve ser único, e conter caracteres alfanúmericos e latinos sem espaços!';
$_lang['ADD_NEW_COLLECT'] = 'Adicionar nova coleção';
$_lang['EXIST_COLLECT_NAME'] = 'Já existe uma coleção com esse nome!';
$_lang['MANAGE_MENU_ITEMS'] = 'Gerir itens do menu';
$_lang['EXPAND'] = 'Expandir';
$_lang['FULL'] = 'Completo';
$_lang['LIMITED'] = 'Limitado';
$_lang['TYPE'] = 'Tipo';
$_lang['LEVEL'] = 'Nível';
$_lang['MAX_LEVEL'] = 'Nível máximo';
$_lang['LINK'] = 'Link';
$_lang['ELXIS_LINK'] = 'Elxis link';
$_lang['SEPARATOR'] = 'Separador';
$_lang['WRAPPER'] = 'Empacotador';
$_lang['WARN_DELETE_MENUITEM'] = 'Tem certeza de que deseja excluir este item do menu? Os itens das crianças também serão excluídos!';
$_lang['SEL_MENUITEM_TYPE'] = 'Selecione o tipo de item para o menu';
$_lang['LINK_LINK_DESC'] = 'Faça um link para a página da ELxis.';
$_lang['LINK_URL_DESC'] = 'Ligação predefenida para uma página externa.';
$_lang['LINK_SEPARATOR_DESC'] = 'Cadeia de texto sem link.';
$_lang['LINK_WRAPPER_DESC'] = 'Linkar para uma página exterior online.';
$_lang['EXPAND_DESC'] = 'Gerir, se suportado, um submenu. A limitada expansão mostra apenas os primeiros itens de nível enquanto completa toda a árvore.';
$_lang['LINK_TARGET'] = 'Destino do link';
$_lang['SELF_WINDOW'] = 'Própria janela';
$_lang['NEW_WINDOW'] = 'Nova janela';
$_lang['PARENT_WINDOW'] = 'Na mesma janela';
$_lang['TOP_WINDOW'] = 'Janela superior';
$_lang['NONE'] = 'Nenhum';
$_lang['ELXIS_INTERFACE'] = 'Interface do Elxis';
$_lang['ELXIS_INTERFACE_DESC'] = 'Os links para index.php gerem páginas normais incluindo os módulos, enquanto os links para o inner.php só gerem páginas onde apenas a área principal do componente é vísivel (útil para janelas instantâneas).';
$_lang['FULL_PAGE'] = 'Página completa';
$_lang['ONLY_COMPONENT'] = 'Apenas o componente';
$_lang['POPUP_WINDOW'] = 'Janela pop-up';
$_lang['TYPICAL_POPUP'] = 'Um pop-up típico';
$_lang['LIGHTBOX_WINDOW'] = 'Janela lightbox';
$_lang['PARENT_ITEM'] = 'Item superior';
$_lang['PARENT_ITEM_DESC'] = 'Faça deste item do menu um sub-menu de um outro item de menu, selecionando-o como o principal.';
$_lang['POPUP_WIDTH_DESC'] = 'A largura em píxeis da janela pop-up ou do wrapper. 0 para o controle automático.';
$_lang['POPUP_HEIGHT_DESC'] = 'A altura em píxeis da janela pop-up ou do wrapper. 0 para o controle automático.';
$_lang['MUST_FIRST_SAVE'] = 'Você deve guardar primeiro este item!';
$_lang['CONTENT'] = 'Conteúdo';
$_lang['SECURE_CONNECT'] = 'Ligação segura';
$_lang['SECURE_CONNECT_DESC'] = 'Apenas quando ativado na configruação geral e quando você tiver um certificado SSL instalado.';
$_lang['SEL_COMPONENT'] = 'Selecionar componente';
$_lang['LINK_GENERATOR'] = 'Gestor do link';
$_lang['URL_HELPER'] = 'Escreva o Url completo da página externa e o título do link a qual você se pretende linkar. 
	Você pode abrir este link numa janela pop-up ou numa janela lightbox. Opções de controle das dimensões da largura e altura  
	das janelas  popup/lightbox.';
$_lang['SEPARATOR_HELPER'] = 'Um separador não é um link, mas apenas um texto. Sendo assim, a opção do link não tem importância. 
	Use-o como um cabeçalho não-clicável para os seus sub-menus ou para outra utilização.';
$_lang['WRAPPER_HELPER'] = 'Wrapper permite-lhe exibir qualquer página dentro de seu site que esteja envolvida por um i-frame. 
	As páginas externas vão parecer como se fossem fornecidas pelo seu próprio site. Você deve fornecer o URL completo para 
	a página que tenha o wrapper. Você pode abrir este link numa janela pop-up ou numa janel lightbox. Opções de controle das dimensões da largura e altura  
	da área envolvida e das janelas popup/lightbox.';
$_lang['TIP_INTERFACE'] = '<strong>Tipo</strong><br />Selecione <strong>Apenas a componente</strong> como a interface do Elxis 
	se você pretende abrir o link numa janela popup/lightbox.';
$_lang['COMP_NO_PUBLIC_IFACE'] = 'Este componente não tem uma interface pública!';
$_lang['STANDARD_LINKS'] = 'Links de padrão';
$_lang['BROWSE_ARTICLES'] = 'Navegar artigos';
$_lang['ACTIONS'] = 'Ações';
$_lang['LINK_TO_ITEM'] = 'Linkar para este item';
$_lang['LINK_TO_CAT_RSS'] = 'Linkar para categoria\'s RSS feed';
$_lang['LINK_TO_CAT_ATOM'] = 'Linkar para categoria\'s ATOM feed';
$_lang['LINK_TO_CAT_OR_ARTICLE'] = 'Linkar para uma categoria ou um artigo';
$_lang['ARTICLE'] = 'Artigo';
$_lang['ARTICLES'] = 'Artigos';
$_lang['ASCENDING'] = 'Ascendente';
$_lang['DESCENDING'] = 'Descendente';
$_lang['LAST_MODIFIED'] = 'Última modificação';
$_lang['CAT_CONT_ART'] = "Categoria %s contém %s artigos."; //fill in by CATEGORY NAME and NUMBER
$_lang['ART_WITHOUT_CAT'] = "Existem %s artigos sem categoria."; //fill in by NUMBER
$_lang['NO_ITEMS_DISPLAY'] = 'Não existem itens para serem exibidos!';
$_lang['ROOT'] = 'Origem'; //root category
$_lang['COMP_FRONTPAGE'] = "Frontpage %s do componente"; //fill in by COMPONENT NAME
$_lang['LINK_TO_CAT'] = 'Linkar para o\'s conteúdo\'s da categoria';
$_lang['LINK_TO_CAT_ARTICLE'] = 'Linkar para a\'s categoria\'s do artigo';
$_lang['LINK_TO_AUT_PAGE'] = 'Linkar para uma página autônoma';
$_lang['SPECIAL_LINK'] = 'Link especial';
$_lang['FRONTPAGE'] = 'Frontpage';
$_lang['BASIC_SETTINGS'] = 'Configueções básicas';
$_lang['OTHER_OPTIONS'] = 'Outras opções';
//5.0
$_lang['ICON_FONT'] = 'Ícone';
$_lang['ICON_FONT_DESC'] = 'Se desejar, você também pode exibir uma fonte de ícone (Elxis Font, Font Awesome, etc).';
$_lang['ONCLICK_DESC'] = 'Executar uma ação de javascript no clique do mouse ou no toque';
$_lang['JSCODE'] = 'Código Javascript';
$_lang['JSCODE_DESC'] = 'Função Javascript / código que será executado no clique';

?>