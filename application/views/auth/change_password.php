{{FILE: change_password.php}}
<?php /* ============================================================
   auth/change_password.php
   ============================================================ */ ?>

<div class="auth-page-wrapper">
    <div class="auth-page-card">
        <div class="auth-page-header">
            <h4><?= lang('change_password_heading') ?></h4>
        </div>
        <div class="auth-page-body">

            <?php if ($message): ?>
                <div class="auth-alert"><?= $message ?></div>
            <?php endif ?>

            <?= form_open("auth/change_password") ?>

            <div class="form-field">
                <?= lang('change_password_old_password_label', 'old_password') ?>
                <?= form_input($old_password) ?>
            </div>

            <div class="form-field">
                <label for="new_password">
                    <?= sprintf(lang('change_password_new_password_label'), $min_password_length) ?>
                </label>
                <?= form_input($new_password) ?>
            </div>

            <div class="form-field">
                <?= lang('change_password_new_password_confirm_label', 'new_password_confirm') ?>
                <?= form_input($new_password_confirm) ?>
            </div>

            <?= form_input($user_id) ?>

            <div class="form-actions">
                <?= form_submit('submit', lang('change_password_submit_btn'), ['class' => 'btn-dark-primary']) ?>
            </div>

            <?= form_close() ?>

        </div>
    </div>
</div>

