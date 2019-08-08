<?php
declare(strict_types=1);
/**
 * @author Joas Schilling <coding@schilljs.com>
 *
 * @copyright Copyright (c) 2018, Joas Schilling <coding@schilljs.com>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactorNextcloudNotification\Notification;

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;
use OCP\Notification\INotification;
use OCP\Notification\INotifier;

class Notifier implements INotifier {

	/** @var IFactory */
	protected $l10nFactory;

	/** @var IURLGenerator */
	protected $urlGenerator;

	/**
	 * @param IFactory $l10nFactory
	 * @param IURLGenerator $urlGenerator
	 */
	public function __construct(IFactory $l10nFactory, IURLGenerator $urlGenerator) {
		$this->l10nFactory = $l10nFactory;
		$this->urlGenerator = $urlGenerator;
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
	 * Human readable name describing the notifier
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
				$this->urlGenerator->getAbsoluteURL(
					$this->urlGenerator->linkTo(
						'',
						'ocs/v2.php/apps/twofactor_nextcloud_notification/api/v1/attempt/' . $attemptId
					)
				),
				'POST'
			);

		$disapproveAction = $notification->createAction()
			->setParsedLabel($l->t('Cancel'))
			->setPrimary(false)
			->setLink(
				$this->urlGenerator->getAbsoluteURL(
					$this->urlGenerator->linkTo(
						'',
						'ocs/v2.php/apps/twofactor_nextcloud_notification/api/v1/attempt/' . $attemptId
					)
				),
				'DELETE'
			);

		$notification->addParsedAction($approveAction)
			->addParsedAction($disapproveAction)
			->setRichSubject(
				$l->t('Login attempt from {ip}'),
				[
					'ip' => [
						'type' => 'highlight',
						'id' => $notification->getObjectId(),
						'name' => $param['ip'],
					],
				])
			->setMessage($l->t('Please approve or deny the login attempt.'))
			->setRichMessage(
				$l->t('Please approve or deny the login attempt.'),
				[])
			->setParsedSubject(str_replace('{ip}', $param['ip'], $l->t('Login attempt from {ip}')));
		return $notification;
	}
}
