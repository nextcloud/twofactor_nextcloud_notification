<?php
declare(strict_types=1);

namespace OCA\TwoFactorNextcloudNotification\Db;

use OCP\AppFramework\Db\Entity;

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

	const PENDING = 0;
	const ACCEPTED = 1;
	const REJECTED = 2;

	/** @var string */
	protected $userId;
	/** @var string */
	protected $token;
	/** @var int */
	protected $status;
	/** @var int */
	protected $timestamp;

	public function __construct() {
		$this->addType('userId', 'string');
		$this->addType('token', 'string');
		$this->addType('status', 'int');
		$this->addType('timestamp', 'int');
	}

}
