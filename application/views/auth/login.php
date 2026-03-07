<?php
$logo_app = $setting->logo_kanan == null
    ? base_url() . 'assets/img/favicon.png'
    : base_url() . $setting->logo_kiri;
?>

<style>
    .auth-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
    }

    .auth-card {
        width: 100%;
        max-width: 400px;
        background: #1a1d23;
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.5);
    }

    .auth-header {
        background: #111318;
        padding: 2rem 2rem 1.5rem;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .auth-header img {
        width: 72px;
        height: 72px;
        object-fit: contain;
        margin-bottom: .75rem;
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.4));
    }

    .auth-header h2 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #e0e0e0;
        margin: 0;
        letter-spacing: .5px;
    }

    .auth-body {
        padding: 2rem;
    }

    .auth-title {
        text-align: center;
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: 4px;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 1.75rem;
    }

    .auth-input-group {
        position: relative;
        margin-bottom: 1.25rem;
    }

    .auth-input-group .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #555;
        font-size: .85rem;
        z-index: 2;
    }

    .auth-input-group input {
        width: 100%;
        background: #111318;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        color: #e0e0e0;
        padding: .7rem 2.5rem .7rem 2.5rem;
        font-size: .9rem;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }

    .auth-input-group input:focus {
        border-color: #3d8bfd;
        box-shadow: 0 0 0 3px rgba(61, 139, 253, 0.15);
    }

    .auth-input-group input::placeholder {
        color: #444;
    }

    .auth-input-group .toggle-pw {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #555;
        cursor: pointer;
        font-size: .85rem;
        z-index: 2;
        transition: color .2s;
    }

    .auth-input-group .toggle-pw:hover {
        color: #aaa;
    }

    .auth-error {
        font-size: .75rem;
        color: #f87171;
        margin-top: .3rem;
        display: none;
    }

    .auth-input-group.has-error input {
        border-color: #f87171;
    }

    .auth-input-group.has-error .auth-error {
        display: block;
    }

    .auth-footer-row {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-top: .25rem;
    }

    .btn-auth {
        width: 100%;
        padding: .75rem;
        background: #3d8bfd;
        border: none;
        border-radius: 8px;
        color: #fff;
        font-weight: 600;
        font-size: .9rem;
        letter-spacing: .5px;
        cursor: pointer;
        transition: background .2s, transform .1s;
        margin-top: 1.25rem;
    }

    .btn-auth:hover {
        background: #2b7ae0;
    }

    .btn-auth:active {
        transform: scale(.98);
    }

    .btn-auth:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    #infoMessage {
        font-size: .82rem;
        text-align: center;
        border-radius: 8px;
        padding: .5rem .75rem;
        margin-bottom: 1rem;
        min-height: 0;
        transition: all .2s;
    }

    #infoMessage:empty {
        display: none;
    }
</style>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <img src="<?= $logo_app ?>" alt="Logo">
            <h2><?= $setting->nama_aplikasi ?></h2>
        </div>

        <div class="auth-body">
            <p class="auth-title">Login</p>

            <div id="infoMessage"><?= $message ?></div>

            <?= form_open("auth/cek_login", ['id' => 'login']) ?>

            <div class="auth-input-group" id="wrap-identity">
                <span class="input-icon fas fa-user"></span>
                <?= form_input($identity, '', 'required autocomplete="username"') ?>
                <div class="auth-error" id="err-identity"></div>
            </div>

            <div class="auth-input-group" id="wrap-password">
                <span class="input-icon fas fa-lock"></span>
                <?= form_input($password, '', 'required autocomplete="current-password"') ?>
                <span id="toggle-password" class="toggle-pw fas fa-eye-slash"></span>
                <div class="auth-error" id="err-password"></div>
            </div>

            <input type="hidden" name="cbt-only" id="cbt-only-hidden" value="1">

            <?= form_submit('submit', lang('login_submit_btn'), [
                'id'    => 'submit',
                'class' => 'btn-auth',
            ]) ?>

            <?= form_close() ?>
        </div>

    </div>
</div>

<script src="<?= base_url() ?>/assets/app/js/jquery.backstretch.js"></script>
<script>
    $(function() {
        const base_url = '<?= base_url() ?>';
        const imgs = ['wall1.jpg', 'wall2.png', 'wall3.jpg'];

        $.backstretch(imgs.map(i => base_url + 'assets/img/' + i), {
            fade: 1000,
            duration: 10000
        });

        // Toggle password visibility
        $('#toggle-password').on('click', function() {
            const input = $('#password');
            input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
            $(this).toggleClass('fa-eye-slash fa-eye');
        });

        // Clear error on input change
        $('form#login input').on('input', function() {
            const wrap = $(this).closest('.auth-input-group');
            wrap.removeClass('has-error');
            wrap.find('.auth-error').text('');
        });

        $('form#login').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const infobox = $('#infoMessage');
            const btnSubmit = $('#submit');

            infobox
                .removeAttr('class')
                .addClass('bg-gradient-info text-white')
                .text('Checking...');
            btnSubmit.attr('disabled', true).val('Wait...');

            // Preserve CBT-only flag in localStorage
            const cbtOnly = $('[name="cbt-only"]').val();
            localStorage.setItem('garudaCBT.login', cbtOnly === '1' ? '1' : '0');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(data) {
                    infobox.removeAttr('class').text('');
                    btnSubmit.removeAttr('disabled').val('<?= lang('login_submit_btn') ?>');

                    if (data.status) {
                        infobox.addClass('bg-gradient-success text-white').text('Login Sukses');
                        const isCbt = localStorage.getItem('garudaCBT.login') === '1';
                        let go = base_url + data.url;
                        if (isCbt && data.role === 'siswa') go = 'siswa/cbt';
                        window.location.href = go;
                    } else {
                        if (data.invalid) {
                            $.each(data.invalid, function(key, val) {
                                const input = $('[name="' + key + '"]');
                                const wrap = input.closest('.auth-input-group');
                                if (val) {
                                    wrap.addClass('has-error');
                                    wrap.find('.auth-error').text(val);
                                }
                            });
                        }
                        if (data.failed) {
                            infobox.addClass('bg-gradient-danger text-white').text(data.failed);
                        }
                    }
                }
            });
        });
    });
</script>
