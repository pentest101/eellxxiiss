<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: zt-CN (Chinese traditional - Taiwan) language for component eMenu
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Chong-Bing Liu ( http://EasyApps.biz )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['MENU'] = '選單';
$_lang['MENU_MANAGER'] = '選單管理';
$_lang['MENU_ITEM_COLLECTIONS'] = '選單項目彙整';
$_lang['SN'] = '序號(S/N)'; //serial number
$_lang['MENU_ITEMS'] = '選單項目';
$_lang['COLLECTION'] = '彙整';
$_lang['WARN_DELETE_COLLECT'] = '此將刪除所有彙整, 所有選單項目與已有指定的模組!';
$_lang['CNOT_DELETE_MAINMENU'] = '您不能刪所有主要選單 (mainmenu) 及彙整內容!';
$_lang['MODULE_TITLE'] = '模組標題';
$_lang['COLLECT_NAME_INFO'] = '彙整名稱應是唯一識別, 並用英文字元不含空白!';
$_lang['ADD_NEW_COLLECT'] = '新增彙整';
$_lang['EXIST_COLLECT_NAME'] = '已有此彙整名稱!';
$_lang['MANAGE_MENU_ITEMS'] = '管理選單項目';
$_lang['EXPAND'] = '展開';
$_lang['FULL'] = '全部';
$_lang['LIMITED'] = '有限的';
$_lang['TYPE'] = '型式';
$_lang['LEVEL'] = '層級';
$_lang['MAX_LEVEL'] = '最大層級';
$_lang['LINK'] = '連結';
$_lang['ELXIS_LINK'] = 'Elxis 連結';
$_lang['SEPARATOR'] = '分隔線';
$_lang['WRAPPER'] = '包裹';
$_lang['WARN_DELETE_MENUITEM'] = '您確定要刪除此選單項目? 所有子項目也會被刪除!';
$_lang['SEL_MENUITEM_TYPE'] = '選擇選單項目型式';
$_lang['LINK_LINK_DESC'] = '連結到 Elxis 頁面.';
$_lang['LINK_URL_DESC'] = '標準連結到外部頁面.';
$_lang['LINK_SEPARATOR_DESC'] = '純文字串沒有連結.';
$_lang['LINK_WRAPPER_DESC'] = '內部網站連到外部頁面顯示.';
$_lang['EXPAND_DESC'] = '產生, 如果支援子選單. 在整個樹狀限制展開顯示只有第一層選單.';
$_lang['LINK_TARGET'] = '連結目標';
$_lang['SELF_WINDOW'] = '本身視窗';
$_lang['NEW_WINDOW'] = '新增視窗';
$_lang['PARENT_WINDOW'] = '回母視窗';
$_lang['TOP_WINDOW'] = '上窗視窗';
$_lang['NONE'] = 'None';
$_lang['ELXIS_INTERFACE'] = 'Elxis 介面';
$_lang['ELXIS_INTERFACE_DESC'] = '連到 index.php 產生一般頁面包含模組, 在連到 inner.php 頁面只有主元件區是可見(這在彈跳視窗很管用).';
$_lang['FULL_PAGE'] = '全頁面';
$_lang['ONLY_COMPONENT'] = '只有元件';
$_lang['POPUP_WINDOW'] = '彈跳視窗';
$_lang['TYPICAL_POPUP'] = '典型彈跳';
$_lang['LIGHTBOX_WINDOW'] = 'Lightbox 視窗';
$_lang['PARENT_ITEM'] = '母項目';
$_lang['PARENT_ITEM_DESC'] = '使其他選單項目的子選單也成為母選單.';
$_lang['POPUP_WIDTH_DESC'] = '彈跳視窗寬度或在像素裡打包. 設 0 為自動控制.';
$_lang['POPUP_HEIGHT_DESC'] = '彈跳視窗長度或在像素裡打包. 設 0 為自動控制.';
$_lang['MUST_FIRST_SAVE'] = '您必需先儲存此項!';
$_lang['CONTENT'] = '內容';
$_lang['SECURE_CONNECT'] = '安全性連結';
$_lang['SECURE_CONNECT_DESC'] = '只能啟動在一般設定, 並且需要有安裝 SSL 認証.';
$_lang['SEL_COMPONENT'] = '選擇元件';
$_lang['LINK_GENERATOR'] = '連結產生器';
$_lang['URL_HELPER'] = '填入完整 URL 給外部頁面, 在您想要本身連結與標題在連結上. 
	您可以開啟這連結在彈跳或在 lightbox 視窗. 而彈跳或在 lightbox 視窗的寬度與高度尺寸控制是選擇性.';
$_lang['SEPARATOR_HELPER'] = '分隔線不是連結只是文字. 所以連結選項不是很重要. 
	使用在您的子選單做為不可點選的檔頭, 或其他使用.';
$_lang['WRAPPER_HELPER'] = '打包允許您可以顯示任何頁面在網站裡, 主要用 i-frame. 
	外部頁面內容看起來就像是自己的網站所提供. 您必須提供完整的 URL 以便打包頁面. 您可用彈跳或 lightbox 視窗來開啟此連結. 打包區塊與彈跳 / lightbox的寬度與高度尺寸控制是選擇性.';
$_lang['TIP_INTERFACE'] = '<strong>技巧</strong><br />選擇 <strong>只有元件</strong> 為 Elxis 介面, 如果您想開啟連結在彈跳/lightbox 視窗.';
$_lang['COMP_NO_PUBLIC_IFACE'] = '此元件沒有公開介面!';
$_lang['STANDARD_LINKS'] = '標準連結';
$_lang['BROWSE_ARTICLES'] = '瀏覽文章';
$_lang['ACTIONS'] = '動作';
$_lang['LINK_TO_ITEM'] = '連到此項目';
$_lang['LINK_TO_CAT_RSS'] = '連到分類的 RSS 摘要';
$_lang['LINK_TO_CAT_ATOM'] = '連到分類的 ATOM 摘要';
$_lang['LINK_TO_CAT_OR_ARTICLE'] = '連到分類或文章';
$_lang['ARTICLE'] = '文章';
$_lang['ARTICLES'] = '所有文章';
$_lang['ASCENDING'] = '升序'; // 升序和降序
$_lang['DESCENDING'] = '降序';
$_lang['LAST_MODIFIED'] = '最後修改';
$_lang['CAT_CONT_ART'] = "分類 %s 包含 %s 文章."; //fill in by CATEGORY NAME and NUMBER
$_lang['ART_WITHOUT_CAT'] = "已有 %s 文章沒有分類."; //fill in by NUMBER
$_lang['NO_ITEMS_DISPLAY'] = '沒有項目可顯示!';
$_lang['ROOT'] = 'Root'; //root category
$_lang['COMP_FRONTPAGE'] = "元件的 %s 前端網頁"; //fill in by COMPONENT NAME
$_lang['LINK_TO_CAT'] = '連結到內容的分類';
$_lang['LINK_TO_CAT_ARTICLE'] = '連到分類的文章';
$_lang['LINK_TO_AUT_PAGE'] = '連結到Link to 獨立頁面';
$_lang['SPECIAL_LINK'] = '特殊連結';
$_lang['FRONTPAGE'] = '前端網頁';
$_lang['BASIC_SETTINGS'] = '基本設定';
$_lang['OTHER_OPTIONS'] = '其他選項';
//5.0
$_lang['ICON_FONT'] = '圖示';
$_lang['ICON_FONT_DESC'] = '如果你想要可以也顯示圖示字型 (如:Elxis Font, Font Awesome, 等).';
$_lang['ONCLICK_DESC'] = '用滑鼠點擊或輕觸來執行 javascript 動作';
$_lang['JSCODE'] = 'Javascript 程式';
$_lang['JSCODE_DESC'] = '點擊後 Javascript 函數/程式 會被執行';

?>