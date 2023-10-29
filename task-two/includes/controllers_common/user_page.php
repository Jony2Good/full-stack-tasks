<?php
/**
 * Отвечет за формирование страницы с url:/users
 * @return void
 */
function controller_user(): void
{
    $offset = isset($_GET['offset']) ? flt_input($_GET['offset']) : 0;
    $search = $_GET['search'] ?? '';
    $user_page = User_Page::user_page_list(['mode' => 'page', 'offset' => $offset, 'search' => $search]);
    HTML::assign('items', $user_page['items']);
    HTML::assign('paginator', $user_page['paginator']);
    HTML::assign('search', $search);
    HTML::assign('offset', $offset);
    HTML::assign('section', 'user_page.html');
    HTML::assign('main_content', 'home.html');
}
