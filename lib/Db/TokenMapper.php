<?php
declare(strict_types=1);

namespace OCA\TwoFactorNextcloudNotification\Db;

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Mapper;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\IDBConnection;
use OCP\Security\ISecureRandom;

class TokenMapper extends Mapper {

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
			->from(Application::APP_ID . '_tokens')
			->where(
				$qb->expr()->eq('token', $qb->createNamedParameter($token))
			);

		$cursor = $qb->execute();
		$data = $cursor->fetch();
		$cursor->closeCursor();

		if ($data === false) {
			throw new DoesNotExistException('Token not found');
		}

		return Token::fromRow($data);
	}

	/**
	 * @param int $id
	 * @return Token
	 * @throws DoesNotExistException
	 */
	public function getById(int $id): Token {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from(Application::APP_ID . '_tokens')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id))
			);

		$cursor = $qb->execute();
		$data = $cursor->fetch();
		$cursor->closeCursor();

		if ($data === false) {
			throw new DoesNotExistException('Token not found');
		}

		return Token::fromRow($data);
	}

	public function generate(string $userId): Token {
		$token = new Token();

		$token->setStatus(Token::PENDING);
		$token->setUserId($userId);
		$token->setTimestamp($this->timeFactory->getTime());
		$token->setToken($this->random->generate(40, ISecureRandom::CHAR_DIGITS.ISecureRandom::CHAR_LOWER.ISecureRandom::CHAR_UPPER));

		/** @var Token
		 * $token
		 */
		$token = $this->insert($token);
		return $token;
	}
}
