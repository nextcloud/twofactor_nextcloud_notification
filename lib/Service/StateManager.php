<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Service;

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCA\TwoFactorNextcloudNotification\Event\StateChanged;
use OCP\Config\IUserConfig;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IUser;

class StateManager {
	public function __construct(
		private readonly IEventDispatcher $dispatcher,
		private readonly IUserConfig $userConfig,
	) {
	}

	public function setState(IUser $user, bool $state): void {
		$this->userConfig->setValueBool($user->getUID(), Application::APP_ID, 'enabled', $state);
		$this->dispatcher->dispatchTyped(new StateChanged($user, $state));
	}

	public function getState(IUser $user): bool {
		return $this->userConfig->getValueBool($user->getUID(), Application::APP_ID, 'enabled');
	}
}
