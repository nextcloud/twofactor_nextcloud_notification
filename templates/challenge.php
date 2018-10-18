<?php
script('twofactor_nextcloud_notification', 'challenge');
?>

<input type="hidden" id="challenge-poll-token" value="<?php p($_['token']); ?>">

<form method="POST" class="2fa-notification-form" id="twofactor-form">
	<input type="hidden" name="challenge" value="<?php p($_['token']); ?>">
	<p><?php p($l->t('Please accept the request on one of your logged in devices.')) ?></p>
	<p>
		<small><?php p($l->t('You will be redirected automatically once this login has been accepted.')) ?></small>
	</p>
</form>
