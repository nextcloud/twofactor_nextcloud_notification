/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { createApp } from 'vue'

import PersonalSettings from './components/PersonalSettings.vue'
import store from './store.js'
import { loadState } from '@nextcloud/initial-state'

const enabled = loadState('twofactor_nextcloud_notification', 'state')
store.replaceState({
	enabled,
})

createApp(PersonalSettings)
	.use(store)
	.mount('#twofactor-notification-settings')
