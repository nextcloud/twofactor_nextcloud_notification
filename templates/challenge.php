<?php
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
script('twofactor_nextcloud_notification', 'twofactor_nextcloud_notification-challenge');
?>

<input type="hidden" id="challenge-poll-token" value="<?php p($_['token']); ?>">

<form method="POST" class="2fa-notification-form" id="twofactor-form">
	<input type="hidden" name="challenge" value="<?php p($_['token']); ?>">
	<div id="twofactor-notification-challenge"></div>
</form>
