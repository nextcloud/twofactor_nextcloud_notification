<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Listener;

use OCA\TwoFactorNextcloudNotification\Event\StateChanged;
use OCA\TwoFactorNextcloudNotification\Provider\NotificationProvider;
use OCP\Authentication\TwoFactorAuth\IRegistry;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\User\Events\PostLoginEvent;

/**
 * @template-implements IEventListener<Event|PostLoginEvent>
 */
class RegistryUpdater implements IEventListener {
	public function __construct(
		private IRegistry $registry,
		private NotificationProvider $provider,
	) {
	}

	#[\Override]
	public function handle(Event $event): void {
		if ($event instanceof StateChanged) {
			if ($event->isEnabled()) {
				$this->registry->enableProviderFor($this->provider, $event->getUser());
			} else {
				$this->registry->disableProviderFor($this->provider, $event->getUser());
			}
		}
	}
}
