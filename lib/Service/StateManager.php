<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Service;

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCA\TwoFactorNextcloudNotification\Event\StateChanged;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IConfig;
use OCP\IUser;

class StateManager {
	public function __construct(
		private IEventDispatcher $dispatcher,
		private IConfig $config,
	) {
	}

	public function setState(IUser $user, bool $state): void {
		$this->config->setUserValue($user->getUID(), Application::APP_ID, 'enabled', $state ? '1' : '0');
		$this->dispatcher->dispatchTyped(new StateChanged($user, $state));
	}

	public function getState(IUser $user): bool {
		return $this->config->getUserValue($user->getUID(), Application::APP_ID, 'enabled', '0') === '1';
	}
}
