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
use OCA\TwoFactorNextcloudNotification\Service\StateManager;
use OCA\TwoFactorNextcloudNotification\Service\TokenManager;
use OCA\TwoFactorNextcloudNotification\Settings\Personal;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\Authentication\TwoFactorAuth\IDeactivatableByAdmin;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\Authentication\TwoFactorAuth\IProvidesPersonalSettings;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IUser;
use OCP\Template;

class NotificationProvider implements IProvider, IProvidesPersonalSettings, IDeactivatableByAdmin {

	/** @var IL10N */
	private $l10n;
	/** @var IConfig */
	private $config;
	/** @var TokenManager */
	private $tokenManager;
	/** @var StateManager */
	private $stateManager;

	public function __construct(IL10N $l10n,
								IConfig $config,
								TokenManager $tokenManager,
								StateManager $stateManager) {
		$this->l10n = $l10n;
		$this->config = $config;
		$this->tokenManager = $tokenManager;
		$this->stateManager = $stateManager;
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
		$token = $this->tokenManager->generate($user->getUID());

		$tmpl = new Template(Application::APP_ID, 'challenge');
		$tmpl->assign('token', $token->getToken());

		return $tmpl;
	}

	public function verifyChallenge(IUser $user, string $challenge): bool {
		try {
			$token = $this->tokenManager->getByToken($challenge);
		} catch (DoesNotExistException $e) {
			return false;
		}

		$this->tokenManager->delete($token);

		return $token->getStatus() === Token::ACCEPTED &&
			$token->getUserId() === $user->getUID();
	}

	public function isTwoFactorAuthEnabledForUser(IUser $user): bool {
		return $this->config->getAppValue(Application::APP_ID, $user->getUID() . '_enabled', '0') === '1';
	}

	public function getPersonalSettings(IUser $user): IPersonalProviderSettings {
		return new Personal($this->config->getAppValue(Application::APP_ID, $user->getUID() . '_enabled', '0') === '1');
	}

	public function disableFor(IUser $user) {
		$this->stateManager->setState($user, false);
	}
}
