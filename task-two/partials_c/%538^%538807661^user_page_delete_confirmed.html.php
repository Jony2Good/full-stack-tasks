<div class="modal_head">
    <i class="icon_close" onclick="common.modal_hide()"></i>
</div>
<div class="modal_body">
    <h1 class="modal_body-title">Вы действительно хотите удалить пользователя?</h1>
    <div class="modal_controls">
        <div>
            <div class="btn_modal" onclick="common.user_page_delete(<?php echo self::$_tpl_vars['items']['user_id']; ?>
);">Delete</div>
        </div>
        <div>
            <div class="btn_modal light" onclick="common.modal_hide();">Cancel</div>
        </div>
    </div>
</div>