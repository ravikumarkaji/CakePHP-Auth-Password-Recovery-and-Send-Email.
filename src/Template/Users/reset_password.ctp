<?php
/**
*@var
**/
?>
<div class="row">
    <div class="large-4 large-offset-4">
        <?= $this->Form->create() ?>
        <fieldset>
            <legend><?= __('Change Your Password') ?></legend>
            	<?php
            		echo $this->Form->hidden('reset_password_token',['value'=>$reset_password_token]);
            		echo $this->Form->control('new_password');
            		echo $this->Form->control('confirm_password');
            	?>
        </fieldset>
        <?= $this->Form->button(__('Change Password')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>

<h1></h1>
