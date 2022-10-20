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

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCA\TwoFactorNextcloudNotification\Db\Token;
use OCP\IRequest;
use OCP\Notification\IManager;

class NotificationManager {
	/** @var IManager */
	private $manager;

	/** @var IRequest */
	private $request;

	public function __construct(IManager $manager, IRequest $request) {
		$this->manager = $manager;
		$this->request = $request;
	}

	public function clearNotification(Token $token) {
		$notification = $this->manager->createNotification();
		$notification->setApp(Application::APP_ID)
			->setSubject('login_attempt')
			->setObject('2fa_id', (string)$token->getId());
		$this->manager->markProcessed($notification);
	}

	public function newNotification(Token $token) {
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
