<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Migration;

use Closure;
use OCP\Config\IUserConfig;
use OCP\IAppConfig;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version3004Date20220331145316 extends SimpleMigrationStep {
	public function __construct(
		private readonly IAppConfig $appConfig,
		private readonly IUserConfig $userConfig,
	) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	#[\Override]
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
		$keys = $this->appConfig->getKeys('twofactor_nextcloud_notification');
		foreach ($keys as $key) {
			if (str_ends_with($key, '_enabled')) {
				$this->userConfig->setValueBool(
					substr($key, 0, -8),
					'twofactor_nextcloud_notification',
					'enabled',
					true
				);

				$this->appConfig->deleteKey('twofactor_nextcloud_notification', $key);
			}
		}
	}
}
