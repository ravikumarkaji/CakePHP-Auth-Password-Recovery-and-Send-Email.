<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<div class="row">
     <div class="large-4 large-offset-4">
        <?= $this->Form->create($user) ?>
        <fieldset>
            <legend><?= __('New Member? Please SignUp') ?></legend>
            <?php
                echo $this->Form->control('username');
                echo $this->Form->control('email');
                echo $this->Form->control('password');
            ?>
            <a href="login">Already A member? Sign In</a>
        </fieldset>
        <?= $this->Form->button(__('SignUp')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
