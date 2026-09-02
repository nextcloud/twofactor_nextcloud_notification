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
use OCP\AppFramework\Http\Attribute\ApiRoute;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoTwoFactorRequired;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\OCSController;
use OCP\IRequest;

class APIController extends OCSController {
	public function __construct(
		string $appName,
		IRequest $request,
		private readonly TokenManager $tokenManager,
		private readonly ?string $userId = null,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * Approve a login attempt
	 *
	 * @param int $attemptId ID of the login attempt
	 * @return DataResponse<Http::STATUS_ACCEPTED|Http::STATUS_FORBIDDEN|Http::STATUS_NOT_FOUND, list<empty>, array{}>
	 *
	 * 202: Login attempt approved
	 * 403: Login attempt belongs to another user or is expired
	 * 404: Login attempt not found
	 */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'POST', url: '/api/{apiVersion}/attempt/{attemptId}', requirements: [
		'apiVersion' => '(v1)',
		'attemptId' => '\d+',
	])]
	public function approve(int $attemptId): DataResponse {
		try {
			$token = $this->tokenManager->getById($attemptId);
		} catch (DoesNotExistException) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		} catch (TokenExpireException) {
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
	 * Disapprove a login attempt
	 *
	 * @param int $attemptId ID of the login attempt
	 * @return DataResponse<Http::STATUS_OK|Http::STATUS_FORBIDDEN|Http::STATUS_NOT_FOUND, list<empty>, array{}>
	 *
	 * 200: Login attempt disapproved
	 * 403: Login attempt belongs to another user or is expired
	 * 404: Login attempt not found
	 */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'DELETE', url: '/api/{apiVersion}/attempt/{attemptId}', requirements: [
		'apiVersion' => '(v1)',
		'attemptId' => '\d+',
	])]
	public function disapprove(int $attemptId): DataResponse {
		try {
			$token = $this->tokenManager->getById($attemptId);
		} catch (DoesNotExistException) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		} catch (TokenExpireException) {
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
	 * Get the state of a login attempt
	 *
	 * @param string $token Token of the login attempt
	 * @return DataResponse<Http::STATUS_OK, array{status: 'pending'|'accepted'|'rejected'}, array{}>|DataResponse<Http::STATUS_FORBIDDEN|Http::STATUS_NOT_FOUND, list<empty>, array{}>
	 *
	 * 200: State of the login attempt returned
	 * 403: Login attempt is expired or in an unknown state
	 * 404: Login attempt not found
	 */
	#[NoTwoFactorRequired]
	#[PublicPage]
	#[ApiRoute(verb: 'GET', url: '/api/{apiVersion}/poll/{token}', requirements: [
		'apiVersion' => '(v1)',
		'token' => '[a-zA-Z0-9]{40}',
	])]
	public function poll(string $token): DataResponse {
		try {
			$token = $this->tokenManager->getByToken($token);
		} catch (DoesNotExistException) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		} catch (TokenExpireException) {
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
