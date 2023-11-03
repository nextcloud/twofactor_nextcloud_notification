<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2018, Joas Schilling <coding@schilljs.com>
 *
 * @author Joas Schilling <coding@schilljs.com>
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

namespace OCA\TwoFactorNextcloudNotification\Notification;

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;

class Notifier implements INotifier {
	public function __construct(
		protected IFactory $l10nFactory,
		protected IURLGenerator $urlGenerator,
	) {
	}

	/**
	 * Identifier of the notifier, only use [a-z0-9_]
	 *
	 * @return string
	 * @since 17.0.0
	 */
	public function getID(): string {
		return Application::APP_ID;
	}

	/**
	 * Human-readable name describing the notifier
	 *
	 * @return string
	 * @since 17.0.0
	 */
	public function getName(): string {
		return $this->l10nFactory->get(Application::APP_ID)->t('TwoFactor Nextcloud notification');
	}

	/**
	 * @param INotification $notification
	 * @param string $languageCode The code of the language that should be used to prepare the notification
	 * @return INotification
	 * @throws \InvalidArgumentException When the notification was not prepared by a notifier
	 */
	public function prepare(INotification $notification, string $languageCode): INotification {
		if ($notification->getApp() !== Application::APP_ID ||
			$notification->getSubject() !== 'login_attempt') {
			throw new \InvalidArgumentException('Unhandled app or subject');
		}

		$l = $this->l10nFactory->get(Application::APP_ID, $languageCode);
		$attemptId = $notification->getObjectId();
		$param = $notification->getSubjectParameters();

		$approveAction = $notification->createAction()
			->setParsedLabel($l->t('Approve'))
			->setPrimary(true)
			->setLink(
				$this->urlGenerator->linkToOCSRouteAbsolute(
					'twofactor_nextcloud_notification.API.approve',
					['attemptId' => $attemptId, 'apiVersion' => 'v1'],
				),
				'POST'
			);

		$disapproveAction = $notification->createAction()
			->setParsedLabel($l->t('Cancel'))
			->setPrimary(false)
			->setLink(
				$this->urlGenerator->linkToOCSRouteAbsolute(
					'twofactor_nextcloud_notification.API.disapprove',
					['attemptId' => $attemptId, 'apiVersion' => 'v1'],
				),
				'DELETE'
			);

		$notification->addParsedAction($approveAction)
			->addParsedAction($disapproveAction)
			->setParsedSubject(str_replace('{ip}', $param['ip'], $l->t('Login attempt from IP address {ip}')))
			->setRichSubject(
				$l->t('Login attempt from IP address {ip}'),
				[
					'ip' => [
						'type' => 'highlight',
						'id' => $notification->getObjectId(),
						'name' => $param['ip'],
					],
				])
			->setParsedMessage($l->t('If you are currently trying log in from another device or browser please approve the request. If you are not trying to log in at the moment, you should use the cancel option to abort the login attempt.'))
			->setRichMessage($l->t('If you are currently trying log in from another device or browser please approve the request. If you are not trying to log in at the moment, you should use the cancel option to abort the login attempt.'))
			->setIcon($this->urlGenerator->imagePath('core', 'actions/password.svg'))
		;
		return $notification;
	}
}
