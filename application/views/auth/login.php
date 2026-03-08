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
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.03);
    }

    .auth-header {
        background: rgba(255, 255, 255, 0.03);
        padding: 2rem 2rem 1.5rem;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .auth-header img {
        width: 72px;
        height: 72px;
        object-fit: contain;
        margin-bottom: .75rem;
        filter: drop-shadow(0 4px 16px rgba(0, 0, 0, 0.5));
    }

    .auth-header h2 {
        font-size: 1.05rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.85);
        margin: 0;
        letter-spacing: .3px;
    }

    .auth-body {
        padding: 2rem;
    }

    .auth-title {
        text-align: center;
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: 4px;
        color: rgba(255, 255, 255, 0.25);
        text-transform: uppercase;
        margin-bottom: 1.75rem;
    }

    .auth-input-group {
        position: relative;
        margin-bottom: 1.1rem;
    }

    .auth-input-group .input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.25);
        font-size: .82rem;
        z-index: 2;
        pointer-events: none;
    }

    .auth-input-group input {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: rgba(255, 255, 255, 0.85);
        padding: .72rem 2.5rem .72rem 2.5rem;
        font-size: .88rem;
        font-family: 'Lexend', sans-serif;
        transition: border-color .2s, box-shadow .2s, background .2s;
        outline: none;
    }

    .auth-input-group input:focus {
        background: rgba(99, 102, 241, 0.07);
        border-color: rgba(99, 102, 241, 0.5);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }

    .auth-input-group input::placeholder {
        color: rgba(255, 255, 255, 0.2);
    }

    .auth-input-group .toggle-pw {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.25);
        cursor: pointer;
        font-size: .82rem;
        z-index: 2;
        transition: color .2s;
    }

    .auth-input-group .toggle-pw:hover {
        color: rgba(255, 255, 255, 0.6);
    }

    .auth-error {
        font-size: .73rem;
        color: #f87171;
        margin-top: .3rem;
        display: none;
    }

    .auth-input-group.has-error input {
        border-color: rgba(248, 113, 113, 0.6);
        box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.1);
    }

    .auth-input-group.has-error .auth-error {
        display: block;
    }

    .btn-auth {
        width: 100%;
        padding: .75rem;
        background: rgba(99, 102, 241, 0.85);
        border: 1px solid rgba(99, 102, 241, 0.4);
        border-radius: 10px;
        color: #fff;
        font-family: 'Lexend', sans-serif;
        font-weight: 600;
        font-size: .88rem;
        letter-spacing: .3px;
        cursor: pointer;
        transition: background .2s, box-shadow .2s, transform .1s;
        margin-top: 1.25rem;
        backdrop-filter: blur(8px);
    }

    .btn-auth:hover {
        background: rgba(99, 102, 241, 1);
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.35);
    }

    .btn-auth:active {
        transform: scale(.98);
    }

    .btn-auth:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    #infoMessage {
        font-size: .8rem;
        text-align: center;
        border-radius: 8px;
        padding: .5rem .75rem;
        margin-bottom: 1rem;
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
        const imgs = 'wallpaper.png';

        $.backstretch(imgs.map(i => base_url + 'assets/img/' + i), {
            fade: 1000,
            duration: 10000
        });

        $('#toggle-password').on('click', function() {
            const input = $('#password');
            input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
            $(this).toggleClass('fa-eye-slash fa-eye');
        });

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

            infobox.removeAttr('class').addClass('bg-gradient-info text-white').text('Checking...');
            btnSubmit.attr('disabled', true).val('Wait...');

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
