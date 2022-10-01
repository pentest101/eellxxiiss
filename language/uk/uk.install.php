<?php 
/**
* @version: 5.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2019 Elxis.org. All rights reserved.
* @description: uk-UA (Ukranian - Ukrain) language for component CPanel
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Aleksandr Shevchenko ( http://pomogite.in.ua )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Доступ заборонено.');


$_lang = array();
$_lang['INSTALLATION'] = 'Встановлення';
$_lang['STEP'] = 'Етап';
$_lang['VERSION'] = 'Версія';
$_lang['VERSION_CHECK'] = 'Перевірка версії';
$_lang['STATUS'] = 'Статус';
$_lang['REVISION_NUMBER'] = 'Номер ревізії';
$_lang['RELEASE_DATE'] = 'Дата релізу';
$_lang['ELXIS_INSTALL'] = 'Встановлення Elxis';
$_lang['LICENSE'] = 'Ліцензія';
$_lang['VERSION_PROLOGUE'] = 'Ви збираєтеся встановлювати Elxis CMS. Нижче вказана поточна версія Elxis,
    яку ви збираєтеся встановлювати.
    Перевірте на <a href="http://www.elxis.org" target="_blank">elxis.org</a>, що це дійсно остання версія Elxis.';
$_lang['BEFORE_BEGIN'] = 'Перед встановленням Elxis';
$_lang['BEFORE_DESC'] = 'Перед тим, як розпочати встановлення Elxis, уважно прояитайте інформацію, наведену нижче.';
$_lang['DATABASE'] = 'База даних';
$_lang['DATABASE_DESC'] = 'Створіть порожню базу даних, яку Elxis буде використовувати для зберігання даних.
	Ми настійно рекомендуємо використовувати базу даних <strong>MySQL</strong>. Хоча Elxis має підтримку інших баз даних, таких як
    PostgreSQL та SQLite 3, добре перевірена тільки MySQL. <br> Для створення порожньої
    MySQL-бази даних, зайдіть в панель керування вашого хостинг-провайдеру (CPanel, Plesk, ISP Config, і т.п.), в
	phpMyAdmin чи в інший інструмент керування базами даних та створіть порожню базу даних <strong>з бажаною назвою</strong>.
	Потім присвойте <strong>користувачеві БД</strong> новостворену базу даних.
	Запишіть назву бази даних, ім\'я користувача та пароль, щоб заповнити їх пізніше, під час встановлення.';
$_lang['REPOSITORY'] = 'Сховище';
$_lang['REPOSITORY_DESC'] = 'Elxis використовує спеціальну папку для збереження кешованих сторінок, лог-файлів, резервних копій та іншого.
	За замовчуванням ця папка називається <strong>repository (сховище)</strong> та знаходиться в кореневій папці Elxis.
	Ця папка повинна мати <strong>права на запис</strong>! Ми настійно рекомендуємо <strong>перейменувати</strong> цю папку та <strong>перемістити</strong> її,
	в місце недоступне із інтернету. Після переміщення, якщо увімкнено в PHP захист <strong>open basedir</strong> 
	Вам також необхідно додати шлях до цього сховища до дозволених шляхів.'; 
$_lang['REPOSITORY_DEFAULT'] = 'Сховище (repository) знаходиться в папці за замовчуванням!';
$_lang['SAMPLE_ELXPATH'] = 'Приклад шляху до Elxis';
$_lang['DEF_REPOPATH'] = 'Шлях за замовчуванням';
$_lang['REQ_REPOPATH'] = 'Приклад рекомендованого шляху';
$_lang['CONTINUE'] = 'Продовжити';
$_lang['I_AGREE_TERMS'] = 'Я прочитав ліцензію та згоден з її умовами.';
$_lang['LICENSE_NOTES'] = 'CMS Elxis це безкоштовне програмне забезпечення, видане під <strong>Elxis Публічною Ліцензією (ЕПЛ)</strong> .
	Для продовження установки і використання Elxis, вам необхідно погодитися з умовами ЄПЛ. Прочитайте уважно
ліцензію на Elxis і, якщо ви згодні, поставте позначку в низу сторінки та натисніть кнопку <strong>Продовжити</strong>. Якщо ви не згодні, то
    припиніть установку та видаліть всі наші файли.';
$_lang['SETTINGS'] = 'Основні налаштування';
$_lang['SITE_URL'] = 'URL сайту';
$_lang['SITE_URL_DESC'] = 'Без слеша (напр. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'Абсолютний шлях до папки сховища Elxis. Залиште порожнім для шляху та імені за замовчуванням.';
$_lang['SETTINGS_DESC'] = 'Введіть основні налаштування для цього сайту. Деякі параметри повинні бути вказані до установки Elxis.
	<strong>Після завершення установки увійдіть в адміністративний розділ і налаштуйте інші параметри.
	Це перше, що обов\'язково повинен зробити адміністратор!</strong>';
$_lang['DEF_LANG'] = 'Мова за замовчуванням';
$_lang['DEFLANG_DESC'] = 'Зміст написано на мові за замовчуванням. Зміст на інших мовах перекладається з оригінального змісту
     на мові за замовчуванням.';
$_lang['ENCRYPT_METHOD'] = 'Спосіб шифрування';
$_lang['ENCRYPT_KEY'] = 'Ключ шифрування';
$_lang['AUTOMATIC'] = 'Автоматично';
$_lang['GEN_OTHER'] = 'Натисніть, щоб згенерувати інший ключ';
$_lang['SITENAME'] = 'Назва сайту';
$_lang['TYPE'] = 'Тип БД';
$_lang['DBTYPE_DESC'] = 'Мы настоятельно рекомендуем MySQL. Вы можете выбрать драйверы, которые поддерживаются Elxis и вашей системой.';
$_lang['HOST'] = 'Хост';
$_lang['TABLES_PREFIX'] = 'Префікс таблиць';
$_lang['DSN_DESC'] = 'Як альтернативу, ви можете використовувати готовий Data Source Name (DNS) для з\'єднання з базою даних.';
$_lang['SCHEME'] = 'Схема';
$_lang['SCHEME_DESC'] = 'Абсолютний шлях до файлів бази даних, якщо ви використовуєте базу даних, типу SQLite.';
$_lang['PORT'] = 'Порт';
$_lang['PORT_DESC'] = 'Порт за замовчуванням для MySQL - 3306. Залиште 0 для автоматичного вибору.';
$_lang['FTPPORT_DESC'] = 'Порт за замовчуванням для FTP - 21. Залиште 0 для автоматичного выбору.';
$_lang['USE_FTP'] = 'Використання FTP';
$_lang['PATH'] = 'Шлях';
$_lang['FTP_PATH_INFO'] = 'Відносний шлях від кореневої папки FTP до папки установки Elxis (наприклад: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'Перевірте налаштування FTP';
$_lang['CHECK_DB_SETS'] = 'Перевірте налаштування бази даних';
$_lang['DATA_IMPORT'] = 'Імпорт даних';
$_lang['SETTINGS_ERRORS'] = 'Параметри, які ви надали містять помилки!';
$_lang['NO_QUERIES_WARN'] = 'Вихідні дані імпортовані в базу даних, але не схожі на запити і не виконані.
	Перш ніж продовжити, переконайтеся, що в даних немає помилки.';
$_lang['RETRY_PREV_STEP'] = 'Повторіть попередній крок';
$_lang['INIT_DATA_IMPORTED'] = 'Вихідні дані імпортовані в базу даних.';
$_lang['QUERIES_EXEC'] = "Виконано %s SQL запитів."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Реєстрація адміністратора сайту';
$_lang['CONFIRM_PASS'] = 'Підтвердіть пароль';
$_lang['AVOID_COMUNAMES'] = 'Уникайте стандартних логінів, таких як admin та administrator.'; 
$_lang['YOUR_DETAILS'] = 'Інформація про адміністратора';
$_lang['PASS_NOMATCH'] = 'Паролі не співпадають!';
$_lang['REPOPATH_NOEX'] = 'Неіснуючий шлях!';
$_lang['FINISH'] = 'Кінець';
$_lang['FRIENDLY_URLS'] = 'Дружні URL-и';
$_lang['FRIENDLY_URLS_DESC'] = 'Ми настійно рекомендуємо увімкнути їх. Після цього, Elxis намагатиметься перейменувати файл htaccess.txt
	в <strong>.htaccess</strong>. Якщо вже такий файл є в цій папці, то існуючий файл .htaccess буде видалений.';
$_lang['GENERAL'] = 'Основні';
$_lang['ELXIS_INST_SUCC'] = 'Встановлення Elxis пройшло успішно.';
$_lang['ELXIS_INST_WARN'] = 'Встановлення Elxis пройшло з попередженнями.';
$_lang['CNOT_CREA_CONFIG'] = 'Не вдалося створити файл <strong>configuration.php</strong> в кореневій папці Elxis.';
$_lang['CNOT_REN_HTACC'] = 'Не вдалося перейменувати файл <strong>htaccess.txt</strong> на <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = 'Файл конфігурації';
$_lang['CONFIG_FILE_MANUAL'] = 'Створіть вручну файл configuration.php, скопіюйте наступний код та вставте його в цей файл.';
$_lang['REN_HTACCESS_MANUAL'] = 'Перейменуйте вручну файл <strong>htaccess.txt</strong> на <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = 'Що робити далі?';
$_lang['RENAME_ADMIN_FOLDER'] = 'Для підвищення безпеки можна змінити ім\'я адміністративної папки (<em>estia</em>) на будь-яке інше ім\'я.
	У цьому випадку, необхідно вписати в файл .htaccess нове ім\'я.';
$_lang['LOGIN_CONFIG'] = '<strong>Увійдіть в адміністративну частину і визначте інші налаштування.</strong>';
$_lang['VISIT_NEW_SITE'] = 'Перейти на мій новий сайт';
$_lang['VISIT_ELXIS_SUP'] = 'Відвідате сайт підтримки Elxis';
$_lang['THANKS_USING_ELXIS'] = 'Дякуємо вам, що обрали CMS Elxis.';
//Elxis 5.0
$_lang['OTHER_LANGS'] = 'Інші мови';
$_lang['OTHER_LANGS_DESC'] = 'Які інші мови, окрім вказаних за замовчуванням, потрібно використовувати?';
$_lang['ALL_LANGS'] = 'Всі';
$_lang['NONE_LANGS'] = 'Жодної';
$_lang['REMOVE'] = 'Видалити';
$_lang['CONFIG_EMAIL_DISPATCH'] = 'Налаштуйте розсилку email (опціонально)';
$_lang['SEND_METHOD'] = 'Метод надсилання';
$_lang['RECOMMENDED'] = 'рекомендовано';
$_lang['SECURE_CONNECTION'] = 'Безпечн з єднання';
$_lang['AUTH_REQUIRED'] = 'Потрібна автентифікація';
$_lang['AUTH_METHOD'] = 'Метод автентифікації';
$_lang['DEFAULT_METHOD'] = 'За замовчуванням';

?>