<?php
script('twofactor_nextcloud_notification', 'twofactor_nextcloud_notification-challenge');
?>

<input type="hidden" id="challenge-poll-token" value="<?php p($_['token']); ?>">

<form method="POST" class="2fa-notification-form" id="twofactor-form">
	<input type="hidden" name="challenge" value="<?php p($_['token']); ?>">
	<div id="twofactor-notification-challenge"></div>
</form>
