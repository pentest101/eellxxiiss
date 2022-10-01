<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: zt-CN (Chinese simplified - China) language for component eMenu
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Chong-Bing Liu ( http://EasyApps.biz )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['MENU'] = '选单';
$_lang['MENU_MANAGER'] = '选单管理';
$_lang['MENU_ITEM_COLLECTIONS'] = '选单项目汇整';
$_lang['SN'] = '序号(S/N)'; //serial number
$_lang['MENU_ITEMS'] = '选单项目';
$_lang['COLLECTION'] = '汇整';
$_lang['WARN_DELETE_COLLECT'] = '此将删除所有汇整, 所有选单项目与已有指定的模组!';
$_lang['CNOT_DELETE_MAINMENU'] = '您不能删所有主要选单 (mainmenu) 及汇整内容!';
$_lang['MODULE_TITLE'] = '模组标题';
$_lang['COLLECT_NAME_INFO'] = '汇整名称应是唯一识别, 并用英文字元不含空白!';
$_lang['ADD_NEW_COLLECT'] = '新增汇整';
$_lang['EXIST_COLLECT_NAME'] = '已有此汇整名称!';
$_lang['MANAGE_MENU_ITEMS'] = '管理选单项目';
$_lang['EXPAND'] = '展开';
$_lang['FULL'] = '全部';
$_lang['LIMITED'] = '有限的';
$_lang['TYPE'] = '型式';
$_lang['LEVEL'] = '层级';
$_lang['MAX_LEVEL'] = '最大层级';
$_lang['LINK'] = '连结';
$_lang['ELXIS_LINK'] = 'Elxis 连结';
$_lang['SEPARATOR'] = '分隔线';
$_lang['WRAPPER'] = '包裹';
$_lang['WARN_DELETE_MENUITEM'] = '您确定要删除此选单项目? 所有子项目也会被删除!';
$_lang['SEL_MENUITEM_TYPE'] = '选择选单项目型式';
$_lang['LINK_LINK_DESC'] = '连结到 Elxis 页面.';
$_lang['LINK_URL_DESC'] = '标准连结到外部页面.';
$_lang['LINK_SEPARATOR_DESC'] = '纯文字串没有连结.';
$_lang['LINK_WRAPPER_DESC'] = '内部网站连到外部页面显示.';
$_lang['EXPAND_DESC'] = '产生, 如果支援子选单. 在整个树状限制展开显示只有第一层选单.';
$_lang['LINK_TARGET'] = '连结目标';
$_lang['SELF_WINDOW'] = '本身视窗';
$_lang['NEW_WINDOW'] = '新增视窗';
$_lang['PARENT_WINDOW'] = '回母视窗';
$_lang['TOP_WINDOW'] = '上窗视窗';
$_lang['NONE'] = 'None';
$_lang['ELXIS_INTERFACE'] = 'Elxis 介面';
$_lang['ELXIS_INTERFACE_DESC'] = '连到 index.php 产生一般页面包含模组, 在连到 inner.php 页面只有主元件区是可见(这在弹跳视窗很管用).';
$_lang['FULL_PAGE'] = '全页面';
$_lang['ONLY_COMPONENT'] = '只有元件';
$_lang['POPUP_WINDOW'] = '弹跳视窗';
$_lang['TYPICAL_POPUP'] = '典型弹跳';
$_lang['LIGHTBOX_WINDOW'] = 'Lightbox 视窗';
$_lang['PARENT_ITEM'] = '母项目';
$_lang['PARENT_ITEM_DESC'] = '使其他选单项目的子选单也成为母选单.';
$_lang['POPUP_WIDTH_DESC'] = '弹跳视窗宽度或在像素里打包. 设 0 为自动控制.';
$_lang['POPUP_HEIGHT_DESC'] = '弹跳视窗长度或在像素里打包. 设 0 为自动控制.';
$_lang['MUST_FIRST_SAVE'] = '您必需先储存此项!';
$_lang['CONTENT'] = '内容';
$_lang['SECURE_CONNECT'] = '安全性连结';
$_lang['SECURE_CONNECT_DESC'] = '只能启动在一般设定, 并且需要有安装 SSL 认证.';
$_lang['SEL_COMPONENT'] = '选择元件';
$_lang['LINK_GENERATOR'] = '连结产生器';
$_lang['URL_HELPER'] = '填入完整 URL 给外部页面, 在您想要本身连结与标题在连结上. 
	您可以开启这连结在弹跳或在 lightbox 视窗. 而弹跳或在 lightbox 视窗的宽度与高度尺寸控制是选择性.';
$_lang['SEPARATOR_HELPER'] = '分隔线不是连结只是文字. 所以连结选项不是很重要. 
	使用在您的子选单做为不可点选的档头, 或其他使用.';
$_lang['WRAPPER_HELPER'] = '打包允许您可以显示任何页面在网站里, 主要用 i-frame. 
	外部页面内容看起来就像是自己的网站所提供. 您必须提供完整的 URL 以便打包页面. 您可用弹跳或 lightbox 视窗来开启此连结. 打包区块与弹跳 / lightbox的宽度与高度尺寸控制是选择性.';
$_lang['TIP_INTERFACE'] = '<strong>技巧</strong><br />选择 <strong>只有元件</strong> 为 Elxis 介面, 如果您想开启连结在弹跳/lightbox 视窗.';
$_lang['COMP_NO_PUBLIC_IFACE'] = '此元件没有公开介面!';
$_lang['STANDARD_LINKS'] = '标准连结';
$_lang['BROWSE_ARTICLES'] = '浏览文章';
$_lang['ACTIONS'] = '动作';
$_lang['LINK_TO_ITEM'] = '连到此项目';
$_lang['LINK_TO_CAT_RSS'] = '连到分类的 RSS 摘要';
$_lang['LINK_TO_CAT_ATOM'] = '连到分类的 ATOM 摘要';
$_lang['LINK_TO_CAT_OR_ARTICLE'] = '连到分类或文章';
$_lang['ARTICLE'] = '文章';
$_lang['ARTICLES'] = '所有文章';
$_lang['ASCENDING'] = '升序'; // 升序和降序
$_lang['DESCENDING'] = '降序';
$_lang['LAST_MODIFIED'] = '最后修改';
$_lang['CAT_CONT_ART'] = "分类 %s 包含 %s 文章."; //fill in by CATEGORY NAME and NUMBER
$_lang['ART_WITHOUT_CAT'] = "已有 %s 文章没有分类."; //fill in by NUMBER
$_lang['NO_ITEMS_DISPLAY'] = '没有项目可显示!';
$_lang['ROOT'] = 'Root'; //root category
$_lang['COMP_FRONTPAGE'] = "元件的 %s 前端网页"; //fill in by COMPONENT NAME
$_lang['LINK_TO_CAT'] = '连结到内容的分类';
$_lang['LINK_TO_CAT_ARTICLE'] = '连到分类的文章';
$_lang['LINK_TO_AUT_PAGE'] = '连结到Link to 独立页面';
$_lang['SPECIAL_LINK'] = '特殊连结';
$_lang['FRONTPAGE'] = '前端网页';
$_lang['BASIC_SETTINGS'] = '基本设定';
$_lang['OTHER_OPTIONS'] = '其他选项';
//5.0
$_lang['ICON_FONT'] = '图示';
$_lang['ICON_FONT_DESC'] = '如果你想要可以也显示图示字型 (如:Elxis Font, Font Awesome, 等).';
$_lang['ONCLICK_DESC'] = '用滑鼠点击或轻触来执行 javascript 动作';
$_lang['JSCODE'] = 'Javascript 程式';
$_lang['JSCODE_DESC'] = '点击后 Javascript 函数/程式 会被执行';

?>