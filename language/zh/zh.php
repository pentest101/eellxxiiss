<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: zt-CN (Chinese simplified - China) language for Elxis CMS
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
$_lang['ELX_PERF_MONITOR'] = 'Elxis 效能监视';
$_lang['ITEM'] = '项目';
$_lang['INIT_FILE'] = '初始档案';
$_lang['EXEC_TIME'] = '执行时间';
$_lang['DB_QUERIES'] = '资料库查询(DB queries)';
$_lang['ERRORS'] = '错误';
$_lang['SIZE'] = '大小';
$_lang['ENTRIES'] = '进入点';

/* general */
$_lang['HOME'] = '首页';
$_lang['YOU_ARE_HERE'] = '您在这';
$_lang['CATEGORY'] = '分类';
$_lang['DESCRIPTION'] = '描述';
$_lang['FILE'] = '档案';
$_lang['IMAGE'] = '图片';
$_lang['IMAGES'] = '所有图片';
$_lang['CONTENT'] = '内文';
$_lang['DATE'] = '日期';
$_lang['YES'] = '是';
$_lang['NO'] = '不';
$_lang['NONE'] = '无';
$_lang['SELECT'] = '选择';
$_lang['LOGIN'] = '登入';
$_lang['LOGOUT'] = '登出';
$_lang['WEBSITE'] = '网站';
$_lang['SECURITY_CODE'] = '安全码';
$_lang['RESET'] = '重设';
$_lang['SUBMIT'] = '送出';
$_lang['REQFIELDEMPTY'] = '一或多个栏位是空白!';
$_lang['FIELDNOEMPTY'] = "%s 不能是空白!";
$_lang['FIELDNOACCCHAR'] = "%s 含不能接受的字元!";
$_lang['INVALID_DATE'] = '无效日期!';
$_lang['INVALID_NUMBER'] = '无效数字!';
$_lang['INVALID_URL'] = '无效 URL 位址!';
$_lang['FIELDSASTERREQ'] = '栏位有星 * 号是需要必须.';
$_lang['ERROR'] = '错误';
$_lang['REGARDS'] = '关于';
$_lang['NOREPLYMSGINFO'] = '请不要回覆此讯息, 此仅是讯息目地送出.';
$_lang['LANGUAGE'] = '语系';
$_lang['PAGE'] = '页';
$_lang['PAGEOF'] = "第 %s 共 %s";
$_lang['OF'] = '的';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "显示 %s 到o %s 的 %s 项目";
$_lang['HITS'] = '点击';
$_lang['PRINT'] = '列印';
$_lang['BACK'] = '回上页';
$_lang['PREVIOUS'] = '上一个';
$_lang['NEXT'] = '下一个';
$_lang['CLOSE'] = '关闭';
$_lang['CLOSE_WINDOW'] = '关闭视窗';
$_lang['COMMENTS'] = '所有评论';
$_lang['COMMENT'] = '评论';
$_lang['PUBLISH'] = '发布';
$_lang['DELETE'] = '删除';
$_lang['EDIT'] = '编辑';
$_lang['COPY'] = '复制';
$_lang['SEARCH'] = '搜寻';
$_lang['PLEASE_WAIT'] = '请稍待...';
$_lang['ANY'] = '任何';
$_lang['NEW'] = '新';
$_lang['ADD'] = '增加';
$_lang['VIEW'] = '阅读';
$_lang['MENU'] = '选单';
$_lang['HELP'] = '辅助';
$_lang['TOP'] = '上';
$_lang['BOTTOM'] = '底';
$_lang['LEFT'] = '左';
$_lang['RIGHT'] = '右';
$_lang['CENTER'] = '中心';

/* xml */
$_lang['CACHE'] = '快取';
$_lang['ENABLE_CACHE_D'] = '要对此项作快取?';
$_lang['YES_FOR_VISITORS'] = '是, 仅对访客';
$_lang['YES_FOR_ALL'] = '是, 全部';
$_lang['CACHE_LIFETIME'] = '快取时间';
$_lang['CACHE_LIFETIME_D'] = '时间, 以分钟, 直到快取对此项目再次更新.';
$_lang['NO_PARAMS'] = '没有参数!';
$_lang['STYLE'] = '样式';
$_lang['ADVANCED_SETTINGS'] = '进阶设定';
$_lang['CSS_SUFFIX'] = 'CSS 字尾';
$_lang['CSS_SUFFIX_D'] = '字尾会被加在模组 CSS 类别(class).';
$_lang['MENU_TYPE'] = '选单型式';
$_lang['ORIENTATION'] = '方向';
$_lang['SHOW'] = '显示';
$_lang['HIDE'] = '隐藏';
$_lang['GLOBAL_SETTING'] = '全域设定';

/* users & authentication */
$_lang['USERNAME'] = '帐号';
$_lang['PASSWORD'] = '密码';
$_lang['NOAUTHMETHODS'] = '还没设定认证方式';
$_lang['AUTHMETHNOTEN'] = '认证方式 %s 还没启动';
$_lang['PASSTOOSHORT'] = '您的密码太短以致无法接受';
$_lang['USERNOTFOUND'] = '帐号没找到';
$_lang['INVALIDUNAME'] = '无效帐号';
$_lang['INVALIDPASS'] = '无效密码';
$_lang['AUTHFAILED'] = '认证失败';
$_lang['YACCBLOCKED'] = '您的帐号已被锁住';
$_lang['YACCEXPIRED'] = '您的帐号已过期';
$_lang['INVUSERGROUP'] = '无效用户群组';
$_lang['NAME'] = '名称(昵称)';
$_lang['FIRSTNAME'] = '名字';
$_lang['LASTNAME'] = '姓氏';
$_lang['EMAIL'] = '邮件(E-mail)';
$_lang['INVALIDEMAIL'] = '无效邮件位址';
$_lang['ADMINISTRATOR'] = '管理员';
$_lang['GUEST'] = '访客';
$_lang['EXTERNALUSER'] = '外部用户';
$_lang['USER'] = '用户';
$_lang['GROUP'] = '群组';
$_lang['NOTALLOWACCPAGE'] = '您尚未允许存取此页!';
$_lang['NOTALLOWACCITEM'] = '您尚未允许存取此项目!';
$_lang['NOTALLOWMANITEM'] = '您尚未允许管理此项目!';
$_lang['NOTALLOWACTION'] = '您尚未允许执行此动作!';
$_lang['NEED_HIGHER_ACCESS'] = '执行此动作需要更高权限!';
$_lang['AREYOUSURE'] = '您确定?';

/* highslide */
$_lang['LOADING'] = '载入中...';
$_lang['CLICK_CANCEL'] = '点击取消';
$_lang['MOVE'] = '移动';
$_lang['PLAY'] = '播放';
$_lang['PAUSE'] = '暂停';
$_lang['RESIZE'] = '调整';

/* admin */
$_lang['ADMINISTRATION'] = '管理';
$_lang['SETTINGS'] = '设定';
$_lang['DATABASE'] = '资料库';
$_lang['ON'] = '开';
$_lang['OFF'] = '关';
$_lang['WARNING'] = '警告';
$_lang['SAVE'] = '储存';
$_lang['APPLY'] = '采用';
$_lang['CANCEL'] = '取消';
$_lang['LIMIT'] = '限制';
$_lang['ORDERING'] = '排序';
$_lang['NO_RESULTS'] = '结果没找到!';
$_lang['CONNECT_ERROR'] = '连线错误';
$_lang['DELETE_SEL_ITEMS'] = '删除已选择项目?';
$_lang['TOGGLE_SELECTED'] = '选择切换';
$_lang['NO_ITEMS_SELECTED'] = '没有已选项目!';
$_lang['ID'] = '编号(Id)';
$_lang['ACTION_FAILED'] = '动作失败!';
$_lang['ACTION_SUCCESS'] = '动作成功地完成!';
$_lang['NO_IMAGE_UPLOADED'] = '没有图片已上传';
$_lang['NO_FILE_UPLOADED'] = '没有档案已上传';
$_lang['MODULES'] = '所有模组';
$_lang['COMPONENTS'] = '所有元件';
$_lang['TEMPLATES'] = '样版';
$_lang['SEARCH_ENGINES'] = '搜寻引擎';
$_lang['AUTH_METHODS'] = '认证方式';
$_lang['CONTENT_PLUGINS'] = '内容插件';
$_lang['PLUGINS'] = '所有插件';
$_lang['PUBLISHED'] = '已发布';
$_lang['ACCESS'] = '取存';
$_lang['ACCESS_LEVEL'] = '取存层级';
$_lang['TITLE'] = '标题';
$_lang['MOVE_UP'] = '往上移动';
$_lang['MOVE_DOWN'] = '往下移动';
$_lang['WIDTH'] = '宽度';
$_lang['HEIGHT'] = '高度';
$_lang['ITEM_SAVED'] = '项目已储存';
$_lang['FIRST'] = '第一';
$_lang['LAST'] = '最后';
$_lang['SUGGESTED'] = '已建议';
$_lang['VALIDATE'] = '有效';
$_lang['NEVER'] = '从未';
$_lang['ALL'] = '所有';
$_lang['ALL_GROUPS_LEVEL'] = "所有层级群组 %s";
$_lang['REQDROPPEDSEC'] = '因安全因素您的要求被中断. 请再试一次.';
$_lang['PROVIDE_TRANS'] = '请提供本地化翻译!';
$_lang['AUTO_TRANS'] = '自动翻译';
$_lang['STATISTICS'] = '统计';
$_lang['UPLOAD'] = '上传';
$_lang['MORE'] = '更多';
//Elxis 4.2
$_lang['TRANSLATIONS'] = '所有翻译';
$_lang['CHECK_UPDATES'] = '检查更新';
$_lang['TODAY'] = '今天';
$_lang['YESTERDAY'] = '昨天';
//Elxis 4.3
$_lang['PUBLISH_ON'] = '发布在';
$_lang['UNPUBLISHED'] = '未发布';
$_lang['UNPUBLISH_ON'] = '未发布在';
$_lang['SCHEDULED_CRON_DIS'] = '已有 %s 排程项目但 Cron Jobs 已被关闭!';
$_lang['CRON_DISABLED'] = 'Cron Jobs 已被关闭!';
$_lang['ARCHIVE'] = '归档';
$_lang['RUN_NOW'] = '现在执行';
$_lang['LAST_RUN'] = '最后执行';
$_lang['SEC_AGO'] = '%s 秒以前.';
$_lang['MIN_SEC_AGO'] = '%s 分 与 %s 秒以前.';
$_lang['HOUR_MIN_AGO'] = '1 小时 与 %s 分以前.';
$_lang['HOURS_MIN_AGO'] = '%s 小时 与 %s 分以前.';
$_lang['CLICK_TOGGLE_STATUS'] = '点击切换状态';
//Elxis 4.5
$_lang['IAMNOTA_ROBOT'] = '我不是机器人';
$_lang['VERIFY_NOROBOT'] = '请确认您不是机器人！';
$_lang['CHECK_FS'] = '检查文件';
//Elxis 5.0
$_lang['TOTAL_ITEMS'] = '%s 项目';
$_lang['SEARCH_OPTIONS'] = '搜寻选项';
$_lang['FILTERS_HAVE_APPLIED'] = '已应用到过滤选项';
$_lang['FILTER_BY_ITEM'] = '由此项目来过滤';
$_lang['REMOVE_FILTER'] = '移除过滤选项';
$_lang['TOTAL'] = '全部';
$_lang['OPTIONS'] = '选项';
$_lang['DISABLE'] = '禁用';
$_lang['REMOVE'] = '移除';
$_lang['ADD_ALL'] = '全部加入';
$_lang['TOMORROW'] = '明天';
$_lang['NOW'] = '现在';
$_lang['MIN_AGO'] = '1 分钟前';
$_lang['MINS_AGO'] = '%s 分钟前';
$_lang['HOUR_AGO'] = '1 小时前';
$_lang['HOURS_AGO'] = '%s 小时前';
$_lang['IN_SEC'] = 'In %s 秒';
$_lang['IN_MINUTE'] = '1分钟内';
$_lang['IN_MINUTES'] = '在 %s 分钟内';
$_lang['IN_HOUR'] = '在 1 小时内';
$_lang['IN_HOURS'] = '在 %s 小时内';
$_lang['OTHER'] = '其他';
$_lang['DELETE_CURRENT_IMAGE'] = '删除目前图像';
$_lang['NO_IMAGE_FILE'] = '没有任何图像!';
$_lang['SELECT_FILE'] = '选择档案';

?>