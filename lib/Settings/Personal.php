<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Settings;

use OCA\TwoFactorNextcloudNotification\AppInfo\Application;
use OCP\AppFramework\Services\IInitialState;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Template\ITemplate;
use OCP\Template\ITemplateManager;

class Personal implements IPersonalProviderSettings {
	public function __construct(
		private IInitialState $initialStateService,
		private ITemplateManager $templateManager,
		private bool $enabled,
	) {
	}

	#[\Override]
	public function getBody(): ITemplate {
		$this->initialStateService->provideInitialState('state', $this->enabled);
		return $this->templateManager->getTemplate(Application::APP_ID, 'personal');
	}
}
