<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Service;

use OCA\TwoFactorNextcloudNotification\Db\Token;
use OCA\TwoFactorNextcloudNotification\Db\TokenMapper;
use OCA\TwoFactorNextcloudNotification\Exception\TokenExpireException;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Utility\ITimeFactory;

class TokenManager {
	public function __construct(
		private TokenMapper $mapper,
		private NotificationManager $notificationManager,
		private ITimeFactory $timeFactory,
	) {
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
	public function delete(Token $token): void {
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

	public function cleanupTokens(): void {
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
		if (($this->timeFactory->getTime() - $token->getTimestamp()) > 60 * 10) {
			$this->delete($token);
			throw new TokenExpireException('Token expired');
		}

		return $token;
	}
}
