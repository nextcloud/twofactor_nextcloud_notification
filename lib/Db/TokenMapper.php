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

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IDBConnection;
use OCP\Security\ISecureRandom;

class TokenMapper extends QBMapper {

	/** @var ITimeFactory */
	private $timeFactory;
	/** @var ISecureRandom */
	private $random;

	public function __construct(IDBConnection $db,
								ITimeFactory $timeFactory,
								ISecureRandom $random) {
		parent::__construct($db, Application::APP_ID . '_tokens', Token::class);

		$this->timeFactory = $timeFactory;
		$this->random = $random;
	}

	/**
	 * @param string $token
	 * @return Token
	 * @throws DoesNotExistException
	 */
	public function getBytoken(string $token): Token {
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
		$token->setToken($this->random->generate(40, ISecureRandom::CHAR_DIGITS.ISecureRandom::CHAR_LOWER.ISecureRandom::CHAR_UPPER));

		/** @var Token $token */
		$token = $this->insert($token);
		return $token;
	}

	public function cleanupTokens() {
		// Clear all tokens older than 10 minutes
		$time = $this->timeFactory->getTime() - (60 * 10);

		$qb = $this->db->getQueryBuilder();
		$qb->delete($this->getTableName())
			->where($qb->expr()->lt('timestamp', $qb->createNamedParameter($time)));
		$qb->execute();
	}
}
