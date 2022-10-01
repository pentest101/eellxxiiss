<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: uk-UA (Ukrainian - Ukrain) language for component CPanel
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Aleksandr Shevchenko ( http://pomogite.in.ua )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Доступ заборонено.');


$locale = array('uk_UK.utf8', 'uk_UK.UTF-8', 'uk_UK', 'uk', 'ukrainian', 'ukraine'); //utf-8 locales array

$_lang = array();
//Формати дат
$_lang['DATE_FORMAT_BOX'] = 'd-m-Y'; //формати, які підтримуються: d-m-Y, Y-m-d, d/m/Y, Y/m/d
$_lang['DATE_FORMAT_BOX_LONG'] = 'd-m-Y H:i:s'; //формати, які підтримуються: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
$_lang['DATE_FORMAT_1'] = "%d/%m/%Y"; //приклад: 20/06/2014
$_lang['DATE_FORMAT_2'] = "%b %d, %Y"; //приклад: Черв 20, 2014
$_lang['DATE_FORMAT_3'] = "%B %d, %Y"; //приклад: Червень 20, 2014
$_lang['DATE_FORMAT_4'] = "%b %d, %Y %H:%M"; //приклад: Черв 20, 2014 15:30
$_lang['DATE_FORMAT_5'] = "%B %d, %Y %H:%M"; //приклад: Червень 25, 2010 15:30
$_lang['DATE_FORMAT_6'] = "%B %d, %Y %H:%M:%S"; //приклад: Червень 20, 2014 15:30:45
$_lang['DATE_FORMAT_7'] = "%a %b %d, %Y"; //приклад: Сб Черв 20, 2014
$_lang['DATE_FORMAT_8'] = "%A %b %d, %Y"; //приклад: Субота Черв 20, 2014
$_lang['DATE_FORMAT_9'] = "%A %B %d, %Y"; //приклад: Субота Червень 20, 2014
$_lang['DATE_FORMAT_10'] = "%A %B %d, %Y %H:%M"; //приклад: Субота Червень 20, 2014 12:34
$_lang['DATE_FORMAT_11'] = "%A %B %d, %Y %H:%M:%S"; //приклад: Субота Червень 20, 2014 12:34:45
$_lang['DATE_FORMAT_12'] = "%a %B %d, %Y %H:%M"; //приклад: Сб Червень 20, 2014 12:34
$_lang['DATE_FORMAT_13'] = "%a %B %d, %Y %H:%M:%S"; //приклад: Сб Червень 20, 2014 12:34:45
$_lang['THOUSANDS_SEP'] = ' ';
$_lang['DECIMALS_SEP'] = '.';
//назви місяців
$_lang['JANUARY'] = 'Січень';
$_lang['FEBRUARY'] = 'Лютий';
$_lang['MARCH'] = 'Березень';
$_lang['APRIL'] = 'Квітень';
$_lang['MAY'] = 'Травень';
$_lang['JUNE'] = 'Червень';
$_lang['JULY'] = 'Липень';
$_lang['AUGUST'] = 'Серпень';
$_lang['SEPTEMBER'] = 'Вересень';
$_lang['OCTOBER'] = 'Жовтень';
$_lang['NOVEMBER'] = 'Листопад';
$_lang['DECEMBER'] = 'Грудень';
$_lang['JANUARY_SHORT'] = 'Січ';
$_lang['FEBRUARY_SHORT'] = 'Лют';
$_lang['MARCH_SHORT'] = 'Бер';
$_lang['APRIL_SHORT'] = 'Квіт';
$_lang['MAY_SHORT'] = 'Трав';
$_lang['JUNE_SHORT'] = 'Черв';
$_lang['JULY_SHORT'] = 'Лип';
$_lang['AUGUST_SHORT'] = 'Серп';
$_lang['SEPTEMBER_SHORT'] = 'Вер';
$_lang['OCTOBER_SHORT'] = 'Жовт';
$_lang['NOVEMBER_SHORT'] = 'Лист';
$_lang['DECEMBER_SHORT'] = 'Груд';
//назви днів
$_lang['MONDAY'] = 'Понеділок';
$_lang['THUESDAY'] = 'Вівторок';
$_lang['WEDNESDAY'] = 'Середа';
$_lang['THURSDAY'] = 'Четвер';
$_lang['FRIDAY'] = 'Пятниця';
$_lang['SATURDAY'] = 'Субота';
$_lang['SUNDAY'] = 'Неділя';
$_lang['MONDAY_SHORT'] = 'Пн';
$_lang['THUESDAY_SHORT'] = 'Вівт';
$_lang['WEDNESDAY_SHORT'] = 'Сер';
$_lang['THURSDAY_SHORT'] = 'Четв';
$_lang['FRIDAY_SHORT'] = 'Пт';
$_lang['SATURDAY_SHORT'] = 'Сб';
$_lang['SUNDAY_SHORT'] = 'Нед';
/* elxis монітор продуктивності */
$_lang['ELX_PERF_MONITOR'] = 'Elxis Монітор Продуктивності';
$_lang['ITEM'] = 'Об єкт';
$_lang['INIT_FILE'] = 'Файл ініціалізації';
$_lang['EXEC_TIME'] = 'Тривалість виконання';
$_lang['DB_QUERIES'] = 'Запити до БД';
$_lang['ERRORS'] = 'Помилки';
$_lang['SIZE'] = 'Розмір';
$_lang['ENTRIES'] = 'Записи';

/* Загальні */
$_lang['HOME'] = 'Головна';
$_lang['YOU_ARE_HERE'] = 'НАВІГАТОР: ';
$_lang['CATEGORY'] = 'Категорія';
$_lang['DESCRIPTION'] = 'Опис';
$_lang['FILE'] = 'Файл';
$_lang['IMAGE'] = 'Зображення';
$_lang['IMAGES'] = 'Зображення';
$_lang['CONTENT'] = 'Зміст';
$_lang['DATE'] = 'Дата';
$_lang['YES'] = 'Так';
$_lang['NO'] = 'Ні';
$_lang['NONE'] = 'Відсутні';
$_lang['SELECT'] = 'Обрати';
$_lang['LOGIN'] = 'Увійти';
$_lang['LOGOUT'] = 'Вихід';
$_lang['WEBSITE'] = 'Веб сайт';
$_lang['SECURITY_CODE'] = 'Код безпеки';
$_lang['RESET'] = 'Відновити';
$_lang['SUBMIT'] = 'Відправити/Виконати';
$_lang['REQFIELDEMPTY'] = 'Одне, чи декілька полів не заповнені!';
$_lang['FIELDNOEMPTY'] = "%s не може бути порожнім!";
$_lang['FIELDNOACCCHAR'] = "%s містить неприпустимі символи!";
$_lang['INVALID_DATE'] = 'Невірна дата!';
$_lang['INVALID_NUMBER'] = 'Невірне число!';
$_lang['INVALID_URL'] = 'Невірний URL!';
$_lang['FIELDSASTERREQ'] = 'Поля, позначені * обов язково повинні бути заповнені.';
$_lang['ERROR'] = 'Помилка';
$_lang['REGARDS'] = 'З повагою';
$_lang['NOREPLYMSGINFO'] = 'Будь ласка, не відповідайте на цей лист, він створений автоматично та відправлений тільки в інформаційних цілях.';
$_lang['LANGUAGE'] = 'Мова';
$_lang['PAGE'] = 'Сторінка';
$_lang['PAGEOF'] = "Сторінка %s від %s";
$_lang['OF'] = 'з';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Показано об єктів з %s по %s. Всього об єктів - %s";
$_lang['HITS'] = 'Хіти';
$_lang['PRINT'] = 'Друк';
$_lang['BACK'] = 'Назад';
$_lang['PREVIOUS'] = 'Попередній';
$_lang['NEXT'] = 'Наступний';
$_lang['CLOSE'] = 'Закрити';
$_lang['CLOSE_WINDOW'] = 'Закрити вікно';
$_lang['COMMENTS'] = 'Коментарі';
$_lang['COMMENT'] = 'Коментар';
$_lang['PUBLISH'] = 'Увімк/Вимк';
$_lang['DELETE'] = 'Видалити';
$_lang['EDIT'] = 'Редагувати';
$_lang['COPY'] = 'Копіювати';
$_lang['SEARCH'] = 'Пошук';
$_lang['PLEASE_WAIT'] = 'Будь ласка, зачекайте...';
$_lang['ANY'] = 'Усі';
$_lang['NEW'] = 'Новий';  // Новый
$_lang['ADD'] = 'Додати';
$_lang['VIEW'] = 'Перегляд';
$_lang['MENU'] = 'Меню';
$_lang['HELP'] = 'Допомога';
$_lang['TOP'] = 'Вгорі';
$_lang['BOTTOM'] = 'Знизу';
$_lang['LEFT'] = 'Ліворуч';
$_lang['RIGHT'] = 'Праворуч';
$_lang['CENTER'] = 'По центру';

/* xml */
$_lang['CACHE'] = 'Кеш';
$_lang['ENABLE_CACHE_D'] = 'Увімкнути кеш для цього об єкту?';
$_lang['YES_FOR_VISITORS'] = 'Так, для користувачів';
$_lang['YES_FOR_ALL'] = 'Так, для всіх';
$_lang['CACHE_LIFETIME'] = 'Тривалість кеш-пам яті';
$_lang['CACHE_LIFETIME_D'] = 'Час до оновлення кешу, у хвилинах.';
$_lang['NO_PARAMS'] = 'Відсутні параметри!';
$_lang['STYLE'] = 'Стиль';
$_lang['ADVANCED_SETTINGS'] = 'Розширені налаштування';
$_lang['CSS_SUFFIX'] = 'CSS суфікс';
$_lang['CSS_SUFFIX_D'] = 'Суфікс, який буде доданий до CSS класу модуля.';
$_lang['MENU_TYPE'] = 'Тип меню';
$_lang['ORIENTATION'] = 'Орієнтація';
$_lang['SHOW'] = 'Показувати';
$_lang['HIDE'] = 'Приховати';
$_lang['GLOBAL_SETTING'] = 'Загальні налаштування';

/* користувачі та реєстрація */
$_lang['USERNAME'] = 'Логін';
$_lang['PASSWORD'] = 'Пароль';
$_lang['NOAUTHMETHODS'] = 'Не визначено способу аутентифікації';
$_lang['AUTHMETHNOTEN'] = 'Спосіб аутентифікації %s не активований';
$_lang['PASSTOOSHORT'] = 'Ваш пароль не прийнятний, так як він занадто короткий.';
$_lang['USERNOTFOUND'] = 'Такого користувача не знайдено';
$_lang['INVALIDUNAME'] = 'Неправильний логін';
$_lang['INVALIDPASS'] = 'Неправильний пароль';
$_lang['AUTHFAILED'] = 'Помилка аутентифікації';
$_lang['YACCBLOCKED'] = 'Ваш обліковий запис заблокований';
$_lang['YACCEXPIRED'] = 'Ваша реєстрація закінчилася';
$_lang['INVUSERGROUP'] = 'Невірна група користувачів';
$_lang['NAME'] = 'Ім я';
$_lang['FIRSTNAME'] = 'Ім я';
$_lang['LASTNAME'] = 'Прізвище';
$_lang['EMAIL'] = 'Email';
$_lang['INVALIDEMAIL'] = 'Неправильний email';
$_lang['ADMINISTRATOR'] = 'Адміністратор';
$_lang['GUEST'] = 'Гість';
$_lang['EXTERNALUSER'] = 'Зовнішній відвідувач';
$_lang['USER'] = 'Користувач';
$_lang['GROUP'] = 'Група';
$_lang['NOTALLOWACCPAGE'] = 'У вас немає прав для доступу до цієї сторінки! Потрібна реєстрація.';
$_lang['NOTALLOWACCITEM'] = 'У вас немає прав для доступу до цього об єкту! Потрібна реєстрація.';
$_lang['NOTALLOWMANITEM'] = 'У вас немає прав для керування цим об єктом! Потрібна реєстрація.';
$_lang['NOTALLOWACTION'] = 'У вас немає прав для такої дії! Потрібна реєстрація.';
$_lang['NEED_HIGHER_ACCESS'] = 'У вас повинен бути вищий рівень доступу для цієї дії!';
$_lang['AREYOUSURE'] = 'Ви впевнені?';

/* highslide */
$_lang['LOADING'] = 'Загрузка...';
$_lang['CLICK_CANCEL'] = 'Нажмите для отмены';
$_lang['MOVE'] = 'Перемещение';
$_lang['PLAY'] = 'Пуск';
$_lang['PAUSE'] = 'Пауза';
$_lang['RESIZE'] = 'Изменение размера';

/* admin */
$_lang['ADMINISTRATION'] = 'Адміністрація';
$_lang['SETTINGS'] = 'Основні налаштування';
$_lang['DATABASE'] = 'База даних';
$_lang['ON'] = 'Увімкнено';
$_lang['OFF'] = 'Вимкнено';
$_lang['WARNING'] = 'Попередження';
$_lang['SAVE'] = 'Зберегти';
$_lang['APPLY'] = 'Застосувати';
$_lang['CANCEL'] = 'Скасувати';
$_lang['LIMIT'] = 'Ліміт';
$_lang['ORDERING'] = 'Порядок';
$_lang['NO_RESULTS'] = 'Результатів не знайдено!';
$_lang['CONNECT_ERROR'] = 'Помилка з єднання';
$_lang['DELETE_SEL_ITEMS'] = 'Видалити обрані об єкти?';
$_lang['TOGGLE_SELECTED'] = 'Переключити обране';
$_lang['NO_ITEMS_SELECTED'] = 'Немає обраних об єктів!';
$_lang['ID'] = 'ID';
$_lang['ACTION_FAILED'] = 'Дія не вдалася!';
$_lang['ACTION_SUCCESS'] = 'Дія завершена успішно!';
$_lang['NO_IMAGE_UPLOADED'] = 'Зображення не завантажено';
$_lang['NO_FILE_UPLOADED'] = 'Файл не завантажено';
$_lang['MODULES'] = 'Модулі';
$_lang['COMPONENTS'] = 'Компоненти';
$_lang['TEMPLATES'] = 'Шаблони';
$_lang['SEARCH_ENGINES'] = 'Пошукі системи';
$_lang['AUTH_METHODS'] = 'Способи аутентифікації';
$_lang['CONTENT_PLUGINS'] = 'Плагіни змісту';
$_lang['PLUGINS'] = 'Плагіни';
$_lang['PUBLISHED'] = 'Опубліковано';
$_lang['ACCESS'] = 'Доступ';
$_lang['ACCESS_LEVEL'] = 'Рівень доступу';
$_lang['TITLE'] = 'Заголовок';
$_lang['MOVE_UP'] = 'Перемістити выще';
$_lang['MOVE_DOWN'] = 'Перемістити нижче';
$_lang['WIDTH'] = 'Ширина';
$_lang['HEIGHT'] = 'Висота';
$_lang['ITEM_SAVED'] = 'Дані збережено';
$_lang['FIRST'] = 'Перший';
$_lang['LAST'] = 'Останній';
$_lang['SUGGESTED'] = 'Бажано';
$_lang['VALIDATE'] = 'Затвердити';
$_lang['NEVER'] = 'Ніколи';
$_lang['ALL'] = 'Усе';
$_lang['ALL_GROUPS_LEVEL'] = "Усі групи рівня %s";
$_lang['REQDROPPEDSEC'] = 'Ваш запит знижений з міркувань безпеки. Будь ласка, спробуйте ще раз.';
$_lang['PROVIDE_TRANS'] = 'Будь ласка, надайте переклад!';
$_lang['AUTO_TRANS'] = 'Автоматичний переклад';
$_lang['STATISTICS'] = 'Статистика';
$_lang['UPLOAD'] = 'Завантаження';
$_lang['MORE'] = 'Далі...';
//Elxis 4.2
$_lang['TRANSLATIONS'] = 'переклади';
$_lang['CHECK_UPDATES'] = 'Перевірка оновлень';
$_lang['TODAY'] = 'сьогодні';
$_lang['YESTERDAY'] = 'вчора';
//Elxis 4.3
$_lang['PUBLISH_ON'] = 'Опублікувати';
$_lang['UNPUBLISHED'] = 'неопублікований';
$_lang['UNPUBLISH_ON'] = 'Скасувати публікацію на';
$_lang['SCHEDULED_CRON_DIS'] = 'There are %s scheduled items but Cron Jobs are disabled!';
$_lang['CRON_DISABLED'] = 'Cron Jobs are disabled!';
$_lang['ARCHIVE'] = 'Архів';
$_lang['RUN_NOW'] = 'Запустити зараз';
$_lang['LAST_RUN'] = 'Остання страта';
$_lang['SEC_AGO'] = '%s сек назад';
$_lang['MIN_SEC_AGO'] = '%s хвилин і %s сек назад';
$_lang['HOUR_MIN_AGO'] = '1 годину і %s хвилин назад';
$_lang['HOURS_MIN_AGO'] = '%s годин і %s хвилин назад';
$_lang['CLICK_TOGGLE_STATUS'] = 'Натисніть для перемикання статусу';
//Elxis 4.5
$_lang['IAMNOTA_ROBOT'] = 'Я не робот';
$_lang['VERIFY_NOROBOT'] = 'Будь ласка, підтвердіть, що ви не робот!';
$_lang['CHECK_FS'] = 'Файли перевірити';
//Elxis 5.0
$_lang['TOTAL_ITEMS'] = '%s елементів';
$_lang['SEARCH_OPTIONS'] = 'Параметри пошуку';
$_lang['FILTERS_HAVE_APPLIED'] = 'Застосовані фільтри';
$_lang['FILTER_BY_ITEM'] = 'Фільтр для цього елементу';
$_lang['REMOVE_FILTER'] = 'Видалити фільтр';
$_lang['TOTAL'] = 'Всього';
$_lang['OPTIONS'] = 'Опції';
$_lang['DISABLE'] = 'Вимкнути';
$_lang['REMOVE'] = 'Видалити';
$_lang['ADD_ALL'] = 'Add all';
$_lang['TOMORROW'] = 'Завтра';
$_lang['NOW'] = 'Зараз';
$_lang['MIN_AGO'] = '1 хвилина тому';
$_lang['MINS_AGO'] = '%s хвилин тому';
$_lang['HOUR_AGO'] = '1 годину тому';
$_lang['HOURS_AGO'] = '%s годин тому';
$_lang['IN_SEC'] = 'In %s сек';
$_lang['IN_MINUTE'] = 'Через 1 хвилину';
$_lang['IN_MINUTES'] = 'Через %s хвилин';
$_lang['IN_HOUR'] = 'Через 1 годину';
$_lang['IN_HOURS'] = 'Через %s годин';
$_lang['OTHER'] = 'Інше';
$_lang['DELETE_CURRENT_IMAGE'] = 'Видалити зображення';
$_lang['NO_IMAGE_FILE'] = 'Немає файлу зображення!';
$_lang['SELECT_FILE'] = 'Виділити файл';

?>