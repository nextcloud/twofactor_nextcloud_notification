<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
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

	#[\Override]
	public function register(IRegistrationContext $context): void {
		$context->registerNotifierService(Notifier::class);
		$context->registerEventListener(StateChanged::class, RegistryUpdater::class);
	}

	#[\Override]
	public function boot(IBootContext $context): void {
	}
}
