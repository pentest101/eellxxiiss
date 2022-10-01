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


$_lang = array();
$_lang['MENU'] = 'Меню';
$_lang['MENU_MANAGER'] = 'Керування меню';
$_lang['MENU_ITEM_COLLECTIONS'] = 'Списки меню з пунктами';
$_lang['SN'] = '№'; //serial number
$_lang['MENU_ITEMS'] = 'Пункти меню';
$_lang['COLLECTION'] = 'Список меню';
$_lang['WARN_DELETE_COLLECT'] = 'Це видалить меню, всі його пункти та модуль, пов\'язаний з ним!';
$_lang['CNOT_DELETE_MAINMENU'] = 'Не можна видаляти основне меню (maimenu)!';
$_lang['MODULE_TITLE'] = 'Назва модуля';
$_lang['COLLECT_NAME_INFO'] = 'Назва меню повинна бути уникальною та складатися з латинських букв та цифр без пробілів';
$_lang['ADD_NEW_COLLECT'] = 'Додати нове меню';
$_lang['EXIST_COLLECT_NAME'] = 'Меню з такою назвою вже існує!';
$_lang['MANAGE_MENU_ITEMS'] = 'Керування пунктами';
$_lang['EXPAND'] = 'Розгорнути';
$_lang['FULL'] = 'Повністю';
$_lang['LIMITED'] = 'Обмежено';
$_lang['TYPE'] = 'Тип';
$_lang['LEVEL'] = 'Рівень';
$_lang['MAX_LEVEL'] = 'Максимальний рівень';
$_lang['LINK'] = 'Посилання';
$_lang['ELXIS_LINK'] = 'Внутрішнє посилання сайту';
$_lang['SEPARATOR'] = 'Роздільник';
$_lang['WRAPPER'] = 'Фрейм';
$_lang['WARN_DELETE_MENUITEM'] = 'Ви впевнені, що хочете видалити цей пункт меню? Підпункти також будуть видалені!';
$_lang['SEL_MENUITEM_TYPE'] = 'Виберіть тип пункта меню';
$_lang['LINK_LINK_DESC'] = 'Внутрішнє посилання до сторінки сайту.';
$_lang['LINK_URL_DESC'] = 'Стандартне посилання до зовнішньої сторінки.';
$_lang['LINK_SEPARATOR_DESC'] = 'Текст рядка без посилання.';
$_lang['LINK_WRAPPER_DESC'] = 'Посилання на зовнішню сторінку, яка відображається на сайті у фреймі.';
$_lang['EXPAND_DESC'] = 'Створюється, якщо підтримується, підменю. Обмежено - показує тільки перший рівень, повне розгортання - все дерево.';
$_lang['LINK_TARGET'] = 'Ціль посилання';
$_lang['SELF_WINDOW'] = 'Теж саме вікно';
$_lang['NEW_WINDOW'] = 'Нове вікно';
$_lang['PARENT_WINDOW'] = 'Батьківське вікно';
$_lang['TOP_WINDOW'] = 'Верхнє вікно';
$_lang['NONE'] = 'Без';
$_lang['ELXIS_INTERFACE'] = 'Інтерфейс Elxis';
$_lang['ELXIS_INTERFACE_DESC'] = 'Посилання index.php генерує звичайну сторінку з модулями, посилання inner.php - сторінки, які відображають функції тільки головного компонента (корисно для спливаючих вікон).';
$_lang['FULL_PAGE'] = 'Сторінка повністю';
$_lang['ONLY_COMPONENT'] = 'Тільки компонент';
$_lang['POPUP_WINDOW'] = 'Popup-вікно';
$_lang['TYPICAL_POPUP'] = 'Стандартне вікно';
$_lang['LIGHTBOX_WINDOW'] = 'Lightbox вікно';
$_lang['PARENT_ITEM'] = 'Вищестоящий пункт';
$_lang['PARENT_ITEM_DESC'] = 'Зробити цей пункт меню як підменю іншого пункту меню, вибравши його в якості батьківського.';
$_lang['POPUP_WIDTH_DESC'] = 'Ширина спливаючого вікна або фрейму в пікселях. 0 для автоматичного вибору.';
$_lang['POPUP_HEIGHT_DESC'] = 'Висота спливаючого вікна або фрейму в пікселях. 0 для автоматичного вибору.';
$_lang['MUST_FIRST_SAVE'] = 'Ви повинні спочатку зберегти цей пункт!';
$_lang['CONTENT'] = 'Зміст';
$_lang['SECURE_CONNECT'] = 'Безпечне з єднання';
$_lang['SECURE_CONNECT_DESC'] = 'Тільки якщо це дозволено в основних налаштуваннях і у вас є встановлений SSL сертифікат.';
$_lang['SEL_COMPONENT'] = 'Виберіть компонент';
$_lang['LINK_GENERATOR'] = 'Генератор посилання';
$_lang['URL_HELPER'] = 'Напишіть повне посилання на зовнішню сторінку та заголовок посилання. 
    Ви можете відкрити посилання у спливаючому вікні або як рамку в якості основи в lightbox.
	Налаштуйте ширину та высоту спливаючого вікна або фрейму lightbox.';
$_lang['SEPARATOR_HELPER'] = 'Роздільник є простим текстом, а не посиланням. Тобто, посилання не має значення.
	Використовуйте його як заголовок без посилання для вашого підменю або для інших цілей.';
$_lang['WRAPPER_HELPER'] = 'Фрейм дозволяю показати будь-яку зовнішню сторінку в рамці, яка називається i-frame.
	Зовнішні сторінки будуть виглядати як частина вашого сайту. Ви повинні вказати повний шлях до сторінки у фреймі.
	Ви можете відкрити посилання у спливаючому вікні або в рамці lightbox. 
	Налаштуйте ширину та висоту спливаючого вікна або фрейму lightbox.';
$_lang['TIP_INTERFACE'] = '<strong>Порада</strong><br />Виберіть <strong>Тільки компонент</strong> в Інші налаштування - Інтерфейс Elxis,
	якщо ви плануєте  відкрити посилання  в  popup-вікні/фреймі lightbox.';
$_lang['COMP_NO_PUBLIC_IFACE'] = 'Цей компонент не має публічного інтерфейсу для відображення!';
$_lang['STANDARD_LINKS'] = 'Стандартне посилання';
$_lang['BROWSE_ARTICLES'] = 'Перегляд статтей';
$_lang['ACTIONS'] = 'Дії';
$_lang['LINK_TO_ITEM'] = 'Посилання для цього элемента';
$_lang['LINK_TO_CAT_RSS'] = 'Посилання на RSS стрічку категорії';
$_lang['LINK_TO_CAT_ATOM'] = 'Посилання на ATOM потік категорії';
$_lang['LINK_TO_CAT_OR_ARTICLE'] = 'Посилання для категорії або статті';
$_lang['ARTICLE'] = 'Стаття';
$_lang['ARTICLES'] = 'Статті';
$_lang['ASCENDING'] = 'За зростанням';
$_lang['DESCENDING'] = 'За зменшенням';
$_lang['LAST_MODIFIED'] = 'Остання модифікація';
$_lang['CAT_CONT_ART'] = "Категорія %s містить статтей - %s."; //fill in by CATEGORY NAME and NUMBER
$_lang['ART_WITHOUT_CAT'] = "Без категорії статтей - %s."; //fill in by NUMBER
$_lang['NO_ITEMS_DISPLAY'] = 'Немає об єктів для показу!';
$_lang['ROOT'] = 'Основна категорія'; //root category
$_lang['COMP_FRONTPAGE'] = "Головна сторінка для компонента %s"; //fill in by COMPONENT NAME
$_lang['LINK_TO_CAT'] = 'Посилання на категорію зі статтями';
$_lang['LINK_TO_CAT_ARTICLE'] = 'Посилання на статтю в категорії';
$_lang['LINK_TO_AUT_PAGE'] = 'Посилання на автономну сторінку';
$_lang['SPECIAL_LINK'] = 'Спеціальне посилання';
$_lang['FRONTPAGE'] = 'Головна сторінка';
$_lang['BASIC_SETTINGS'] = 'Загальні налаштування';
$_lang['OTHER_OPTIONS'] = 'Інші налаштування';
//5.0
$_lang['ICON_FONT'] = 'Значок';
$_lang['ICON_FONT_DESC'] = 'За бажанням можна також показати значок шрифта (Elxis Font, Font Awesome, etc).';
$_lang['ONCLICK_DESC'] = 'Виконайте дію JavaScript клацнувши мишкою або дотиком';
$_lang['JSCODE'] = 'Код Javascript';
$_lang['JSCODE_DESC'] = 'функція/код Javascript, які буде виконано після натискання кнопки';

?>