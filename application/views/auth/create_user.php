{{FILE: create_user.php}}
<?php /* ============================================================
   auth/create_user.php
   ============================================================ */ ?>

<div class="auth-page-wrapper">
    <div class="auth-page-card auth-page-card--wide">
        <div class="auth-page-header">
            <h4><?= lang('create_user_heading') ?></h4>
            <p><?= lang('create_user_subheading') ?></p>
        </div>
        <div class="auth-page-body">

            <?php if ($message): ?>
                <div class="auth-alert"><?= $message ?></div>
            <?php endif ?>

            <?= form_open("auth/create_user") ?>

            <div class="form-grid">

                <div class="form-field">
                    <?= lang('create_user_fname_label', 'first_name') ?>
                    <?= form_input($first_name) ?>
                </div>

                <div class="form-field">
                    <?= lang('create_user_lname_label', 'last_name') ?>
                    <?= form_input($last_name) ?>
                </div>

                <?php if ($identity_column !== 'email'): ?>
                    <div class="form-field">
                        <?= lang('create_user_identity_label', 'identity') ?>
                        <?= form_error('identity') ?>
                        <?= form_input($identity) ?>
                    </div>
                <?php endif ?>

                <div class="form-field">
                    <?= lang('create_user_company_label', 'company') ?>
                    <?= form_input($company) ?>
                </div>

                <div class="form-field">
                    <?= lang('create_user_email_label', 'email') ?>
                    <?= form_input($email) ?>
                </div>

                <div class="form-field">
                    <?= lang('create_user_phone_label', 'phone') ?>
                    <?= form_input($phone) ?>
                </div>

                <div class="form-field">
                    <?= lang('create_user_password_label', 'password') ?>
                    <?= form_input($password) ?>
                </div>

                <div class="form-field">
                    <?= lang('create_user_password_confirm_label', 'password_confirm') ?>
                    <?= form_input($password_confirm) ?>
                </div>

            </div>

            <div class="form-actions">
                <?= form_submit('submit', lang('create_user_submit_btn'), ['class' => 'btn-dark-primary']) ?>
            </div>

            <?= form_close() ?>

        </div>
    </div>
</div>

