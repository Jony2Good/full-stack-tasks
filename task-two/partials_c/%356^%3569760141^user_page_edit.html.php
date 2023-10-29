<div class="modal_head modal_user_page">
    <i class="icon_close" onclick="common.modal_hide()"></i>
</div>
<div class="modal_body">
    <div class="input_group_modal">
        <div>Plot number</div>
        <input type="text" id="plot_id" value="<?php echo self::$_tpl_vars['items']['id']; ?>
">
    </div>
    <div class="input_group_modal">
        <div>First name</div>
        <input type="text" id="first_name" value="<?php echo self::$_tpl_vars['items']['first_name']; ?>
">
    </div>
    <div class="input_group_modal">
        <div>Last name</div>
        <input type="text" id="last_name" value="<?php echo self::$_tpl_vars['items']['last_name']; ?>
">
    </div>
    <div class="input_group_modal">
        <div>Email</div>
        <input type="text" id="email" value="<?php echo self::$_tpl_vars['items']['email']; ?>
">
    </div>
    <div class="input_group_modal">
        <div>Phone</div>
        <input type="text" id="phone" value="<?php echo self::$_tpl_vars['items']['phone']; ?>
">
    </div>
    <div class="modal_controls">
        <div>
            <div class="btn_modal" onclick="common.user_page_edit_update(<?php echo self::$_tpl_vars['items']['user_id']; ?>
);">Save</div>
        </div>
        <div>
            <div class="btn_modal light" onclick="common.modal_hide();">Cancel</div>
        </div>
    </div>
</div>