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
            <legend><?= __('Please Send Your Email ID to get Password reset instruction!') ?></legend>
            <?php
                echo $this->Form->control('email');
                echo $this->Html->link('Back',['Controller'=>'Users','action'=>'login']);
            ?>
            
        </fieldset>
        <?= $this->Form->button(__('Send')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
