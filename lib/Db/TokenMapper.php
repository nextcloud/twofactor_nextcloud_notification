<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IDBConnection;
use OCP\Security\ISecureRandom;

/**
 * @template-extends QBMapper<Token>
 */
class TokenMapper extends QBMapper {
	public function __construct(
		IDBConnection $db,
		private ITimeFactory $timeFactory,
		private ISecureRandom $random,
	) {
		parent::__construct($db, 'twofactor_tnn_tokens', Token::class);
	}

	/**
	 * @param string $token
	 * @return Token
	 * @throws DoesNotExistException
	 */
	public function getByToken(string $token): Token {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('token', $qb->createNamedParameter($token))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param int $id
	 * @return Token
	 * @throws DoesNotExistException
	 */
	public function getById(int $id): Token {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id))
			);

		return $this->findEntity($qb);
	}

	public function generate(string $userId): Token {
		$token = new Token();

		$token->setStatus(Token::PENDING);
		$token->setUserId($userId);
		$token->setTimestamp($this->timeFactory->getTime());
		$token->setToken($this->random->generate(40, ISecureRandom::CHAR_DIGITS . ISecureRandom::CHAR_LOWER . ISecureRandom::CHAR_UPPER));

		/** @var Token $token */
		$token = $this->insert($token);
		return $token;
	}

	public function getTokensForCleanup(): array {
		// Clear all tokens older than 10 minutes
		$time = $this->timeFactory->getTime() - (60 * 10);

		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->lt('timestamp', $qb->createNamedParameter($time)));

		return $this->findEntities($qb);
	}
}
