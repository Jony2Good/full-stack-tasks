<?php

function controller_search($act, $d) {
    if ($act == 'plots') return Plot::plots_fetch($d);
    if ($act == 'users') return User_Page::user_page_fetch($d);
    return '';
}
