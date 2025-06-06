<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Notification;

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCA\TwoFactorNextcloudNotification\Exception\TokenExpireException;
use OCA\TwoFactorNextcloudNotification\Service\TokenManager;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use OCP\Notification\AlreadyProcessedException;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;
use OCP\Notification\UnknownNotificationException;

class Notifier implements INotifier {
	public function __construct(
		protected IFactory $l10nFactory,
		protected IURLGenerator $urlGenerator,
		protected TokenManager $tokenManager,
	) {
	}

	#[\Override]
	public function getID(): string {
		return Application::APP_ID;
	}

	#[\Override]
	public function getName(): string {
		return $this->l10nFactory->get(Application::APP_ID)->t('TwoFactor Nextcloud notification');
	}

	/**
	 * @param INotification $notification
	 * @param string $languageCode The code of the language that should be used to prepare the notification
	 * @return INotification
	 * @throws UnknownNotificationException When the notification was not prepared by a notifier
	 * @throws AlreadyProcessedException When the notification is not needed anymore and should be deleted
	 */
	#[\Override]
	public function prepare(INotification $notification, string $languageCode): INotification {
		if ($notification->getApp() !== Application::APP_ID ||
			$notification->getSubject() !== 'login_attempt') {
			throw new UnknownNotificationException();
		}

		$attemptId = $notification->getObjectId();

		try {
			$token = $this->tokenManager->getById((int)$attemptId);
		} catch (DoesNotExistException|TokenExpireException) {
			throw new AlreadyProcessedException();
		}

		if ($token->getUserId() !== $notification->getUser()) {
			throw new AlreadyProcessedException();
		}

		$l = $this->l10nFactory->get(Application::APP_ID, $languageCode);
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
			->setPriorityNotification(true)
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
			->setIcon($this->urlGenerator->getAbsoluteURL($this->urlGenerator->imagePath(Application::APP_ID, 'login-dark.svg')))
		;
		return $notification;
	}
}
