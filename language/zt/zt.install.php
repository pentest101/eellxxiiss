<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: zt-CN (Chinese traditional - Taiwan) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Chong-Bing Liu ( http://EasyApps.biz )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['INSTALLATION'] = '安裝';
$_lang['STEP'] = '步驟';
$_lang['VERSION'] = '版本';
$_lang['VERSION_CHECK'] = '版本檢查';
$_lang['STATUS'] = '狀態';
$_lang['REVISION_NUMBER'] = '修改編號';
$_lang['RELEASE_DATE'] = '發佈日期';
$_lang['ELXIS_INSTALL'] = 'Elxis 安裝';
$_lang['LICENSE'] = '許可證';
$_lang['VERSION_PROLOGUE'] = '您將準備安裝 Elxis CMS. 正確的 Elxis 版本是您準備好安裝如下. 請確定這是最新的 Elxis 發佈版本 
	on <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = '在您開始之前';
$_lang['BEFORE_DESC'] = '在您開始前請小心閱讀如下.';
$_lang['DATABASE'] = '資料庫';
$_lang['DATABASE_DESC'] = '建立一個空的資料庫, 它會被 Elxis 用來存放您的資料. 我們強烈建議使用 <strong>MySQL</strong> 資料庫. 雖然 Elxis 後台也支持資料庫形式像是 PostgreSQL 和 SQLite 3 但經由良好的測試只用 MySQL. 從您的主機端控制台 (CPanel, Plesk, ISP Config, 等) 去建立一個空的資料庫, 或從 
	phpMyAdmin or 或其他資料庫管理工具. 只要提供一個 <strong>名字</strong> 給您的資料庫並且建立它. 
	之後建立資料庫 <strong>使用者</strong> 並指定他給您新建立好的資料庫. 記下資料庫名稱, 使用者名稱與密碼, 您將在安裝時需要用.';
$_lang['REPOSITORY'] = '存放位置';
$_lang['REPOSITORY_DESC'] = 'Elxis 使用特定的資料夾存放快取頁面, 記錄檔, 交談, 備份等. 預設此資料夾命名為 <strong>存放位置(repository)</strong> 同時是放在 Elxis 根目錄上. 此資料夾<strong>必須是可寫入</strong>! 我們強烈建議您 <strong>改名</strong> 此目錄並 <strong>搬移</strong> 它在網頁不可獲得的地方. 搬移後如果您啟動 <strong>開啟(open basedir)</strong> 保護在 PHP 在, 您可能也需要包含存放位置(repository)路徑到允許的路徑.';
$_lang['REPOSITORY_DEFAULT'] = '存放路徑在預設位置!';
$_lang['SAMPLE_ELXPATH'] = '範例 Elxis 路徑';
$_lang['DEF_REPOPATH'] = '預設存放路徑';
$_lang['REQ_REPOPATH'] = '建議的存放路徑';
$_lang['CONTINUE'] = '繼續';
$_lang['I_AGREE_TERMS'] = '我已閱讀, 了解也同意 EPL 協議與條件';
$_lang['LICENSE_NOTES'] = 'Elxis CMS 是一個免費軟體發佈在 <strong>Elxis 公開許可</strong> (EPL). 
	繼續此安裝並使用 Elxis 您必需同意 EPL 的協議與條件. 謹慎地閱讀 Elxis 許可並且如果您同意的話, 請在頁面最下方核取方塊上打勾並繼續. 如果不是, 
	信止這安裝並刪除 Elxis 所有檔案.';
$_lang['SETTINGS'] = '設定';
$_lang['SITE_URL'] = '網站 URL';
$_lang['SITE_URL_DESC'] = '尾巴不用斜線 (如. http://www.example.com)';
$_lang['REPOPATH_DESC'] = '給 Elxis 存放目錄的絕對路徑. 留空白是預設的路徑與名稱.';
$_lang['SETTINGS_DESC'] = '設 Elxis 需要的設定參數. 有些參數是在安裝 Elxis 前需要被設定. 安裝完成後會登入到管理控制台並設定其餘的參數. 
	這是您第一次非常重要的管理任務.';
$_lang['DEF_LANG'] = '預設語系';
$_lang['DEFLANG_DESC'] = '在預設語系的內容可被寫入. 內容在原內容的翻譯 內容在其他語系是預設語系的原文翻譯.';
$_lang['ENCRYPT_METHOD'] = '加密方式';
$_lang['ENCRYPT_KEY'] = '加密金鑰';
$_lang['AUTOMATIC'] = '自動地';
$_lang['GEN_OTHER'] = '產生其他';
$_lang['SITENAME'] = '網站名稱';
$_lang['TYPE'] = '型式';
$_lang['DBTYPE_DESC'] = '我們強烈建議用 MySQL. 可選擇的只有在您系統所支援的驅動程式與 Elxis 安裝包.';
$_lang['HOST'] = '主機(Host)';
$_lang['TABLES_PREFIX'] = '表格前置文字';
$_lang['DSN_DESC'] = '您可提供用一個已準備好的資料來源名稱來連線到資料庫.';
$_lang['SCHEME'] = '計劃(Scheme)';
$_lang['SCHEME_DESC'] = '資料庫檔案的絕對路徑, 如果您使用的資料庫像是 SQLite.';
$_lang['PORT'] = '埠號(Port)';
$_lang['PORT_DESC'] = '預號 MySQL 埠號是 3306. 留下 0 表示自動選擇.';
$_lang['FTPPORT_DESC'] = '預設 FTP 埠號是 21. 留下 0 表示自動選擇.';
$_lang['USE_FTP'] = '使用 FTP';
$_lang['PATH'] = '路徑';
$_lang['FTP_PATH_INFO'] = '對於 Elxis 安裝目錄的 FTP 根目錄相關路徑 (像是: /public_html).';
$_lang['CHECK_FTP_SETS'] = '檢查 FTP 設定';
$_lang['CHECK_DB_SETS'] = '檢查資料庫設定';
$_lang['DATA_IMPORT'] = '資料匯入';
$_lang['SETTINGS_ERRORS'] = '設定您給予包含的錯誤!';
$_lang['NO_QUERIES_WARN'] = '初始資料已匯入到資料庫但看像是沒有任何查詢已被執行. 在進一步前, 確認資料已真實匯入.';
$_lang['RETRY_PREV_STEP'] = '重試上一步驟';
$_lang['INIT_DATA_IMPORTED'] = '初始資料匯入已到資料庫.';
$_lang['QUERIES_EXEC'] = "%s SQL 查詢已被執行."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = '管理者帳號';
$_lang['CONFIRM_PASS'] = '確定密碼';
$_lang['AVOID_COMUNAMES'] = '避免一般帳號像是 admin 與 administrator.';
$_lang['YOUR_DETAILS'] = '您的詳細';
$_lang['PASS_NOMATCH'] = '密碼沒有匹配!';
$_lang['REPOPATH_NOEX'] = '存放路徑不存在!';
$_lang['FINISH'] = 'Finish';
$_lang['FRIENDLY_URLS'] = '友善的 URLs';
$_lang['FRIENDLY_URLS_DESC'] = '我們強烈建議您啟動它. 為讓它運作, Elxis 將會試著改名檔案 htaccess.txt 為
	<strong>.htaccess</strong> . 如有已存在的 .htaccess 檔案在相同資料夾將會被刪除.';
$_lang['GENERAL'] = 'General';
$_lang['ELXIS_INST_SUCC'] = 'Elxis 安裝已成功完成.';
$_lang['ELXIS_INST_WARN'] = 'Elxis 安裝完成及警告.';
$_lang['CNOT_CREA_CONFIG'] = '不能建立 <strong>configuration.php</strong> 檔在 Elxis 根目錄.';
$_lang['CNOT_REN_HTACC'] = '不能改名 <strong>htaccess.txt</strong> 檔案到 <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = '設定檔';
$_lang['CONFIG_FILE_MANUAL'] = '建立手動的 configuration.php 檔案, 複製下列程式碼並貼到文件裡面.';
$_lang['REN_HTACCESS_MANUAL'] = '請手動更名 <strong>htaccess.txt</strong> 檔案到 <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = '什麼是下一步?';
$_lang['RENAME_ADMIN_FOLDER'] = '為加強安全性您可以改名 administration 資料夾 (<em>estia</em>) 到您任何想取的名稱. 
	如果您是這麼做, 您必須也同時更新 .htaccess 裡的內容，取為新的名稱.';
$_lang['LOGIN_CONFIG'] = '登入管理區塊並設成適當的選項.';
$_lang['VISIT_NEW_SITE'] = '訪問您的新網站';
$_lang['VISIT_ELXIS_SUP'] = '訪問 Elxis 技術支持網站';
$_lang['THANKS_USING_ELXIS'] = '謝謝使用 Elxis CMS.';
//Elxis 5.0
$_lang['OTHER_LANGS'] = '其他語言';
$_lang['OTHER_LANGS_DESC'] = '您希望哪些其他語言（預設值除外）可用?';
$_lang['ALL_LANGS'] = '全部';
$_lang['NONE_LANGS'] = '沒有';
$_lang['REMOVE'] = '移除';
$_lang['CONFIG_EMAIL_DISPATCH'] = '設定郵件 email 派送 (選擇性)';
$_lang['SEND_METHOD'] = '傳送方式';
$_lang['RECOMMENDED'] = '建議';
$_lang['SECURE_CONNECTION'] = '安全連線';
$_lang['AUTH_REQUIRED'] = '需要認証';
$_lang['AUTH_METHOD'] = '認証方式';
$_lang['DEFAULT_METHOD'] = '預設';

?>