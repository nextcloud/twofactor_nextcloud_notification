/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import Axios from '@nextcloud/axios'
import { generateOcsUrl } from '@nextcloud/router'
import { poll } from '../util/poll.js'

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

/**
 * Start polling for the 2FA challenge
 *
 * @param {string} token - The challenge token
 * @return {Promise<boolean>} - Whether the challenge was accepted
 */
function challenge(token) {
	const url = generateOcsUrl('apps/twofactor_nextcloud_notification/api/v1/poll/{token}', { token })

	return poll(pollProducer(url), 800).then(r => {
		console.debug('polling finished', r)
		if (r.data.status === 'accepted') {
			return true
		} else {
			return false
		}
	}).catch(err => {
		console.error('polling failed', err)
		// TODO: properly handle an error
	})
}

/**
 * Start polling the 2FA challenge from within the challenge view page
 *
 * @return {Promise<boolean>} - Whether the challenge was accepted
 */
export async function challengeOnLoginForm() {
	const token = document.getElementById('challenge-poll-token').value
	console.debug('starting challenge polling', token)

	const isAccepted = await challenge(token)
	if (isAccepted) {
		// Move on when accepting
		document.getElementById('twofactor-form').submit()
		return true
	} else {
		// When the login was rejected cancel the login
		location.href = document.getElementsByClassName('two-factor-secondary')[0].href
		return false
	}
}
