<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Service;

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCA\TwoFactorNextcloudNotification\Db\Token;
use OCP\IRequest;
use OCP\Notification\IManager;

class NotificationManager {
	public function __construct(
		private IManager $manager,
		private IRequest $request,
	) {
	}

	public function clearNotification(Token $token): void {
		$notification = $this->manager->createNotification();
		$notification->setApp(Application::APP_ID)
			->setSubject('login_attempt')
			->setObject('2fa_id', (string)$token->getId());
		$this->manager->markProcessed($notification);
	}

	public function newNotification(Token $token): void {
		$notification = $this->manager->createNotification();
		$notification->setApp(Application::APP_ID)
			->setSubject('login_attempt', [
				'ip' => $this->request->getRemoteAddress(),
			])
			->setObject('2fa_id', (string)$token->getId())
			->setUser($token->getUserId())
			->setDateTime(new \DateTime());
		$this->manager->notify($notification);
	}
}
