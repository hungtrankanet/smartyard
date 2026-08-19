<?php
if (empty($roles)) {
    $roles = (new \App\Models\AuthModel())->getRoles();
}
?>
<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("add_user"); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('users'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-bars"></i>
                        <?= trans("users"); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('Admin/addUserPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("username"); ?> <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="username" class="form-control auth-form-input" placeholder="<?= trans("username"); ?>" value="<?= old("username"); ?>" required>
                    </div>
                    <div class="form-group">
                        <label><?= trans("email"); ?> <span style="color:#ef4444;">*</span></label>
                        <input type="email" name="email" class="form-control auth-form-input" placeholder="<?= trans("email"); ?>" value="<?= old("email"); ?>" required>
                    </div>
                    <div class="form-group">
                        <label><?= trans("password"); ?> <span style="color:#ef4444;">*</span></label>
                        <input type="password" name="password" class="form-control auth-form-input" placeholder="<?= trans("password"); ?>" value="<?= old("password"); ?>" required>
                    </div>
                    <div class="form-group">
                        <label><?= trans("role"); ?> (Vai Trò Người Dùng) <span style="color:#ef4444;">*</span></label>
                        <select name="role_id" class="form-control" required>
                            <option value=""><?= trans("select"); ?> vai trò...</option>
                            <?php if (!empty($roles)):
                                foreach ($roles as $role):
                                    $roleName = parseSerializedNameArray($role->role_name, $activeLang->id ?? 1);
                                    if (empty($roleName)) {
                                        $roleName = !empty($role->role_name) ? $role->role_name : 'Role #' . $role->id;
                                    } ?>
                                    <option value="<?= $role->id; ?>"><?= esc($roleName); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_user'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>