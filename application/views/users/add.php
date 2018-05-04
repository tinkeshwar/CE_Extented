<div class="row mt-4">
    <div class="col-md-12">
        <?= form_open(base_url('users/add'), ['class' => 'form-horizontal']) ?>
        <?= form_fieldset('Add User Form') ?>
        <div class="form-inline form-group">
            <?= form_label('Username', 'username', ['class' => 'col-form-label text-left col-md-2']) ?>
            <?= form_input(['name' => 'username', 'type' => 'text', 'class' => 'form-control col-md-10']) ?>
        </div>
        <div class="form-inline form-group">
            <?= form_label('Password', 'password', ['class' => 'col-form-label text-left col-md-2']) ?>
            <?= form_input(['name' => 'password', 'type' => 'password', 'class' => 'form-control col-md-10']) ?>
        </div>
        <div class="form-inline form-group">
            <?= form_label('Confirm Password', 'confirm_password', ['class' => 'col-form-label text-left col-md-2']) ?>
            <?= form_input(['name' => 'cpassword', 'type' => 'password', 'class' => 'form-control col-md-10']) ?>
        </div>
        <?= form_submit('addForm', 'Submit!', ['class' => 'btn btn-primary']); ?>
    </div>
</div>
