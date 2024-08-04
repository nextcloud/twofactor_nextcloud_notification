<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Settings;

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCP\AppFramework\Services\IInitialState;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Template;

class Personal implements IPersonalProviderSettings {
	public function __construct(
		private IInitialState $initialStateService,
		private bool $enabled,
	) {
	}

	public function getBody(): Template {
		$this->initialStateService->provideInitialState('state', $this->enabled);
		return new Template(Application::APP_ID, 'personal');
	}
}
