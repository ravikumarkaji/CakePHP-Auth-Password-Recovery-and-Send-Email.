<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<div class="row">
    <div class="large-4 large-offset-4">
        <?= $this->Form->create() ?>
        <fieldset>
            <legend><?= __('Already A member? Please Sign in') ?></legend>
            <?php
                echo $this->Form->control('email');
                echo $this->Form->control('password');
            ?>
            <a href="forgot-password">Forgot Password?</a>
            <a href="add" style="float: right">New Member?</a>
        </fieldset>
        <?= $this->Form->button(__('Log In')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
