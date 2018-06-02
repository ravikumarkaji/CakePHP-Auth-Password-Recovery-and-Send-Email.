<?php
/**
*$var
*
**/
?>
<p>Dear <?php echo $user->email; ?>,</p>

<p>You may change your password using the link below.</p>
<?php $url = 'http://' . env('SERVER_NAME') . '/cakephp-login-logout-forgot-password/users/reset-password/' . $user->reset_password_token; ?>
<p><?php echo $this->Html->link($url, $url); ?></p>

<p>Your password won't change until you access the link above and create a new one.</p>
<p>Thanks and have a nice day!</p>