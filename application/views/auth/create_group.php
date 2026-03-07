{{FILE: create_group.php}}
<?php /* ============================================================
   auth/create_group.php
   ============================================================ */ ?>

<div class="auth-page-wrapper">
    <div class="auth-page-card">
        <div class="auth-page-header">
            <h4><?= lang('create_group_heading') ?></h4>
            <p><?= lang('create_group_subheading') ?></p>
        </div>
        <div class="auth-page-body">

            <?php if ($message): ?>
                <div class="auth-alert"><?= $message ?></div>
            <?php endif ?>

            <?= form_open("auth/create_group") ?>

            <div class="form-field">
                <?= lang('create_group_name_label', 'group_name') ?>
                <?= form_input($group_name) ?>
            </div>

            <div class="form-field">
                <?= lang('create_group_desc_label', 'description') ?>
                <?= form_input($description) ?>
            </div>

            <div class="form-actions">
                <?= form_submit('submit', lang('create_group_submit_btn'), ['class' => 'btn-dark-primary']) ?>
            </div>

            <?= form_close() ?>

        </div>
    </div>
</div>

