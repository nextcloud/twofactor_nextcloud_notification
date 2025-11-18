/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import Axios from '@nextcloud/axios'
import { generateOcsUrl } from '@nextcloud/router'
import Vue from 'vue'

import Challenge from './components/Challenge.vue'
import { poll } from './util/poll.js'
import store from './store.js'

const pollProducer = (url) => () => {
	return Axios.get(url, {})
		.then(resp => resp.data)
		.then(({ ocs }) => {
			if (ocs.data.status === 'pending') {
				return Promise.reject(ocs.data.status)
			}

			return ocs
		})
}

const View = Vue.extend(Challenge)
const view = new View({
	store,
}).$mount('#twofactor-notification-challenge')

const token = document.getElementById('challenge-poll-token').value
console.debug('starting challenge polling', token)

const url = generateOcsUrl('apps/twofactor_nextcloud_notification/api/v1/poll/{token}', { token })

poll(pollProducer(url), 800).then(r => {
	console.debug('polling finished', r)
	if (r.data.status === 'accepted') {
		// Move on when accepting
		view.state = 1
		document.getElementById('twofactor-form').submit()
	} else {
		// When the login was rejected cancel the login
		view.state = 2
		location.href = document.getElementsByClassName('two-factor-secondary')[0].href
	}
}).catch(err => {
	console.error('polling failed', err)
})
