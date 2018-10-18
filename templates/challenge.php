<?php
script('twofactor_nextcloud_notification', 'challenge');
?>

<input type="hidden" id="challenge-poll-token" value="<?php p($_['token']); ?>">

<form method="POST" class="2fa-notification-form" id="twofactor-form">
	<input type="hidden" name="challenge" value="<?php p($_['token']); ?>">
	<button type="submit">
		<span><?php p($l->t('Continue!')); ?></span>
	</button>
	<p><?php p($l->t('Please accept the request on one of your logged in devices.')) ?></p>
</form>
