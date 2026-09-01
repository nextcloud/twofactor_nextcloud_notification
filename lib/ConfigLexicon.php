<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification;

use OCP\Config\Lexicon\Entry;
use OCP\Config\Lexicon\ILexicon;
use OCP\Config\Lexicon\Strictness;
use OCP\Config\ValueType;

/**
 * Config Lexicon for twofactor_nextcloud_notification.
 *
 * Please Add & Manage your Config Keys in that file and keep the Lexicon up to date!
 *
 * {@see ILexicon}
 */
class ConfigLexicon implements ILexicon {
	/** Whether the user activated this two-factor provider */
	public const USER_ENABLED = 'enabled';

	#[\Override]
	public function getStrictness(): Strictness {
		return Strictness::EXCEPTION;
	}

	#[\Override]
	public function getAppConfigs(): array {
		return [];
	}

	#[\Override]
	public function getUserConfigs(): array {
		return [
			new Entry(
				self::USER_ENABLED,
				ValueType::BOOL,
				defaultRaw: false,
				definition: 'Whether the user enabled two-factor authentication via Nextcloud notification',
				lazy: false,
			),
		];
	}
}
