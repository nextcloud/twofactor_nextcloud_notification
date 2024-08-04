<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Provider;

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCA\TwoFactorNextcloudNotification\Db\Token;
use OCA\TwoFactorNextcloudNotification\Service\StateManager;
use OCA\TwoFactorNextcloudNotification\Service\TokenManager;
use OCA\TwoFactorNextcloudNotification\Settings\Personal;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Services\IInitialState;
use OCP\Authentication\TwoFactorAuth\IActivatableByAdmin;
use OCP\Authentication\TwoFactorAuth\IDeactivatableByAdmin;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\Authentication\TwoFactorAuth\IProvidesIcons;
use OCP\Authentication\TwoFactorAuth\IProvidesPersonalSettings;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Template;

class NotificationProvider implements IProvider, IProvidesIcons, IProvidesPersonalSettings, IActivatableByAdmin, IDeactivatableByAdmin {
	public function __construct(
		private IL10N $l10n,
		private TokenManager $tokenManager,
		private StateManager $stateManager,
		private IInitialState $initialStateService,
		private IURLGenerator $url,
	) {
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

	public function getLightIcon(): string {
		return $this->url->getAbsoluteURL($this->url->imagePath(Application::APP_ID, 'app.svg'));
	}

	public function getDarkIcon(): string {
		return $this->url->getAbsoluteURL($this->url->imagePath(Application::APP_ID, 'app-dark.svg'));
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
		return $this->stateManager->getState($user);
	}

	public function getPersonalSettings(IUser $user): IPersonalProviderSettings {
		return new Personal($this->initialStateService, $this->stateManager->getState($user));
	}

	public function enableFor(IUser $user): void {
		$this->stateManager->setState($user, true);
	}

	public function disableFor(IUser $user): void {
		$this->stateManager->setState($user, false);
	}
}
