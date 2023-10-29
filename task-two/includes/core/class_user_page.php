<?php

class User_Page
{
    /**
     * Получение из БД данных для формирования модальных окон
     * @param string $user_id
     * @return array<string|int>
     */
    public static function user_page_info(string $user_id): array
    {
        $q = DB::query("SELECT plot_id, user_id, first_name, last_name, phone, email
            FROM users WHERE user_id='" . $user_id . "' LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => (int)$row['plot_id'],
                'user_id' => (int)$row['user_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'phone' => $row['phone'],
                'email' => $row['email'],
            ];
        } else {
            return [
                'id' => 0,
                'user_id' => 0,
                'first_name' => '',
                'last_name' => '',
                'phone' => '',
                'email' => '',
            ];
        }
    }

    /**
     * Формирует запрос к БД для 1) поиска по сайту, 2) отображения данных в таблице по url/users
     * 3) для создания пагинации
     * @param array $d
     * @return array<string|int>|null
     */
    public static function user_page_list(array $d = []): array|null
    {
        $search = isset($d['search']) && trim($d['search']) ? $d['search'] : '';
        $offset = isset($d['offset']) && is_numeric($d['offset']) ? $d['offset'] : 0;
        $limit = 20;
        $items = [];
        $where = [];
        if ($search) $where[] = "(first_name LIKE '%" . $search . "%' OR phone LIKE '%" . $search . "%' OR email LIKE '%" . $search . "%')";
        $where = $where ? "WHERE " . implode(" AND ", $where) : "";

        $q = DB::query("SELECT plot_id, user_id, first_name, last_name, phone, email,last_login
             FROM users " . $where . " ORDER BY plot_id+0 LIMIT " . $offset . ", " . $limit . ";") or die (DB::error());
        while ($row = DB::fetch_row($q)) {
            $items[] = [
                'id' => (int)$row['plot_id'],
                'user_id' => (int)$row['user_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'last_login' => strtotime(date('Y/m/d', $row['last_login'])),
            ];
        }
        // paginator
        $q = DB::query("SELECT count(*) FROM users " . $where . ";");
        $count = ($row = DB::fetch_row($q)) ? $row['count(*)'] : 0;
        $url = 'user?';
        if ($search) $url .= '&search=' . $search;
        paginator($count, $offset, $limit, $url, $paginator);
        return ['items' => $items, 'paginator' => $paginator];
    }

    /**
     * Используется для подключения HTML файла ./partials/user_page_table.html
     * и наполнения его данными из БД    
     * @param array<string|int> $d
     * @return array<string>
     */
    public static function user_page_fetch(array $d = []): array
    {
        $info = self::user_page_list($d);
        HTML::assign('items', $info['items']);
        return ['html' => HTML::fetch('./partials/user_page_table.html'), 'paginator' => $info['paginator']];
    }

    /**
     * @param array<string> $d
     * @return array<string>
     */
    public static function user_page_edit_window(array $d = []): array
    {
        $user_id = isset($d['user_id']) && is_numeric($d['user_id']) ? $d['user_id'] : 0;
        HTML::assign('items', self::user_page_info($user_id));
        return ['html' => HTML::fetch('./partials/user_page_edit.html')];
    }

    /**
     * Принимает из js/common.js данные из поле формы модального окна,
     * валидирует строковые данные, формирует и направляет два запроса
     * к БД 1) заменить данные 2) ввести данные (в том числе, добаление пользователя)
     * @param array<string> $d
     * @return array|string|false
     */
    public static function user_page_edit_update(array $d = []): array|string|false
    {
        $user_id = isset($d['user_id']) && is_numeric($d['user_id']) ? $d['user_id'] : 0;
        $plot_id = isset($d['plot_id']) && is_string($d['plot_id']) ? $d['plot_id'] : '';
        $first_name = isset($d['first_name']) && is_string($d['first_name']) ? $d['first_name'] : '';
        $last_name = isset($d['last_name']) && is_string($d['last_name']) ? $d['last_name'] : '';
        $email = isset($d['email']) && trim($d['email']) ? trim($d['email']) : '';
        $phone = isset($d['phone']) && trim($d['phone']) ? trim($d['phone']) : '';
        $offset = isset($d['offset']) ? preg_replace('~\D+~', '', $d['offset']) : 0;
        $phone = phone_formatting($phone);

        if (checkNameData([$first_name, $last_name]) && checkEmailData($email)) {
            return self::errorPage();
        } else {
            if ($user_id) {
                $set = [];
                $set[] = "first_name='" . $first_name . "'";
                $set[] = "last_name='" . $last_name . "'";
                $set[] = "email='" . $email . "'";
                $set[] = "phone='" . $phone . "'";
                $set[] = "updated='" . Session::$ts . "'";
                $set[] = "plot_id='" . $plot_id . "'";
                $set = implode(", ", $set);
                DB::query("UPDATE users SET " . $set . " WHERE user_id='" . $user_id . "' LIMIT 1;") or die (DB::error());
            } else {
                DB::query("INSERT INTO users (
                plot_id,
                first_name,
                last_name,
                email,
                phone,
                updated
            ) VALUES (
                '" . $plot_id . "',
                '" . $first_name . "',
                '" . $last_name . "',
                '" . $email . "',
                '" . $phone . "',
                '" . Session::$ts . "'
            );") or die (DB::error());
            }
            return self::user_page_fetch(['offset' => $offset]);
        }
    }

    /**
     * Формирование модального окна для подтверждения удаления данных
     * @param array<string> $d
     * @return array<string>
     */
    public static function user_page_delete_window(array $d): array
    {
        $user_id = isset($d['user_id']) && is_numeric($d['user_id']) ? $d['user_id'] : 0;
        HTML::assign('items', self::user_page_info($user_id));
        return ['html' => HTML::fetch('./partials/user_page_delete_confirmed.html')];
    }

    /**
     * Получает запрос по HTTP из директории js/common.js
     * удаляет данные из БД
     * @param array<string> $d
     * @return array<string>
     */
    public static function user_page_delete(array $d): array
    {
        $user_id = isset($d['user_id']) && is_numeric($d['user_id']) ? $d['user_id'] : 0;
        $offset = isset($d['offset']) ? preg_replace('~\D+~', '', $d['offset']) : 0;
        DB::query("DELETE FROM users WHERE user_id = {$user_id}") or die (DB::error());
        return self::user_page_fetch(['offset' => $offset]);
    }

    /**
     * Формирование модального окна при нажатии на кнопку add
     * @return array
     */
    public static function user_page_add_window(): array
    {
        return ['html' => HTML::fetch('./partials/user_page_add.html')];
    }

    /**
     * Подключение страницы об ошибке
     * @return array<string>
     */
    public static function errorPage(): array
    {
        return ['html' => HTML::fetch('./partials/error_page.html')];
    }

}
