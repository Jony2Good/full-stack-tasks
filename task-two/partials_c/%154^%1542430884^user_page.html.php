<div class="sub_header">
    <div>
        <div class="btn_sub" onclick="common.user_page_add_window();">Add</div>
    </div>
    <div>
        <div id="paginator"><?php echo self::$_tpl_vars['paginator']; ?>
</div>
        <div class="input_group">
            <i class="icon_search"></i>
            <input id="search" type="text" value="<?php echo self::$_tpl_vars['search']; ?>
" placeholder="Search" oninput="common.search_do('users');">
        </div>
    </div>
</div>
<div id="table">
    <?php $_html_tpl_vars = self::$_tpl_vars;
self::include_file(['file'=>"./partials/user_page_table.html", 'vars'=>[]]);
self::$_tpl_vars = $_html_tpl_vars;
unset($_html_tpl_vars);
 ?>
</div>