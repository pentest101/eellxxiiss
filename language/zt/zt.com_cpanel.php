<?php 
/**
* @version: 5.2
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( https://www.elxis.org )
* @copyright: (C) 2006-2021 Elxis.org. All rights reserved.
* @description: zt-CN (Chinese traditional - Taiwan) language for component CPanel
* @license: Elxis public license https://www.elxis.org/elxis-public-license.html
* @translator: Chong-Bing Liu ( https://EasyApps.Biz )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['CONTROL_PANEL'] = '控制台';
$_lang['GENERAL_SITE_SETS'] = '常見網站設定';
$_lang['LANGS_MANAGER'] = '語系管理';
$_lang['MANAGE_SITE_LANGS'] = '管理網站語系';
$_lang['USERS'] = '用戶';
$_lang['MANAGE_USERS'] = '建立, 編輯, 刪除用戶帳號';
$_lang['USER_GROUPS'] = '用戶群組';
$_lang['MANAGE_UGROUPS'] = '管理用戶群組';
$_lang['MEDIA_MANAGER'] = '媒體管理';
$_lang['MEDIA_MANAGER_INFO'] = '管理多媒體檔案';
$_lang['ACCESS_MANAGER'] = '存取管理';
$_lang['MANAGE_ACL'] = '管理存取控制列表';
$_lang['MENU_MANAGER'] = '選單管理';
$_lang['MANAGE_MENUS_ITEMS'] = '管理選單與選單項目';
$_lang['FRONTPAGE'] = '前端網頁';
$_lang['DESIGN_FRONTPAGE'] = '設計網站前端頁面';
$_lang['CATEGORIES_MANAGER'] = '分類管理';
$_lang['MANAGE_CONT_CATS'] = '管理內容分類';
$_lang['CONTENT_MANAGER'] = '內容管理';
$_lang['MANAGE_CONT_ITEMS'] = '管理內容項目';
$_lang['MODULES_MANAGE_INST'] = '管理模組與安裝.';
$_lang['PLUGINS_MANAGE_INST'] = '管理內容插件與安裝.';
$_lang['COMPONENTS_MANAGE_INST'] = '管理元件與安裝.';
$_lang['TEMPLATES_MANAGE_INST'] = '管理樣版與安裝.';
$_lang['SENGINES_MANAGE_INST'] = '管理搜尋引擎與安裝.';
$_lang['MANAGE_WAY_LOGIN'] = '管理用戶方式與登入網站.';
$_lang['TRANSLATOR'] = '譯者';
$_lang['MANAGE_MLANG_CONTENT'] = '管理多語系內容';
$_lang['LOGS'] = '記錄';
$_lang['VIEW_MANAGE_LOGS'] = '閱讀與管理記錄檔案';
$_lang['GENERAL'] = '通用';
$_lang['WEBSITE_STATUS'] = '網站狀況';
$_lang['ONLINE'] = '在線';
$_lang['OFFLINE'] = '離線';
$_lang['ONLINE_ADMINS'] = '只有管理者在線';
$_lang['OFFLINE_MSG'] = '離線訊息';
$_lang['OFFLINE_MSG_INFO'] = '留此欄位為空, 來顯示自動化的多國語訊息';
$_lang['SITENAME'] = '網站名稱';
$_lang['URL_ADDRESS'] = 'URL 位址';
$_lang['REPO_PATH'] = '存放路徑';
$_lang['REPO_PATH_INFO'] = '完整路徑到 Elxis 存放夾. 留空為預設 
	位置 (elxis_root/repository/). 我們強烈建議您移此目錄在 WWW 資料夾以上並命名為不可猜得名字!';
$_lang['FRIENDLY_URLS'] = '友善的 URLs';
$_lang['SEF_INFO'] = '如選擇 YES (建議) 改名 htaccess.txt 檔案為 .htaccess';
$_lang['STATISTICS_INFO'] = '啟動網站流量統計?';
$_lang['GZIP_COMPRESSION'] = 'GZip 壓縮';
$_lang['GZIP_COMPRESSION_DESC'] = '在傳送到瀏覽器前, Elxis 將會用 GZIP 方式壓縮檔案, 因此您可省下約 70% 到 80% 的頻寬.';
$_lang['DEFAULT_ROUTE'] = '預設路由';
$_lang['DEFAULT_ROUTE_INFO'] = 'Elxis 格式化的URI 會被用來當成是首頁';
$_lang['META_DATA'] = 'META 資料';
$_lang['META_DATA_INFO'] = '用簡短文字描述網站';
$_lang['KEYWORDS'] = '關鍵字';
$_lang['KEYWORDS_INFO'] = '用幾個關鍵字來並以逗號分開';
$_lang['STYLE_LAYOUT'] = '樣式與版面';
$_lang['SITE_TEMPLATE'] = '網站樣版';
$_lang['ADMIN_TEMPLATE'] = '管理樣版';
$_lang['ICONS_PACK'] = '圖示包';
$_lang['LOCALE'] = '場所';
$_lang['TIMEZONE'] = '時間區';
$_lang['MULTILINGUISM'] = '多語言';
$_lang['MULTILINGUISM_INFO'] = '能讓您輸入文字元素超過一個語言 (翻譯). 
	沒啟動時,也是有可能使網站變慢，因此設定 No 時, Elxis 介面也是會變成多國語. .';
$_lang['CHANGE_LANG'] = '改變語系';
$_lang['LANG_CHANGE_WARN'] = '如果不改變預設語系, 在語系的指標與翻譯表裡的翻譯, 很可能變成不一致性.';
$_lang['CACHE'] = '快取';
$_lang['CACHE_INFO'] = 'Elxis 可以儲存藉由個單一元素所產生的 HTML 碼到快取使之後再產生時更加快速. 
	這是常見設定, 您必需在需要快取的元素上啟動(如:模組).';
$_lang['APC_INFO'] = '這是另外一種 PHP 快取 (APC), 給 PHP 的 opcode 快取. 此需您的網站伺服器有支援. 
	不建議在共享的伺服器環境使用. Elxis 將會使用它在特殊頁面來增加效能.';
$_lang['APC_ID_INFO'] = '在超過 1 個站台是存放在同一台伺服器時來識別它, 就是由此站台唯一的整數識別.';
$_lang['USERS_AND_REGISTRATION'] = '用戶與註冊';
$_lang['PRIVACY_PROTECTION'] = '隱密保護';
$_lang['PASSWORD_NOT_SHOWN'] = '由於安全理由, 目前密碼不會顯示. 
	如果您想改變目前的密碼才填此欄位.';
$_lang['DB_TYPE'] = '資料庫型態';
$_lang['ALERT_CON_LOST'] = '如果您改變了目前資料連結線將會被中斷!';
$_lang['HOST'] = '主機(Host)';
$_lang['PORT'] = '埠號(Port)';
$_lang['PERSISTENT_CON'] = '持續連線';
$_lang['DB_NAME'] = '資料庫名稱';
$_lang['TABLES_PREFIX'] = '表格前置字串';
$_lang['DSN_INFO'] = '準備好的資料來源名稱用來連到資料庫.';
$_lang['SCHEME'] = '摘要(Scheme)';
$_lang['SCHEME_INFO'] = '絕對路徑連到資料庫檔, 假如您是用資料庫像是SQLite.';
$_lang['SEND_METHOD'] = '傳送方式';
$_lang['SMTP_OPTIONS'] = 'SMTP 選項';
$_lang['AUTH_REQ'] = '需要驗証';
$_lang['SECURE_CON'] = '安全連線';
$_lang['SENDER_NAME'] = '寄送者名稱';
$_lang['SENDER_EMAIL'] = '寄送者郵件';
$_lang['RCPT_NAME'] = '收信者名稱';
$_lang['RCPT_EMAIL'] = '收信者郵件';
$_lang['TECHNICAL_MANAGER'] = '技術人員';
$_lang['TECHNICAL_MANAGER_INFO'] = '技術人員會收到錯誤或安全相關警訊.';
$_lang['USE_FTP'] = '使用 FTP';
$_lang['PATH'] = '路徑';
$_lang['FTP_PATH_INFO'] = '從 FTP 根目錄的相對路徑到 Exlis 安裝目錄 (如: /public_html).';
$_lang['SESSION'] = '交談(Session)';
$_lang['HANDLER'] = '處理者(Handler)';
$_lang['HANDLER_INFO'] = 'Elxis 能以檔案方式儲存交談內容到存放地, 或存到資料庫. 
	您可认選擇 None 來讓 PHP 儲存交談資料到伺服器的預設位置.';
$_lang['FILES'] = '檔案';
$_lang['LIFETIME'] = '使用期';
$_lang['SESS_LIFETIME_INFO'] = '當您閒置時, 會直到交談時間過期.';
$_lang['CACHE_TIME_INFO'] = '此快取項獲得再次產生之後.';
$_lang['MINUTES'] = '分鐘';
$_lang['HOURS'] = '小時';
$_lang['MATCH_IP'] = '符合 IP';
$_lang['MATCH_BROWSER'] = '符合瀏覽器';
$_lang['MATCH_REFERER'] = '符合 HTTP 參照位址';
$_lang['MATCH_SESS_INFO'] = '給予進階交談驗證路由.';
$_lang['ENCRYPTION'] = '加密';
$_lang['ENCRYPT_SESS_INFO'] = '加密交談資料?';
$_lang['ERRORS'] = '錯誤';
$_lang['WARNINGS'] = '警告';
$_lang['NOTICES'] = '提醒';
$_lang['NOTICE'] = '提醒';
$_lang['REPORT'] = '報告';
$_lang['REPORT_INFO'] = '錯誤報告級別. 在正式網站我們建議您設成 off .';
$_lang['LOG'] = 'Log';
$_lang['LOG_INFO'] = '錯誤記錄級別. 選擇哪個錯誤是您想要 Elxis 寫入系統記錄, (repository/logs/).';
$_lang['ALERT'] = '警告';
$_lang['ALERT_INFO'] = '寄嚴重錯誤給網站管理者.';
$_lang['ROTATE'] = '輪流';
$_lang['ROTATE_INFO'] = '輪流錯誤記錄在每個月底. 推薦此項.';
$_lang['DEBUG'] = '除錯';
$_lang['MODULE_POS'] = '模組位置';
$_lang['MINIMAL'] = '最小';
$_lang['FULL'] = '最大';
$_lang['DISPUSERS_AS'] = '顯示用戶為';
$_lang['USERS_REGISTRATION'] = '用戶註冊';
$_lang['ALLOWED_DOMAIN'] = '允許的網域';
$_lang['ALLOWED_DOMAIN_INFO'] = '寫下網域名稱 (如. elxis.org) 系統將可接受註冊郵件位址.';
$_lang['EXCLUDED_DOMAINS'] = '不允許的網域';
$_lang['EXCLUDED_DOMAINS_INFO'] = '請用逗號分開網域名 (i.e. badsite.com,hacksite.com) 
	這是在註冊時不能接受.';
$_lang['ACCOUNT_ACTIVATION'] = '帳號啟動';
$_lang['DIRECT'] = '直接';
$_lang['MANUAL_BY_ADMIN'] = '由管理者手動';
$_lang['PASS_RECOVERY'] = '密碼恢復';
$_lang['SECURITY'] = '安全性';
$_lang['SECURITY_LEVEL'] = '安全性等級';
$_lang['SECURITY_LEVEL_INFO'] = '有些選項可以強制啟動以增加安全等級, 但有些功能可能會關閉. 詳細內容可參閱 Elxis 文件.';
$_lang['NORMAL'] = '正常';
$_lang['HIGH'] = '高';
$_lang['INSANE'] = '極瘋狂';
$_lang['ENCRYPT_METHOD'] = '加密方法';
$_lang['AUTOMATIC'] = '自動';
$_lang['ENCRYPTION_KEY'] = '加密金鑰';
$_lang['ELXIS_DEFENDER'] = 'Elxis 防禦者';
$_lang['ELXIS_DEFENDER_INFO'] = 'Elxis 防禦者從 XSS 與 SQL 資料隱碼攻擊情況下保護您的網站. 
	這是強大工具, 過濾用戶對您的網站要求與封鎖攻擊. 這也會通知您在攻擊時與記錄起來. 您可選擇哪種過濾型式來做應用,或甚至針對未認可的修改, 鎖住您系統的關鍵性檔案. 較多的過濾會使您的網站更慢, 但網站仍持續在運行. 
	我們建議啟動 G, C 與 F. 可參考 Elxis 文件獲得更多資訊.';
$_lang['SSL_SWITCH'] = 'SSL 切換';
$_lang['SSL_SWITCH_INFO'] = 'Elxis 將會自動地在保密重要頁面從 HTTP 到 HTTPS 切換. 
	對管理 HTTPS 頁面將持久固定. 此需要 SSL 認証!';
$_lang['PUBLIC_AREA'] = '公開區域';
$_lang['GENERAL_FILTERS'] = '常用規則';
$_lang['CUSTOM_FILTERS'] = '自定義規則';
$_lang['FSYS_PROTECTION'] = '檔案系統保護';
$_lang['CHECK_FTP_SETS'] = '檢查 FTP 設定值';
$_lang['FTP_CON_SUCCESS'] = '已成功連到 FTP 伺服器.';
$_lang['ELXIS_FOUND_FTP'] = 'Elxis 安裝時找到 FTP.';
$_lang['ELXIS_NOT_FOUND_FTP'] = 'Elxis 安裝時沒有找到 FTP! 選確認 FTP 路徑選項.';
$_lang['CAN_NOT_CHANGE'] = '您不可更改它.';
$_lang['SETS_SAVED_SUCC'] = '設定已成功儲存';
$_lang['ACTIONS'] = '動作';
$_lang['BAN_IP_REQ_DEF'] = '要阻擋 IP 位址, 此至少需要啟動一個選項在 Elxis 防禦者!';
$_lang['BAN_YOURSELF'] = '確定您要阻檔您自己?';
$_lang['IP_AL_BANNED'] = '此 IP 已有阻檔!';
$_lang['IP_BANNED'] = 'IP 位置 %s 已阻檔!';
$_lang['BAN_FAILED_NOWRITE'] = '阻檔失敗! 檔案存放在/logs/defender_ban.php 不能被寫入.';
$_lang['ONLY_ADMINS_ACTION'] = '限管理者才能執行此動作!';
$_lang['CNOT_LOGOUT_ADMIN'] = '您不能將管理者登出!';
$_lang['USER_LOGGED_OUT'] = '此用戶已被登出!';
$_lang['SITE_STATISTICS'] = '網站統計';
$_lang['SITE_STATISTICS_INFO'] = '看網站流量統計';
$_lang['BACKUP'] = '備份';
$_lang['BACKUP_INFO'] = '做新的完整備份與管理既有備份.';
$_lang['BACKUP_FLIST'] = '已存在的備份檔列表';
$_lang['TYPE'] = '型式';
$_lang['FILENAME'] = '檔名';
$_lang['SIZE'] = '容量';
$_lang['NEW_DB_BACKUP'] = '新資料庫備份';
$_lang['NEW_FS_BACKUP'] = '新檔案系統備份';
$_lang['FILESYSTEM'] = '檔案系統';
$_lang['DOWNLOAD'] = '下載';
$_lang['TAKE_NEW_BACKUP'] = '要做新的備份?\n此將會佔用一點時間, 請耐心等待!';
$_lang['FOLDER_NOT_EXIST'] = "資料夾 %s 不存在!";
$_lang['FOLDER_NOT_WRITE'] = "資料夾 %s 不能被寫入!";
$_lang['BACKUP_SAVED_INTO'] = "備份檔案已存放在 %s";
$_lang['CACHE_SAVED_INTO'] = "快取檔案已存在 %s";
$_lang['CACHED_ITEMS'] = '快取項目';
$_lang['ELXIS_ROUTER'] = 'Elxis 路由器';
$_lang['ROUTING'] = '路由中';
$_lang['ROUTING_INFO'] = '重新路由用戶要求到自定義 URL 位址.';
$_lang['SOURCE'] = '來源';
$_lang['ROUTE_TO'] = '路由到';
$_lang['REROUTE'] = "重新路由 %s";
$_lang['DIRECTORY'] = '目錄';
$_lang['SET_FRONT_CONF'] = '設定網站前端在 Elxis 設定!';
$_lang['ADD_NEW_ROUTE'] = '新增路由';
$_lang['OTHER'] = '其它';
$_lang['LAST_MODIFIED'] = '最後更新';
$_lang['PERIOD'] = '期間'; //time period
$_lang['ERROR_LOG_DISABLED'] = '錯誤記錄已關閉!';
$_lang['LOG_ENABLE_ERR'] = '錯誤記錄啟動, 僅對嚴重記錄.';
$_lang['LOG_ENABLE_ERRWARN'] = '錯誤記錄啟動, 僅對錯誤與警告.';
$_lang['LOG_ENABLE_ERRWARNNTC'] = '錯誤記錄啟動, 僅對警告與通知.';
$_lang['LOGROT_ENABLED'] = '輪流記錄已啟動.';
$_lang['LOGROT_DISABLED'] = '輪流記錄已啟動!';
$_lang['SYSLOG_FILES'] = '系統記錄檔';
$_lang['DEFENDER_BANS'] = '防禦者禁止';
$_lang['LAST_DEFEND_NOTIF'] = '最後防禦者通知';
$_lang['LAST_ERROR_NOTIF'] = '最後錯誤通知';
$_lang['TIMES_BLOCKED'] = '時間已禁止';
$_lang['REFER_CODE'] = '參考碼';
$_lang['CLEAR_FILE'] = '清除檔案';
$_lang['CLEAR_FILE_WARN'] = '檔案內容將會被刪除. 繼續?';
$_lang['FILE_NOT_FOUND'] = '檔案沒找到!';
$_lang['FILE_CNOT_DELETE'] = '此檔案不能被刪除!';
$_lang['ONLY_LOG_DOWNLOAD'] = '只有副檔名(擴展名) .log 可以下載!';
$_lang['SYSTEM'] = '系統';
$_lang['PHP_INFO'] = 'PHP 版本';
$_lang['PHP_VERSION'] = 'PHP 版本';
$_lang['ELXIS_INFO'] = 'Elxis 資訊';
$_lang['VERSION'] = '版本';
$_lang['REVISION_NUMBER'] = '修正號';
$_lang['STATUS'] = '狀態';
$_lang['CODENAME'] = '代號';
$_lang['RELEASE_DATE'] = '發佈日期';
$_lang['COPYRIGHT'] = '版權';
$_lang['POWERED_BY'] = '撰寫';
$_lang['AUTHOR'] = '作者';
$_lang['PLATFORM'] = '平台';
$_lang['HEADQUARTERS'] = '總部';
$_lang['ELXIS_ENVIROMENT'] = 'Elxis 環境';
$_lang['DEFENDER_LOGS'] = '防禦者記錄';
$_lang['ADMIN_FOLDER'] = '管理目錄';
$_lang['DEF_NAME_RENAME'] = '預設名稱, 請改名!';
$_lang['INSTALL_PATH'] = '安裝路徑';
$_lang['IS_PUBLIC'] = '屬於公開!';
$_lang['CREDITS'] = '功勞';
$_lang['LOCATION'] = '所在位置';
$_lang['CONTRIBUTION'] = '貢獻';
$_lang['LICENSE'] = '許可';
$_lang['MULTISITES'] = '多網站';
$_lang['MULTISITES_DESC'] = '在一個 Elxis 安裝裡, 即可管理多網站.';
$_lang['MULTISITES_WARN'] = '您可以在一個 Elxis 安裝裡有多個網站. 運行在多網站需要進階的 Elxis CMS 知識. 在您匯入資料到新的多網站時, 請確認資料庫已有存在. 
	建立新多網站後請依照指示編輯 htaccess 檔. 刪除多網站時請不要刪除已連結的資料庫. 如需協助請詢問已有經驗的技術人員.';
$_lang['MULTISITES_DISABLED'] = '多網站已關閉!';
$_lang['ENABLE'] = '啟動';
$_lang['ACTIVE'] = '已啟動';
$_lang['URL_ID'] = 'URL 識別號';
$_lang['MAN_MULTISITES_ONLY'] = "您可管理多網站僅需從網站 %s";
$_lang['LOWER_ALPHANUM'] = '小寫字母元無需空白';
$_lang['IMPORT_DATA'] = '匯入資料';
$_lang['CNOT_CREATE_CFG_NEW'] = "不能建立設定檔案 %s 從新網站!";
$_lang['DATA_IMPORT_FAILED'] = '資料匯入失敗!';
$_lang['DATA_IMPORT_SUC'] = '資料已匯入成功!';
$_lang['ADD_RULES_HTACCESS'] = '加入下列規則在 htaccess 檔案';
$_lang['CREATE_REPOSITORY_NOTE'] = '此強烈建議去建在各別的子網站的存放地!';
$_lang['NOT_SUP_DBTYPE'] = '不支持的資料型式!';
$_lang['DBTYPES_MUST_SAME'] = '此網站與新網站的資料型式必需一致!';
$_lang['DISABLE_MULTISITES'] = '關閉多網站';
$_lang['DISABLE_MULTISITES_WARN'] = '除了 id 1之外, 所有網站會被移除!';
$_lang['VISITS_PER_DAY'] = "每天訪問 %s"; //translators help: ... for {MONTH YEAR}
$_lang['CLICKS_PER_DAY'] = "每天點擊 %s"; //translators help: ... for {MONTH YEAR}
$_lang['VISITS_PER_MONTH'] = "每月訪問 %s"; //translators help: ... for {YEAR}
$_lang['CLICKS_PER_MONTH'] = "每月點擊 %s"; //translators help: ... for {YEAR}
$_lang['LANGS_USAGE_FOR'] = "語系使用百分比 %s"; //translators help: ... for {MONTH YEAR}
$_lang['UNIQUE_VISITS'] = '單一訪問';
$_lang['PAGE_VIEWS'] = '閱讀頁面';
$_lang['TOTAL_VISITS'] = '所有訪問';
$_lang['TOTAL_PAGE_VIEWS'] = '所有頁面';
$_lang['LANGS_USAGE'] = '語系使用狀況';
$_lang['LEGEND'] = '圖例';
$_lang['USAGE'] = '使用';
$_lang['VIEWS'] = '觀看';
$_lang['OTHER'] = '其他';
$_lang['NO_DATA_AVAIL'] = '無可供資料';
$_lang['PERIOD'] = '期間';
$_lang['YEAR_STATS'] = '年度統計';
$_lang['MONTH_STATS'] = '年統計';
$_lang['PREVIOUS_YEAR'] = '上一年';
$_lang['NEXT_YEAR'] = '下一年';
$_lang['STATS_COL_DISABLED'] = '統計資料彙整已關閉! 啟動統計在 Elxis 設定裡.';
$_lang['DOCTYPE'] = '文件型式';
$_lang['DOCTYPE_INFO'] = '推薦選擇 HTML5. Elxis 將會產生為 XHTML 輸出方式, 即使您設 DOCTYPE 到 HTML5. 
在 XHTML 型式, Elxis 將文件及 application/xhtml+xml mime 型式在最新的瀏覽器與 text/html 放在更舊的內容.';
$_lang['ABR_SECONDS'] = '秒';
$_lang['ABR_MINUTES'] = '分';
$_lang['HOUR'] = '小時';
$_lang['HOURS'] = '小時';
$_lang['DAY'] = '天';
$_lang['DAYS'] = '天';
$_lang['UPDATED_BEFORE'] = '之前已更新';
$_lang['CACHE_INFO'] = '瀏覽與刪除項目已存在快取裡.';
$_lang['ELXISDC'] = 'Elxis 下載中心';
$_lang['ELXISDC_INFO'] = '瀏覽現有 EDC 與可用的插件';
$_lang['SITE_LANGS'] = '網站語系';
$_lang['SITE_LANGS_DESC'] = '預設安裝所有可用語系在前端網站, 您可選擇如下語系只顯示在前端網頁.';
//Elxis 4.1
$_lang['PERFORMANCE'] = '效能';
$_lang['MINIFIER_CSSJS'] = '較少的 CSS/Javascript';
$_lang['MINIFIER_INFO'] = 'Elxis 可統一將個別本地端 CSS 與 JS 檔案, 同時選擇性的壓縮它們. 統一的檔案將存在快取裡. 
所以取代已有多個的 CSS/JS 檔案在您的頁面檔頭，讓您只有需要較少的檔案.';
$_lang['MOBILE_VERSION'] = '移動版本';
$_lang['MOBILE_VERSION_DESC'] = '啟動友善移動版本來顯示在移動端?';
//Elxis 4.2
$_lang['SEND_TEST_EMAIL'] = '傳送測試郵件';
$_lang['ONLINE_USERS'] = '給線上用戶';
$_lang['CRONJOBS'] = '排程(Cron jobs)';
$_lang['CRONJOBS_INFO'] = '啟動排程, 如果您想自動執行任務, 像是排時程讓文章發佈.';
$_lang['LANG_DETECTION'] = '語系偵測';
$_lang['LANG_DETECTION_INFO'] = '在第一次訪問網頁時, 偵測原始語系與重導到正確網站語系版本.';
//Elxis 4.4
$_lang['DEFENDER_NOTIFS'] = '防禦器通知';
$_lang['XFRAMEOPT_HELP'] = '如果瀏覽器會接受或拒絕顯示在這網站內頁面的框架(frame),會由 HTTP 檔頭控制. 可避免點閱綁架(clickjacking)攻擊.';
$_lang['ACCEPT_XFRAME'] = '接受 X-Frame';
$_lang['DENY'] = '拒絕';
$_lang['SAMEORIGIN'] = '與來源相同';
$_lang['ALLOW_FROM'] = '允許從';
$_lang['ALLOW_FROM_ORIGIN'] = '允許從來源';
$_lang['CONTENT_SEC_POLICY'] = '內文安全原則';
$_lang['IP_RANGES'] = 'IP 範圍';
$_lang['UPDATED_AUTO'] = '自動更新';
$_lang['CHECK_IP_MOMENT'] = '檢查IP時刻';
$_lang['BEFORE_LOAD_ELXIS'] = '載入 Elxis 核心前';
$_lang['AFTER_LOAD_ELXIS'] = '載入 Elxis 核心後';
$_lang['CHECK_IP_MOMENT_HELP'] = '前: 防禦器檢查每個點擊的 IP. 有問題的 IPs 不會侵入到 Elxis 核心. 
	後: 防禦器檢查每個對話(session)的 IPs (有助效能改善). 有問題的 IPs 在侵入 Elxis 核心前會被封鎖.';
$_lang['SECURITY'] = '安全';
$_lang['EVERYTHING'] = '一切';
$_lang['ONLY_ATTACKS'] = '只有攻擊';
$_lang['CRONJOBS_PROB'] = 'Cron jobs 概率';
$_lang['CRONJOBS_PROB_INFO'] = '執行 Cron jobs 的百分比概率會在 Cron jobs 對每個用戶的點擊. 只有在 Elxis 內部執行 cron jobs 有影響. 最好的效能是站上有很多的參訪者時, 值應該要較低. 預設值是 10%.';
$_lang['EXTERNAL'] = '外部';
$_lang['SEO_TITLES_MATCH'] = '符合 SEO 標題';
$_lang['SEO_TITLES_MATCH_HELP'] = '從一般的標題來控制 SEO 標題的產生. 精確建立 SEO 標題,會符合原先翻譯的標題.';
$_lang['EXACT'] = '精確';
//Elxis 4.6
$_lang['CONFIG_FOR_GMAIL'] = 'Gmail的配置';
$_lang['AUTH_METHOD'] = '認證方式';
$_lang['DEFAULT'] = '預設';
$_lang['BACKUP_EXCLUDE_PATHS_HELP'] = '你可以從檔案系統備份程序排除一些資料夾, . 這是非常管用, 假使你有一個很大的檔案系統或記憶體的大小設定導致備份失敗時. 
	提供以下以相對路徑來設定是你想要排除的資料夾. 如: media/videos/';
$_lang['PATHS_EXCLUDED_FSBK'] = '從檔案系統已排除路徑';
$_lang['EXCLUSIONS'] = '排除';
//Elxis 5.0
$_lang['BACKUP_FOLDER_TABLE_TIP'] = '針對檔案系統備份, 你可以選擇備份整個 Elxis 的安裝內容或指定資料夾. 
	針對資料庫, 你可以備份整個資料庫或指定表格. 這是避免執行備份時,超出時間或記憶體錯誤 (特別是大型或檔案很多的網站) ,所以可以各別選擇要備份的檔案或表格來取而代之.';
$_lang['FOLDER'] = '資料夾';
$_lang['TABLE'] = '表格';
$_lang['INACTIVE'] = '未啟用';
$_lang['DEPRECATED'] = '已棄用';
$_lang['ALL_AVAILABLE'] = '所有可用語系';//translators help: "All available languages"
$_lang['NO_PROTECTION'] = '沒有保護';//translators help: No Elxis Defender filters enabled
$_lang['NEWER_VERSION_FOR'] = '這裡有較新的版本 (%s) 針對 %s';
$_lang['NEWER_VERSIONS_FOR'] = '對於 %s 有較新的版本';
$_lang['NEWER_VERSIONS_FOR_EXTS'] = '對於 %s 有較新的外掛版本';
$_lang['OUTDATED_ELXIS_UPDATE_TO'] = '你現在使用的是過期的 Elxis 版本! 建議最好更新到 %s';
$_lang['NO_BACKUPS'] = '你還沒有備份!';
$_lang['LONGTIME_TAKE_BACKUP'] = '你已經很久沒有做網站備份';
$_lang['DELETE_OLD_LOGS'] = '刪除舊的日誌log 檔案';
$_lang['DEFENDER_IS_DISABLED'] = 'Elxis 防護已取消';
$_lang['REPO_DEF_PATH'] = '貯藏處(Repository)的預設路徑';
$_lang['CHANGE_MAIL_TO_SMTP'] = '更改設定 PHP 郵件到 SMTP 或其他方式';
$_lang['DISABLE_MULTILINGUISM'] = '不啟用多國語言';
$_lang['ENABLE_MULTILINGUISM'] = '啟用多國語言';
//Elxis 5.2
$_lang['NOTFOUND'] = '未找到';
$_lang['EXTENSION'] = '延期';
$_lang['CODE_EDITOR_WARN'] = 'We strongly recommend not to modify extensions\'s files because you will lose your changes after an update. 
	Add your custom or overwrite CSS rules on <strong>user.config</strong> files instead.';
$_lang['EDIT_CODE'] = '修改代碼';
$_lang['EXCLUDED_IPS'] = '排除的 IP';

?>