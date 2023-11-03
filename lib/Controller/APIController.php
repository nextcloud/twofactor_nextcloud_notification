<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2018 Joas Schilling <coding@schilljs.com>
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

namespace OCA\TwoFactorNextcloudNotification\Controller;

use OCA\TwoFactorNextcloudNotification\Db\Token;
use OCA\TwoFactorNextcloudNotification\Exception\TokenExpireException;
use OCA\TwoFactorNextcloudNotification\Service\TokenManager;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\IRequest;

class APIController extends OCSController {
	public function __construct(
		string $appName,
		IRequest $request,
		private TokenManager $tokenManager,
		private ?string $userId = null,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @NoAdminRequired
	 * @param int $attemptId
	 * @return DataResponse
	 */
	public function approve(int $attemptId): DataResponse {
		try {
			$token = $this->tokenManager->getById($attemptId);
		} catch (DoesNotExistException $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		} catch (TokenExpireException $e) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}

		if ($token->getUserId() !== $this->userId) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}

		$token->setStatus(Token::ACCEPTED);
		$this->tokenManager->update($token);

		return new DataResponse([], Http::STATUS_ACCEPTED);
	}

	/**
	 * @NoAdminRequired
	 * @param int $attemptId
	 * @return DataResponse
	 */
	public function disapprove(int $attemptId): DataResponse {
		try {
			$token = $this->tokenManager->getById($attemptId);
		} catch (DoesNotExistException $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		} catch (TokenExpireException $e) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}

		if ($token->getUserId() !== $this->userId) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}

		$token->setStatus(Token::REJECTED);
		$this->tokenManager->update($token);

		return new DataResponse([], Http::STATUS_OK);
	}

	/**
	 * @PublicPage
	 * @param string $token
	 * @return DataResponse
	 */
	public function poll(string $token): DataResponse {
		try {
			$token = $this->tokenManager->getByToken($token);
		} catch (DoesNotExistException $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		} catch (TokenExpireException $e) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}

		if ($token->getStatus() === Token::PENDING) {
			return new DataResponse(['status' => 'pending']);
		}
		if ($token->getStatus() === Token::ACCEPTED) {
			return new DataResponse(['status' => 'accepted']);
		}
		if ($token->getStatus() === Token::REJECTED) {
			return new DataResponse(['status' => 'rejected']);
		}

		return new DataResponse([], Http::STATUS_FORBIDDEN);
	}
}
