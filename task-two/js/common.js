let common = {

    // vars

    modal_progress: false,
    modal_open: false,

    // common

    init: () => {
        add_event(document, 'mousedown touchstart', common.auto_hide_modal);
        add_event(document, 'click', () => common.menu_popup_hide_all('inactive', event));
        add_event(document, 'scroll', () => common.menu_popup_hide_all('all', event));
    },

    menu_popup_toggle: (el, e) => {
        el = qs('.menu_popup', el);
        if (has_class(el, 'active') && !e.target.closest('.menu_popup')) remove_class(el, 'active');
        else {
            common.menu_popup_hide_all('all');
            add_class(el, 'active');
        }
        if (e.target.tagName !== 'A') cancel_event(e);
    },

    menu_popup_hide_all: (mode, e) => {
        qs_all('.menu_popup.active').forEach((el) => {
            if (mode === 'all' || !e.target.closest('.menu_popup')) remove_class(el, 'active');
        })
    },

    // modal

    modal_show: (width, content) => {
        // progress
        if (common.modal_progress) return false;
        // width
        let display_width = w_width();
        if (width > display_width - 20) width = display_width - 40;
        // active
        add_class('modal', 'active');
        common.modal_open = true;
        set_style('modal_content', 'width', width);
        set_style(document.body, 'overflowY', 'hidden');
        // actions
        html('modal_content', content);
        common.modal_resize();

        const modal_user = document.querySelector('.modal_user_page');
        if (modal_user != null) {
            common.validate();
        }

    },

    validate: () => {
        const button = document.querySelector('.btn_modal');
        const first_name = document.getElementById('first_name');
        const last_name = document.getElementById('last_name');
        const email = document.getElementById('email');
        const phone = document.getElementById('phone');

        const valid = {
            first_name: true,
            last_name: true,
            email: true,
            phone: true,
        };

        function validateInputs(value) {
            return (value !== '') ? true : false;
        }

        [first_name, last_name, email, phone].forEach((elem) =>
            elem.addEventListener("input", (e) => {
                unsetValid(e.currentTarget);
            })
        );
        first_name.addEventListener("blur", (e) => {
            valid.first_name = validateInputs(e.target.value);
            setValid(e.target, valid.first_name);
        });

        last_name.addEventListener("blur", (e) => {
            valid.last_name = validateInputs(e.target.value);
            setValid(e.target, valid.last_name);
        });

        email.addEventListener("blur", (e) => {
            valid.email = validateInputs(e.target.value);
            valid.email = common.validateEmail(e.target.value);
            e.target.value = e.target.value.toLowerCase();
            setValid(e.target, valid.email);
        });

        phone.addEventListener("blur", (e) => {
            valid.phone = validateInputs(e.target.value);
            e.target.value = e.target.value.replace(/[^+\d]/g, '');
            setValid(e.target, valid.phone);
        });

        function setValid(target, value) {
            if (value) {
                target.classList.add("is-valid");
            } else {
                target.classList.add("is-invalid");
            }
            setButtonDisable('none');
        }

        function unsetValid(target) {
            target.classList.remove("is-valid");
            target.classList.remove("is-invalid");
            button.removeAttribute('style');
        }

        function setButtonDisable(state) {
            button.style.pointerEvents = state || Object.values(valid).includes(false);
            if (!Object.values(valid).includes(false)) {
                button.removeAttribute('style');
            }
        }
    },

    validateEmail: (value) => {
        let reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if (reg.test(value) === false) {
            return false;
        }
        return true;
    },

    modal_hide: () => {
        // progress
        if (common.modal_progress) return false;
        common.modal_progress = true;
        // update
        set_style('modal_container', 'overflow', 'hidden');
        remove_class('modal', 'active');
        html('modal_content', '');
        set_style('modal_container', 'overflow', '');
        set_style(document.body, 'overflowY', 'scroll');
        common.modal_progress = false;
        common.modal_open = false;
    },

    modal_resize: () => {
        // vars
        let h_display = window.innerHeight;
        let h_content = ge('modal_content').clientHeight;
        let k = (h_content * 100 / h_display > 85) ? 0.5 : 0.25;
        let margin = (h_display - h_content) * k;
        if (margin < 20) margin = 20;
        // update
        ge('modal_content').style.marginTop = margin + 'px';
        ge('modal_content').style.height = 'auto';
    },

    auto_hide_modal: (e) => {
        if (!has_class('modal', 'active')) return false;
        let t = e.target || e.srcElement;
        if (t.id === 'modal_overlay') on_click('modal_close');
    },

    // auth

    auth_send: () => {
        // vars
        let data = {phone: gv('phone')};
        let location = {dpt: 'auth', act: 'send'};
        // call
        request({location: location, data: data}, (result) => {
            if (result.error_msg) {
                html('login_note', result.error_msg);
                remove_class('login_note', 'fade');
                setTimeout(function () {
                    add_class('login_note', 'fade');
                }, 3000);
                setTimeout(function () {
                    html('login_note', '');
                }, 3500);
            } else html(qs('body'), result.html);
        });
    },

    auth_confirm: () => {
        // vars
        let data = {phone: gv('phone'), code: gv('code')};
        let location = {dpt: 'auth', act: 'confirm'};
        // call
        request({location: location, data: data}, (result) => {
            if (result.error_msg) {
                html('login_note', result.error_msg);
                remove_class('login_note', 'fade');
                setTimeout(function () {
                    add_class('login_note', 'fade');
                }, 3000);
                setTimeout(function () {
                    html('login_note', '');
                }, 3500);
            } else window.location = window.location.href;
        });
    },

    // search

    search_do: (act) => {
        // vars
        let data = {search: gv('search')};
        let location = {dpt: 'search', act: act};
        // call
        request({location: location, data: data}, (result) => {
            html('table', result.html);
            html('paginator', result.paginator);
        });
    },

    // plots

    plot_edit_window: (plot_id, e) => {
        // actions
        cancel_event(e);
        common.menu_popup_hide_all('all');
        // vars
        let data = {plot_id: plot_id};
        let location = {dpt: 'plot', act: 'edit_window'};
        // call
        request({location: location, data: data}, (result) => {
            common.modal_show(400, result.html);
        });
    },

    plot_edit_update: (plot_id = 0) => {
        // vars
        let data = {
            plot_id: plot_id,
            status: gv('status'),
            billing: gv('billing'),
            number: gv('number'),
            size: gv('size'),
            price: gv('price'),
            offset: global.offset
        };
        let location = {dpt: 'plot', act: 'edit_update'};
        // call
        request({location: location, data: data}, (result) => {
            common.modal_hide();
            html('table', result.html);
        });
    },

    //user_page

    user_page_add_window: (e) => {
        // actions
        cancel_event(e);
        common.menu_popup_hide_all('all');
        // vars
        let location = {dpt: 'user', act: 'add_window'};
        // call
        request({location: location}, (result) => {
            common.modal_show(400, result.html);
        });
    },

    user_page_add_user: () => {
    // vars
    let data = {
        plot_id: gv('plot_id'),
        first_name: gv('first_name'),
        last_name: gv('last_name'),
        email: gv('email'),
        phone: gv('phone'),
        offset: global.offset
    };
    if(Object.values(data).includes('')) {
       return alert('Требуется заполнение формы');
    };
    let location = {dpt: 'user', act: 'add_user'};
    // call
    request({location: location, data: data}, (result) => {
        common.modal_hide();
        html('table', result.html);
    });
},

    user_page_edit_window: (user_id, e) => {
        // actions
        cancel_event(e);
        common.menu_popup_hide_all('all');
        // vars
        let data = {user_id: user_id};
        let location = {dpt: 'user', act: 'edit_window'};
        // call
        request({location: location, data: data}, (result) => {
            common.modal_show(400, result.html);
        });
    },

    user_page_edit_update: (user_id = 0) => {
        // vars
        let data = {
            user_id: user_id,
            plot_id: gv('plot_id'),
            first_name: gv('first_name'),
            last_name: gv('last_name'),
            email: gv('email'),
            phone: gv('phone'),
            offset: global.offset
        };
        let location = {dpt: 'user', act: 'edit_update'};
        // call
        request({location: location, data: data}, (result) => {
            common.modal_hide();
            html('table', result.html);
        });
    },
    user_page_delete_window: (user_id, e) => {
        // actions
        cancel_event(e);
        common.menu_popup_hide_all('all');
        // vars
        let data = {user_id: user_id};
        let location = {dpt: 'user', act: 'delete_window'};
        // call
        request({location: location, data: data}, (result) => {
            common.modal_show(400, result.html);
        });
    },

    user_page_delete: (user_id = 0) => {
        // vars
        let data = {
            user_id: user_id,
            offset: global.offset
        };
        let location = {dpt: 'user', act: 'delete_user'};
        // call
        request({location: location, data: data}, (result) => {
            common.modal_hide();
            html('table', result.html);
        });
    },

}

add_event(document, 'DOMContentLoaded', common.init);