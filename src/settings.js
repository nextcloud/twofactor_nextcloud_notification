/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import Vue from 'vue'

import Nextcloud from './mixins/Nextcloud.js'
import PersonalSettings from './components/PersonalSettings.vue'
import store from './store.js'
import { loadState } from '@nextcloud/initial-state'

Vue.mixin(Nextcloud)

const enabled = loadState('twofactor_nextcloud_notification', 'state')
store.replaceState({
	enabled,
})

const View = Vue.extend(PersonalSettings)
new View({
	store,
}).$mount('#twofactor-notification-settings')
