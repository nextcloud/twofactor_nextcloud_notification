<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2018, Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

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
