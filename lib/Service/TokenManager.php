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

namespace OCA\TwoFactorNextcloudNotification\Service;

use OCA\TwoFactorNextcloudNotification\Db\Token;
use OCA\TwoFactorNextcloudNotification\Db\TokenMapper;
use OCA\TwoFactorNextcloudNotification\Exception\TokenExpireException;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Utility\ITimeFactory;

class TokenManager {

	/** @var TokenMapper */
	private $mapper;

	/** @var NotificationManager */
	private $notificationManager;

	/** @var ITimeFactory */
	private $timeFactory;

	public function __construct(TokenMapper $mapper,
								NotificationManager $notificationManager,
								ITimeFactory $timeFactory) {
		$this->mapper = $mapper;
		$this->notificationManager = $notificationManager;
		$this->timeFactory = $timeFactory;
	}

	/**
	 * @param int $attemptId
	 * @return Token
	 * @throws DoesNotExistException
	 * @throws TokenExpireException
	 */
	public function getById(int $attemptId): Token {
		return $this->validateToken($this->mapper->getById($attemptId));
	}

	/**
	 * @param string $token
	 * @return Token
	 * @throws DoesNotExistException
	 * @throws TokenExpireException
	 */
	public function getByToken(string $token): Token {
		return $this->validateToken($this->mapper->getByToken($token));
	}

	/**
	 * @param Token $token
	 */
	public function delete(Token $token) {
		$this->notificationManager->clearNotification($token);
		$this->mapper->delete($token);
	}

	/**
	 * @param Token $token
	 * @return Token
	 */
	public function update(Token $token): Token {
		$this->notificationManager->clearNotification($token);
		return $this->mapper->update($token);
	}

	public function cleanupTokens() {
		$tokens = $this->mapper->getTokensForCleanup();

		foreach ($tokens as $token) {
			$this->delete($token);
		}
	}

	public function generate(string $userId): Token {
		$token = $this->mapper->generate($userId);
		$this->notificationManager->newNotification($token);
		return $token;
	}

	/**
	 * @param Token $token
	 * @return Token
	 * @throws TokenExpireException
	 */
	protected function validateToken(Token $token): Token {
		if (($this->timeFactory->getTime() - $token->getTimestamp()) > 60*10) {
			$this->delete($token);
			throw new TokenExpireException('Token expired');
		}

		return $token;
	}
}
