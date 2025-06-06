<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\BackgroundJob;

use OCA\TwoFactorNextcloudNotification\Service\TokenManager;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\TimedJob;

class CleanupTokens extends TimedJob {
	public function __construct(
		ITimeFactory $timeFactory,
		private TokenManager $tokenManager,
	) {
		parent::__construct($timeFactory);

		// Run once an hour
		$this->setInterval(3600);
	}

	#[\Override]
	protected function run($argument) {
		$this->tokenManager->cleanupTokens();
	}
}
