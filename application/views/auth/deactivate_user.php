{{FILE: deactivate_user.php}}
<?php /* ============================================================
   auth/deactivate_user.php
   ============================================================ */ ?>

<div class="auth-page-wrapper">
    <div class="auth-page-card">
        <div class="auth-page-header">
            <h4><?= lang('deactivate_heading') ?></h4>
            <p><?= sprintf(lang('deactivate_subheading'), $user->username) ?></p>
        </div>
        <div class="auth-page-body">

            <?= form_open("auth/deactivate/" . $user->id) ?>

            <div class="form-field">
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="confirm" value="yes" checked="checked">
                        <span><?= lang('deactivate_confirm_y_label') ?></span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="confirm" value="no">
                        <span><?= lang('deactivate_confirm_n_label') ?></span>
                    </label>
                </div>
            </div>

            <?= form_hidden($csrf) ?>
            <?= form_hidden(['id' => $user->id]) ?>

            <div class="form-actions">
                <?= form_submit('submit', lang('deactivate_submit_btn'), ['class' => 'btn-dark-danger']) ?>
            </div>

            <?= form_close() ?>

        </div>
    </div>
</div>

