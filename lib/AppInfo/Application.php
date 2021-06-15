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

use OCA\TwoFactorNextcloudNotification\Event\StateChanged;
use OCA\TwoFactorNextcloudNotification\Listener\RegistryUpdater;
use OCA\TwoFactorNextcloudNotification\Notification\Notifier;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
	public const APP_ID = 'twofactor_nextcloud_notification';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(StateChanged::class, RegistryUpdater::class);
	}

	public function boot(IBootContext $context): void {
		$notificationManager = $context->getServerContainer()->getNotificationManager();
		$notificationManager->registerNotifierService(Notifier::class);
	}
}
