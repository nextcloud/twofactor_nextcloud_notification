<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Db;

use OCP\AppFramework\Db\Entity;
use OCP\DB\Types;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getToken()
 * @method void setToken(string $token)
 * @method int getStatus()
 * @method void setStatus(int $status)
 * @method int getTimestamp()
 * @method void setTimestamp(int $timestamp)
 */
class Token extends Entity {
	public const PENDING = 0;
	public const ACCEPTED = 1;
	public const REJECTED = 2;

	/** @var string */
	protected $userId;
	/** @var string */
	protected $token;
	/** @var int */
	protected $status;
	/** @var int */
	protected $timestamp;

	public function __construct() {
		$this->addType('userId', Types::STRING);
		$this->addType('token', Types::STRING);
		$this->addType('status', Types::INTEGER);
		$this->addType('timestamp', Types::INTEGER);
	}
}
