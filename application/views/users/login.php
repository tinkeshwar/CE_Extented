<div class="row mt-4">
    <div class="col-md-12">
        <?= form_open(base_url('users/login'), ['class' => 'form-horizontal']) ?>
        <?= form_fieldset('Login Form') ?>
        <div class="form-inline form-group">
            <?= form_label('Username', 'username', ['class' => 'col-form-label text-left col-md-2']) ?>
            <?= form_input(['name' => 'username', 'type' => 'text', 'class' => 'form-control col-md-10']) ?>
        </div>
        <div class="form-inline form-group">
            <?= form_label('Password', 'password', ['class' => 'col-form-label text-left col-md-2']) ?>
            <?= form_input(['name' => 'password', 'type' => 'password', 'class' => 'form-control col-md-10']) ?>
        </div>
        <?= form_submit('addForm', 'Login!', ['class' => 'btn btn-primary']); ?>
    </div>
</div>
