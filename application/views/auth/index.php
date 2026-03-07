{{FILE: index.php}}
<?php /* ============================================================
   auth/index.php
   ============================================================ */ ?>

<div class="auth-page-wrapper">
    <div class="auth-page-card auth-page-card--full">
        <div class="auth-page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h4><?= lang('index_heading') ?></h4>
                <p class="mb-0"><?= lang('index_subheading') ?></p>
            </div>
            <div class="d-flex gap-2">
                <?= anchor('auth/create_user',  lang('index_create_user_link'),  'class="btn-dark-primary btn-sm"') ?>
                <?= anchor('auth/create_group', lang('index_create_group_link'), 'class="btn-dark-secondary btn-sm"') ?>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="auth-alert mx-3"><?= $message ?></div>
        <?php endif ?>

        <div class="auth-page-body p-0">
            <div class="table-responsive">
                <table class="auth-table">
                    <thead>
                        <tr>
                            <th><?= lang('index_fname_th') ?></th>
                            <th><?= lang('index_lname_th') ?></th>
                            <th><?= lang('index_email_th') ?></th>
                            <th><?= lang('index_groups_th') ?></th>
                            <th><?= lang('index_status_th') ?></th>
                            <th><?= lang('index_action_th') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user->first_name, ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user->last_name, ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php foreach ($user->groups as $group): ?>
                                        <?= anchor("auth/edit_group/{$group->id}", htmlspecialchars($group->name, ENT_QUOTES, 'UTF-8'), 'class="badge-group"') ?>
                                    <?php endforeach ?>
                                </td>
                                <td>
                                    <?php if ($user->active): ?>
                                        <?= anchor("auth/deactivate/{$user->id}", lang('index_active_link'), 'class="badge-status badge-active"') ?>
                                    <?php else: ?>
                                        <?= anchor("auth/activate/{$user->id}", lang('index_inactive_link'), 'class="badge-status badge-inactive"') ?>
                                    <?php endif ?>
                                </td>
                                <td>
                                    <?= anchor("auth/edit_user/{$user->id}", 'Edit', 'class="btn-dark-primary btn-xs"') ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
