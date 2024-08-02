/**
 * SPDX-FileCopyrightText: 2019 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

const pollRec = (producer, res, interval) => {
	producer()
		.then(res)
		.catch(err => {
			console.error('promise rejected:', err)

			setTimeout(() => {
				console.error('retrying …')
				pollRec(producer, res, interval)
			}, interval)
		})
}

/**
 * @param {Function} producer factory function that produces promises
 * @param {number} interval polling interval
 * @return {Promise}
 */
export const poll = (producer, interval) => {
	return new Promise(resolve => {
		pollRec(producer, resolve, interval)
	})
}
