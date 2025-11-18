/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { createApp } from 'vue'
import Challenge from './components/Challenge.vue'
import store from './store.js'

createApp(Challenge)
	.use(store)
	.mount('#twofactor-notification-challenge')
