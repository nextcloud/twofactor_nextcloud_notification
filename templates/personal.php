<?php
script('twofactor_nextcloud_notification', 'settings');
?>

<input type="hidden" id="twofactor-notifications-initial-state" value="<?php p($_['enabled'] ? 'true' : 'false'); ?>">

<div id="twofactor-notification-settings"></div>
