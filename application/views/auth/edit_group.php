{{FILE: edit_group.php}}
<?php /* ============================================================
   auth/edit_group.php
   ============================================================ */ ?>

<div class="auth-page-wrapper">
    <div class="auth-page-card">
        <div class="auth-page-header">
            <h4><?= lang('edit_group_heading') ?></h4>
            <p><?= lang('edit_group_subheading') ?></p>
        </div>
        <div class="auth-page-body">

            <?php if ($message): ?>
                <div class="auth-alert"><?= $message ?></div>
            <?php endif ?>

            <?= form_open(current_url()) ?>

            <div class="form-field">
                <?= lang('edit_group_name_label', 'group_name') ?>
                <?= form_input($group_name) ?>
            </div>

            <div class="form-field">
                <?= lang('edit_group_desc_label', 'description') ?>
                <?= form_input($group_description) ?>
            </div>

            <div class="form-actions">
                <?= form_submit('submit', lang('edit_group_submit_btn'), ['class' => 'btn-dark-primary']) ?>
            </div>

            <?= form_close() ?>

        </div>
    </div>
</div>

