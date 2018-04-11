<?php
declare(strict_types=1);
/**
 * @author Joas Schilling <coding@schilljs.com>
 *
 * @copyright Copyright (c) 2018, Joas Schilling <coding@schilljs.com>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactorNextcloudNotification\AppInfo;

use OCA\TwoFactorNextcloudNotification\Notification\Notifier;
use OCP\AppFramework\App;

class Application extends App {
	const APP_ID = 'twofactor_nextcloud_notification';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register() {
		$notificationManager = $this->getContainer()->getServer()->getNotificationManager();
		$notificationManager->registerNotifier(function() {
			return $this->getContainer()->getServer()->query(Notifier::class);
		}, function() {
			$l = $this->getContainer()->getServer()->getL10N(self::APP_ID);
			return [
				'id' => self::APP_ID,
				'name' => $l->t('TwoFactor Nextcloud notification'),
			];
		});
	}
}
