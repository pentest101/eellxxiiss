<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: zt-CN (Chinese traditional - Taiwan) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Chong-Bing Liu ( http://easyapps.biz )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$locale = array('zh_TW.UTF-8', 'zh_TW.utf8', 'zh_CN.UTF-8', 'zh_CN.utf8', 'en_GB.utf8', 'en_GB.UTF-8', 'en_GB', 'en', 'english', 'england'); //utf-8 locales array

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
$_lang['JANUARY'] = '一月';
$_lang['FEBRUARY'] = '二月';
$_lang['MARCH'] = '三月';
$_lang['APRIL'] = '四月';
$_lang['MAY'] = '五月';
$_lang['JUNE'] = '六月';
$_lang['JULY'] = '七月';
$_lang['AUGUST'] = '八月';
$_lang['SEPTEMBER'] = '九月';
$_lang['OCTOBER'] = '十月';
$_lang['NOVEMBER'] = '十一月';
$_lang['DECEMBER'] = '十二月';
$_lang['JANUARY_SHORT'] = '一月';
$_lang['FEBRUARY_SHORT'] = '二月';
$_lang['MARCH_SHORT'] = '三月';
$_lang['APRIL_SHORT'] = '四月';
$_lang['MAY_SHORT'] = '五月';
$_lang['JUNE_SHORT'] = '六月';
$_lang['JULY_SHORT'] = '七月';
$_lang['AUGUST_SHORT'] = '八月';
$_lang['SEPTEMBER_SHORT'] = '九月';
$_lang['OCTOBER_SHORT'] = '十月';
$_lang['NOVEMBER_SHORT'] = '十一月';
$_lang['DECEMBER_SHORT'] = '十二月';
//day names
$_lang['MONDAY'] = '星期一';
$_lang['THUESDAY'] = '星期二';
$_lang['WEDNESDAY'] = '星期三';
$_lang['THURSDAY'] = '星期四';
$_lang['FRIDAY'] = '星期五';
$_lang['SATURDAY'] = '星期六';
$_lang['SUNDAY'] = '星期日';
$_lang['MONDAY_SHORT'] = '一';
$_lang['THUESDAY_SHORT'] = '二';
$_lang['WEDNESDAY_SHORT'] = '三';
$_lang['THURSDAY_SHORT'] = '四';
$_lang['FRIDAY_SHORT'] = '五';
$_lang['SATURDAY_SHORT'] = '六';
$_lang['SUNDAY_SHORT'] = '七';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Elxis 效能監視';
$_lang['ITEM'] = '項目';
$_lang['INIT_FILE'] = '初始檔案';
$_lang['EXEC_TIME'] = '執行時間';
$_lang['DB_QUERIES'] = '資料庫查詢(DB queries)';
$_lang['ERRORS'] = '錯誤';
$_lang['SIZE'] = '大小';
$_lang['ENTRIES'] = '進入點';

/* general */
$_lang['HOME'] = '首頁';
$_lang['YOU_ARE_HERE'] = '您在這';
$_lang['CATEGORY'] = '分類';
$_lang['DESCRIPTION'] = '描述';
$_lang['FILE'] = '檔案';
$_lang['IMAGE'] = '圖片';
$_lang['IMAGES'] = '所有圖片';
$_lang['CONTENT'] = '內文';
$_lang['DATE'] = '日期';
$_lang['YES'] = '是';
$_lang['NO'] = '不';
$_lang['NONE'] = '無';
$_lang['SELECT'] = '選擇';
$_lang['LOGIN'] = '登入';
$_lang['LOGOUT'] = '登出';
$_lang['WEBSITE'] = '網站';
$_lang['SECURITY_CODE'] = '安全碼';
$_lang['RESET'] = '重設';
$_lang['SUBMIT'] = '送出';
$_lang['REQFIELDEMPTY'] = '一或多個欄位是空白!';
$_lang['FIELDNOEMPTY'] = "%s 不能是空白!";
$_lang['FIELDNOACCCHAR'] = "%s 含不能接受的字元!";
$_lang['INVALID_DATE'] = '無效日期!';
$_lang['INVALID_NUMBER'] = '無效數字!';
$_lang['INVALID_URL'] = '無效 URL 位址!';
$_lang['FIELDSASTERREQ'] = '欄位有星 * 號是需要必須.';
$_lang['ERROR'] = '錯誤';
$_lang['REGARDS'] = '關於';
$_lang['NOREPLYMSGINFO'] = '請不要回覆此訊息, 此僅是訊息目地送出.';
$_lang['LANGUAGE'] = '語系';
$_lang['PAGE'] = '頁';
$_lang['PAGEOF'] = "第 %s 共 %s";
$_lang['OF'] = '的';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "顯示 %s 到o %s 的 %s 項目";
$_lang['HITS'] = '點擊';
$_lang['PRINT'] = '列印';
$_lang['BACK'] = '回上頁';
$_lang['PREVIOUS'] = '上一個';
$_lang['NEXT'] = '下一個';
$_lang['CLOSE'] = '關閉';
$_lang['CLOSE_WINDOW'] = '關閉視窗';
$_lang['COMMENTS'] = '所有評論';
$_lang['COMMENT'] = '評論';
$_lang['PUBLISH'] = '發佈';
$_lang['DELETE'] = '刪除';
$_lang['EDIT'] = '編輯';
$_lang['COPY'] = '複製';
$_lang['SEARCH'] = '搜尋';
$_lang['PLEASE_WAIT'] = '請稍待...';
$_lang['ANY'] = '任何';
$_lang['NEW'] = '新';
$_lang['ADD'] = '增加';
$_lang['VIEW'] = '閱讀';
$_lang['MENU'] = '選單';
$_lang['HELP'] = '輔助';
$_lang['TOP'] = '上';
$_lang['BOTTOM'] = '底';
$_lang['LEFT'] = '左';
$_lang['RIGHT'] = '右';
$_lang['CENTER'] = '中心';

/* xml */
$_lang['CACHE'] = '快取';
$_lang['ENABLE_CACHE_D'] = '要對此項作快取?';
$_lang['YES_FOR_VISITORS'] = '是, 僅對訪客';
$_lang['YES_FOR_ALL'] = '是, 全部';
$_lang['CACHE_LIFETIME'] = '快取時間';
$_lang['CACHE_LIFETIME_D'] = '時間, 以分鐘, 直到快取對此項目再次更新.';
$_lang['NO_PARAMS'] = '沒有參數!';
$_lang['STYLE'] = '樣式';
$_lang['ADVANCED_SETTINGS'] = '進階設定';
$_lang['CSS_SUFFIX'] = 'CSS 字尾';
$_lang['CSS_SUFFIX_D'] = '字尾會被加在模組 CSS 類別(class).';
$_lang['MENU_TYPE'] = '選單型式';
$_lang['ORIENTATION'] = '方向';
$_lang['SHOW'] = '顯示';
$_lang['HIDE'] = '隱藏';
$_lang['GLOBAL_SETTING'] = '全域設定';

/* users & authentication */
$_lang['USERNAME'] = '帳號';
$_lang['PASSWORD'] = '密碼';
$_lang['NOAUTHMETHODS'] = '還沒設定認証方式';
$_lang['AUTHMETHNOTEN'] = '認証方式 %s 還沒啟動';
$_lang['PASSTOOSHORT'] = '您的密碼太短以致無法接受';
$_lang['USERNOTFOUND'] = '帳號沒找到';
$_lang['INVALIDUNAME'] = '無效帳號';
$_lang['INVALIDPASS'] = '無效密碼';
$_lang['AUTHFAILED'] = '認証失敗';
$_lang['YACCBLOCKED'] = '您的帳號已被鎖住';
$_lang['YACCEXPIRED'] = '您的帳號已過期';
$_lang['INVUSERGROUP'] = '無效用戶群組';
$_lang['NAME'] = '名稱(暱稱)';
$_lang['FIRSTNAME'] = '名字';
$_lang['LASTNAME'] = '姓氏';
$_lang['EMAIL'] = '郵件(E-mail)';
$_lang['INVALIDEMAIL'] = '無效郵件位址';
$_lang['ADMINISTRATOR'] = '管理員';
$_lang['GUEST'] = '訪客';
$_lang['EXTERNALUSER'] = '外部用戶';
$_lang['USER'] = '用戶';
$_lang['GROUP'] = '群組';
$_lang['NOTALLOWACCPAGE'] = '您尚未允許存取此頁!';
$_lang['NOTALLOWACCITEM'] = '您尚未允許存取此項目!';
$_lang['NOTALLOWMANITEM'] = '您尚未允許管理此項目!';
$_lang['NOTALLOWACTION'] = '您尚未允許執行此動作!';
$_lang['NEED_HIGHER_ACCESS'] = '執行此動作需要更高權限!';
$_lang['AREYOUSURE'] = '您確定?';

/* highslide */
$_lang['LOADING'] = '載入中...';
$_lang['CLICK_CANCEL'] = '點擊取消';
$_lang['MOVE'] = '移動';
$_lang['PLAY'] = '播放';
$_lang['PAUSE'] = '暫停';
$_lang['RESIZE'] = '調整';

/* admin */
$_lang['ADMINISTRATION'] = '管理';
$_lang['SETTINGS'] = '設定';
$_lang['DATABASE'] = '資料庫';
$_lang['ON'] = '開';
$_lang['OFF'] = '關';
$_lang['WARNING'] = '警告';
$_lang['SAVE'] = '儲存';
$_lang['APPLY'] = '採用';
$_lang['CANCEL'] = '取消';
$_lang['LIMIT'] = '限制';
$_lang['ORDERING'] = '排序';
$_lang['NO_RESULTS'] = '結果沒找到!';
$_lang['CONNECT_ERROR'] = '連線錯誤';
$_lang['DELETE_SEL_ITEMS'] = '刪除已選擇項目?';
$_lang['TOGGLE_SELECTED'] = '選擇切換';
$_lang['NO_ITEMS_SELECTED'] = '沒有已選項目!';
$_lang['ID'] = '編號(Id)';
$_lang['ACTION_FAILED'] = '動作失敗!';
$_lang['ACTION_SUCCESS'] = '動作成功地完成!';
$_lang['NO_IMAGE_UPLOADED'] = '沒有圖片已上傳';
$_lang['NO_FILE_UPLOADED'] = '沒有檔案已上傳';
$_lang['MODULES'] = '所有模組';
$_lang['COMPONENTS'] = '所有元件';
$_lang['TEMPLATES'] = '樣版';
$_lang['SEARCH_ENGINES'] = '搜尋引擎';
$_lang['AUTH_METHODS'] = '認証方式';
$_lang['CONTENT_PLUGINS'] = '內容插件';
$_lang['PLUGINS'] = '所有插件';
$_lang['PUBLISHED'] = '已發佈';
$_lang['ACCESS'] = '取存';
$_lang['ACCESS_LEVEL'] = '取存層級';
$_lang['TITLE'] = '標題';
$_lang['MOVE_UP'] = '往上移動';
$_lang['MOVE_DOWN'] = '往下移動';
$_lang['WIDTH'] = '寬度';
$_lang['HEIGHT'] = '高度';
$_lang['ITEM_SAVED'] = '項目已儲存';
$_lang['FIRST'] = '第一';
$_lang['LAST'] = '最後';
$_lang['SUGGESTED'] = '已建議';
$_lang['VALIDATE'] = '有效';
$_lang['NEVER'] = '從未';
$_lang['ALL'] = '所有';
$_lang['ALL_GROUPS_LEVEL'] = "所有層級群組 %s";
$_lang['REQDROPPEDSEC'] = '因安全因素您的要求被中斷. 請再試一次.';
$_lang['PROVIDE_TRANS'] = '請提供本地化翻譯!';
$_lang['AUTO_TRANS'] = '自動翻譯';
$_lang['STATISTICS'] = '統計';
$_lang['UPLOAD'] = '上傳';
$_lang['MORE'] = '更多';
//Elxis 4.2
$_lang['TRANSLATIONS'] = '所有翻譯';
$_lang['CHECK_UPDATES'] = '檢查更新';
$_lang['TODAY'] = '今天';
$_lang['YESTERDAY'] = '昨天';
//Elxis 4.3
$_lang['PUBLISH_ON'] = '發佈在';
$_lang['UNPUBLISHED'] = '未發佈';
$_lang['UNPUBLISH_ON'] = '未發佈在';
$_lang['SCHEDULED_CRON_DIS'] = '已有 %s 排程項目但 Cron Jobs 已被關閉!';
$_lang['CRON_DISABLED'] = 'Cron Jobs 已被關閉!';
$_lang['ARCHIVE'] = '歸檔';
$_lang['RUN_NOW'] = '現在執行';
$_lang['LAST_RUN'] = '最後執行';
$_lang['SEC_AGO'] = '%s 秒以前.';
$_lang['MIN_SEC_AGO'] = '%s 分 與 %s 秒以前.';
$_lang['HOUR_MIN_AGO'] = '1 小時 與 %s 分以前.';
$_lang['HOURS_MIN_AGO'] = '%s 小時 與 %s 分以前.';
$_lang['CLICK_TOGGLE_STATUS'] = '點擊切換狀態';
//Elxis 4.5
$_lang['IAMNOTA_ROBOT'] = '我不是機器人';
$_lang['VERIFY_NOROBOT'] = '請確認您不是機器人！';
$_lang['CHECK_FS'] = '檢查文件';
//Elxis 5.0
$_lang['TOTAL_ITEMS'] = '%s 項目';
$_lang['SEARCH_OPTIONS'] = '搜尋選項';
$_lang['FILTERS_HAVE_APPLIED'] = '已應用到過濾選項';
$_lang['FILTER_BY_ITEM'] = '由此項目來過濾';
$_lang['REMOVE_FILTER'] = '移除過濾選項';
$_lang['TOTAL'] = '全部';
$_lang['OPTIONS'] = '選項';
$_lang['DISABLE'] = '禁用';
$_lang['REMOVE'] = '移除';
$_lang['ADD_ALL'] = '全部加入';
$_lang['TOMORROW'] = '明天';
$_lang['NOW'] = '現在';
$_lang['MIN_AGO'] = '1 分鐘前';
$_lang['MINS_AGO'] = '%s 分鐘前';
$_lang['HOUR_AGO'] = '1 小時前';
$_lang['HOURS_AGO'] = '%s 小時前';
$_lang['IN_SEC'] = 'In %s 秒';
$_lang['IN_MINUTE'] = '1分鐘內';
$_lang['IN_MINUTES'] = '在 %s 分鐘內';
$_lang['IN_HOUR'] = '在 1 小時內';
$_lang['IN_HOURS'] = '在 %s 小時內';
$_lang['OTHER'] = '其他';
$_lang['DELETE_CURRENT_IMAGE'] = '刪除目前圖像';
$_lang['NO_IMAGE_FILE'] = '沒有任何圖像!';
$_lang['SELECT_FILE'] = '選擇檔案';

?>