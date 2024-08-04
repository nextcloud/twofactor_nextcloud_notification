<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Controller;

use OCA\TwoFactorNextcloudNotification\Db\Token;
use OCA\TwoFactorNextcloudNotification\Exception\TokenExpireException;
use OCA\TwoFactorNextcloudNotification\Service\TokenManager;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\PublicPage;
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

	#[NoAdminRequired]
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

	#[NoAdminRequired]
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

	#[PublicPage]
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
