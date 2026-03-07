{{FILE: edit_user.php}}
<?php /* ============================================================
   auth/edit_user.php
   ============================================================ */ ?>

<div class="auth-page-wrapper">
    <div class="auth-page-card auth-page-card--wide">
        <div class="auth-page-header">
            <h4><?= lang('edit_user_heading') ?></h4>
            <p><?= lang('edit_user_subheading') ?></p>
        </div>
        <div class="auth-page-body">

            <?php if ($message): ?>
                <div class="auth-alert"><?= $message ?></div>
            <?php endif ?>

            <?= form_open(uri_string()) ?>

            <div class="form-grid">

                <div class="form-field">
                    <?= lang('edit_user_fname_label', 'first_name') ?>
                    <?= form_input($first_name) ?>
                </div>

                <div class="form-field">
                    <?= lang('edit_user_lname_label', 'last_name') ?>
                    <?= form_input($last_name) ?>
                </div>

                <div class="form-field">
                    <?= lang('edit_user_company_label', 'company') ?>
                    <?= form_input($company) ?>
                </div>

                <div class="form-field">
                    <?= lang('edit_user_phone_label', 'phone') ?>
                    <?= form_input($phone) ?>
                </div>

                <div class="form-field">
                    <?= lang('edit_user_password_label', 'password') ?>
                    <?= form_input($password) ?>
                </div>

                <div class="form-field">
                    <?= lang('edit_user_password_confirm_label', 'password_confirm') ?>
                    <?= form_input($password_confirm) ?>
                </div>

            </div>

            <?php if ($this->ion_auth->is_admin()): ?>
                <div class="form-section-divider">
                    <h5><?= lang('edit_user_groups_heading') ?></h5>
                    <div class="checkbox-group">
                        <?php foreach ($groups as $group):
                            $checked = null;
                            foreach ($currentGroups as $grp) {
                                if ($group['id'] == $grp->id) {
                                    $checked = 'checked';
                                    break;
                                }
                            }
                        ?>
                            <label class="checkbox-option">
                                <input type="checkbox" name="groups[]" value="<?= $group['id'] ?>" <?= $checked ?>>
                                <span><?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?></span>
                            </label>
                        <?php endforeach ?>
                    </div>
                </div>
            <?php endif ?>

            <?= form_hidden('id', $user->id) ?>
            <?= form_hidden($csrf) ?>

            <div class="form-actions">
                <?= form_submit('submit', lang('edit_user_submit_btn'), ['class' => 'btn-dark-primary']) ?>
            </div>

            <?= form_close() ?>

        </div>
    </div>
</div>

