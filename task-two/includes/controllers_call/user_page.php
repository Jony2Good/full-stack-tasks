<?php
/**
 * Регистрируем action для взамиодействия с функцией request (lib/common.js)
 * отправляющей HTTP запрос к выбранному методу класса UserPage
 * инициализация $act осществляется в функциях директории js/common.js
 * @param string $act
 * @param array<string>|null $d
 * @return array|string|null
 */
function controller_user_page(string $act, ?array $d): array|string|null
{
    if ($act == 'edit_window') return User_Page::user_page_edit_window($d);
    if ($act == 'edit_update') return User_Page::user_page_edit_update($d);
    if ($act == 'delete_window') return User_Page::user_page_delete_window($d);
    if ($act == 'delete_user') return User_Page::user_page_delete($d);
    if ($act == 'add_window') return User_Page::user_page_add_window($d);
    if ($act == 'add_user') return User_Page::user_page_edit_update($d);
    return '';
}
