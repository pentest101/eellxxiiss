<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: zt-CN (Chinese simplified - China) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Chong-Bing Liu ( http://EasyApps.biz )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['INSTALLATION'] = '安装';
$_lang['STEP'] = '步骤';
$_lang['VERSION'] = '版本';
$_lang['VERSION_CHECK'] = '版本检查';
$_lang['STATUS'] = '状态';
$_lang['REVISION_NUMBER'] = '修改编号';
$_lang['RELEASE_DATE'] = '发布日期';
$_lang['ELXIS_INSTALL'] = 'Elxis 安装';
$_lang['LICENSE'] = '许可证';
$_lang['VERSION_PROLOGUE'] = '您将准备安装 Elxis CMS. 正确的 Elxis 版本是您准备好安装如下. 请确定这是最新的 Elxis 发布版本 
	on <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = '在您开始之前';
$_lang['BEFORE_DESC'] = '在您开始前请小心阅读如下.';
$_lang['DATABASE'] = '资料库';
$_lang['DATABASE_DESC'] = '建立一个空的资料库, 它会被 Elxis 用来存放您的资料. 我们强烈建议使用 <strong>MySQL</strong> 资料库. 虽然 Elxis 后台也支持资料库形式像是 PostgreSQL 和 SQLite 3 但经由良好的测试只用 MySQL. 从您的主机端控制台 (CPanel, Plesk, ISP Config, 等) 去建立一个空的资料库, 或从 
	phpMyAdmin or 或其他资料库管理工具. 只要提供一个 <strong>名字</strong> 给您的资料库并且建立它. 
	之后建立资料库 <strong>使用者</strong> 并指定他给您新建立好的资料库. 记下资料库名称, 使用者名称与密码, 您将在安装时需要用.';
$_lang['REPOSITORY'] = '存放位置';
$_lang['REPOSITORY_DESC'] = 'Elxis 使用特定的资料夹存放快取页面, 记录档, 交谈, 备份等. 预设此资料夹命名为 <strong>存放位置(repository)</strong> 同时是放在 Elxis 根目录上. 此资料夹<strong>必须是可写入</strong>! 我们强烈建议您 <strong>改名</strong> 此目录并 <strong>搬移</strong> 它在网页不可获得的地方. 搬移后如果您启动 <strong>开启(open basedir)</strong> 保护在 PHP 在, 您可能也需要包含存放位置(repository)路径到允许的路径.';
$_lang['REPOSITORY_DEFAULT'] = '存放路径在预设位置!';
$_lang['SAMPLE_ELXPATH'] = '范例 Elxis 路径';
$_lang['DEF_REPOPATH'] = '预设存放路径';
$_lang['REQ_REPOPATH'] = '建议的存放路径';
$_lang['CONTINUE'] = '继续';
$_lang['I_AGREE_TERMS'] = '我已阅读, 了解也同意 EPL 协议与条件';
$_lang['LICENSE_NOTES'] = 'Elxis CMS 是一个免费软体发布在 <strong>Elxis 公开许可</strong> (EPL). 
	继续此安装并使用 Elxis 您必需同意 EPL 的协议与条件. 谨慎地阅读 Elxis 许可并且如果您同意的话, 请在页面最下方核取方块上打勾并继续. 如果不是, 
	信止这安装并删除 Elxis 所有档案.';
$_lang['SETTINGS'] = '设定';
$_lang['SITE_URL'] = '网站 URL';
$_lang['SITE_URL_DESC'] = '尾巴不用斜线 (如. http://www.example.com)';
$_lang['REPOPATH_DESC'] = '给 Elxis 存放目录的绝对路径. 留空白是预设的路径与名称.';
$_lang['SETTINGS_DESC'] = '设 Elxis 需要的设定参数. 有些参数是在安装 Elxis 前需要被设定. 安装完成后会登入到管理控制台并设定其余的参数. 
	这是您第一次非常重要的管理任务.';
$_lang['DEF_LANG'] = '预设语系';
$_lang['DEFLANG_DESC'] = '在预设语系的内容可被写入. 内容在原内容的翻译 内容在其他语系是预设语系的原文翻译.';
$_lang['ENCRYPT_METHOD'] = '加密方式';
$_lang['ENCRYPT_KEY'] = '加密金钥';
$_lang['AUTOMATIC'] = '自动地';
$_lang['GEN_OTHER'] = '产生其他';
$_lang['SITENAME'] = '网站名称';
$_lang['TYPE'] = '型式';
$_lang['DBTYPE_DESC'] = '我们强烈建议用 MySQL. 可选择的只有在您系统所支援的驱动程式与 Elxis 安装包.';
$_lang['HOST'] = '主机(Host)';
$_lang['TABLES_PREFIX'] = '表格前置文字';
$_lang['DSN_DESC'] = '您可提供用一个已准备好的资料来源名称来连线到资料库.';
$_lang['SCHEME'] = '计划(Scheme)';
$_lang['SCHEME_DESC'] = '资料库档案的绝对路径, 如果您使用的资料库像是 SQLite.';
$_lang['PORT'] = '埠号(Port)';
$_lang['PORT_DESC'] = '预号 MySQL 埠号是 3306. 留下 0 表示自动选择.';
$_lang['FTPPORT_DESC'] = '预设 FTP 埠号是 21. 留下 0 表示自动选择.';
$_lang['USE_FTP'] = '使用 FTP';
$_lang['PATH'] = '路径';
$_lang['FTP_PATH_INFO'] = '对于 Elxis 安装目录的 FTP 根目录相关路径 (像是: /public_html).';
$_lang['CHECK_FTP_SETS'] = '检查 FTP 设定';
$_lang['CHECK_DB_SETS'] = '检查资料库设定';
$_lang['DATA_IMPORT'] = '资料汇入';
$_lang['SETTINGS_ERRORS'] = '设定您给予包含的错误!';
$_lang['NO_QUERIES_WARN'] = '初始资料已汇入到资料库但看像是没有任何查询已被执行. 在进一步前, 确认资料已真实汇入.';
$_lang['RETRY_PREV_STEP'] = '重试上一步骤';
$_lang['INIT_DATA_IMPORTED'] = '初始资料汇入已到资料库.';
$_lang['QUERIES_EXEC'] = "%s SQL 查询已被执行."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = '管理者帐号';
$_lang['CONFIRM_PASS'] = '确定密码';
$_lang['AVOID_COMUNAMES'] = '避免一般帐号像是 admin 与 administrator.';
$_lang['YOUR_DETAILS'] = '您的详细';
$_lang['PASS_NOMATCH'] = '密码没有匹配!';
$_lang['REPOPATH_NOEX'] = '存放路径不存在!';
$_lang['FINISH'] = 'Finish';
$_lang['FRIENDLY_URLS'] = '友善的 URLs';
$_lang['FRIENDLY_URLS_DESC'] = '我们强烈建议您启动它. 为让它运作, Elxis 将会试着改名档案 htaccess.txt 为
	<strong>.htaccess</strong> . 如有已存在的 .htaccess 档案在相同资料夹将会被删除.';
$_lang['GENERAL'] = 'General';
$_lang['ELXIS_INST_SUCC'] = 'Elxis 安装已成功完成.';
$_lang['ELXIS_INST_WARN'] = 'Elxis 安装完成及警告.';
$_lang['CNOT_CREA_CONFIG'] = '不能建立 <strong>configuration.php</strong> 档在 Elxis 根目录.';
$_lang['CNOT_REN_HTACC'] = '不能改名 <strong>htaccess.txt</strong> 档案到 <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = '设定档';
$_lang['CONFIG_FILE_MANUAL'] = '建立手动的 configuration.php 档案, 复制下列程式码并贴到文件里面.';
$_lang['REN_HTACCESS_MANUAL'] = '请手动更名 <strong>htaccess.txt</strong> 档案到 <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = '什么是下一步?';
$_lang['RENAME_ADMIN_FOLDER'] = '为加强安全性您可以改名 administration 资料夹 (<em>estia</em>) 到您任何想取的名称. 
	如果您是这么做, 您必须也同时更新 .htaccess 里的内容，取为新的名称.';
$_lang['LOGIN_CONFIG'] = '登入管理区块并设成适当的选项.';
$_lang['VISIT_NEW_SITE'] = '访问您的新网站';
$_lang['VISIT_ELXIS_SUP'] = '访问 Elxis 技术支持网站';
$_lang['THANKS_USING_ELXIS'] = '谢谢使用 Elxis CMS.';
//Elxis 5.0
$_lang['OTHER_LANGS'] = '其他语言';
$_lang['OTHER_LANGS_DESC'] = '您希望哪些其他语言（预设值除外）可用?';
$_lang['ALL_LANGS'] = '全部';
$_lang['NONE_LANGS'] = '没有';
$_lang['REMOVE'] = '移除';
$_lang['CONFIG_EMAIL_DISPATCH'] = '设定邮件 email 派送 (选择性)';
$_lang['SEND_METHOD'] = '传送方式';
$_lang['RECOMMENDED'] = '建议';
$_lang['SECURE_CONNECTION'] = '安全连线';
$_lang['AUTH_REQUIRED'] = '需要认证';
$_lang['AUTH_METHOD'] = '认证方式';
$_lang['DEFAULT_METHOD'] = '预设';

?>