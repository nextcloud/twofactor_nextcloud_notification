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

namespace OCA\TwoFactorNextcloudNotification\Provider;

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCA\TwoFactorNextcloudNotification\Db\Token;
use OCA\TwoFactorNextcloudNotification\Db\TokenMapper;
use OCA\TwoFactorNextcloudNotification\Settings\Personal;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\Authentication\TwoFactorAuth\IProvidesPersonalSettings;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IUser;
use OCP\Notification\IManager;
use OCP\Template;

class NotificationProvider implements IProvider, IProvidesPersonalSettings {

	/** @var IL10N */
	private $l10n;
	/** @var TokenMapper */
	private $tokenMapper;
	/** @var ITimeFactory */
	private $timeFactory;
	/** @var IManager */
	private $notificationManager;
	/** @var IRequest */
	private $request;
	/** @var IConfig */
	private $config;

	public function __construct(IL10N $l10n,
								IRequest $request,
								TokenMapper $tokenMapper,
								ITimeFactory $timeFactory,
								IManager $notificationManager,
								IConfig $config) {
		$this->l10n = $l10n;
		$this->request = $request;
		$this->tokenMapper = $tokenMapper;
		$this->timeFactory = $timeFactory;
		$this->notificationManager = $notificationManager;
		$this->config = $config;
	}

	/**
	 * Get unique identifier of this 2FA provider
	 *
	 * @return string
	 */
	public function getId(): string {
		return Application::APP_ID;
	}

	/**
	 * Get the display name for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDisplayName(): string {
		return $this->l10n->t('Nextcloud Notification');
	}

	/**
	 * Get the description for selecting the 2FA provider
	 *
	 * @return string
	 */
	public function getDescription(): string {
		return $this->l10n->t('Authenticate using a device that is already logged in to your account');
	}

	/**
	 * Get the template for rending the 2FA provider view
	 *
	 * @param IUser $user
	 * @return Template
	 */
	public function getTemplate(IUser $user): Template {
		$token = $this->tokenMapper->generate($user->getUID());

		//Send notificcation
		$notification = $this->notificationManager->createNotification();
		$notification->setApp(Application::APP_ID)
			->setSubject('login_attempt', [
				'ip' => $this->request->getRemoteAddress(),
			])
			->setObject('2fa_id', $token->getId())
			->setUser($user->getUID())
			->setDateTime(new \DateTime());
		$this->notificationManager->notify($notification);

		$tmpl = new Template(Application::APP_ID, 'challenge');
		$tmpl->assign('token', $token->getToken());

		return $tmpl;
	}

	public function verifyChallenge(IUser $user, string $challenge): bool {
		try {
			$token = $this->tokenMapper->getBytoken($challenge);
		} catch (DoesNotExistException $e) {
			return false;
		}

		$this->tokenMapper->delete($token);

		return $token->getStatus() === Token::ACCEPTED &&
			($this->timeFactory->getTime() - $token->getTimestamp()) <= 60 * 10 &&
			$token->getUserId() === $user->getUID();
	}

	public function isTwoFactorAuthEnabledForUser(IUser $user): bool {
		return $this->config->getAppValue(Application::APP_ID, $user->getUID() . '_enabled', '0') === '1';
	}

	public function getPersonalSettings(IUser $user): IPersonalProviderSettings {
		return new Personal(
			$this->config->getAppValue(Application::APP_ID, $user->getUID() . '_enabled', '0') === '1'
		);
	}
}
