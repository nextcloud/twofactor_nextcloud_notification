<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2022 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Migration;

use Closure;
use OCP\IConfig;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version3004Date20220331145316 extends SimpleMigrationStep {
	public function __construct(
		protected IConfig $config,
	) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	#[\Override]
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
		$keys = $this->config->getAppKeys('twofactor_nextcloud_notification');
		foreach ($keys as $key) {
			if (str_ends_with($key, '_enabled')) {
				$this->config->setUserValue(
					substr($key, 0, -8),
					'twofactor_nextcloud_notification',
					'enabled',
					'1'
				);

				$this->config->deleteAppValue('twofactor_nextcloud_notification', $key);
			}
		}
	}
}
