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
            <legend><?= __('Have a query? Contact Us.') ?></legend>
            <?php
                echo $this->Form->control('name');
                echo $this->Form->control('email');
                echo $this->Form->control('subject');
                echo $this->Form->control('message',['type'=>'textarea']);
            ?>
            
        </fieldset>
        <?= $this->Form->button(__('Send')) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
