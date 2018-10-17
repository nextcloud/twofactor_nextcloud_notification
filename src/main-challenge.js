/*
 * @copyright 2018 Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * @author 2018 Christoph Wurst <christoph@winzerhof-wurst.at>
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
 */

import Axios from 'nextcloud-axios'
import promisPoller from 'promise-poller'

const poll = (url) => () => {
	return Axios.get(url, {
		headers: {
			'OCS-APIRequest': 'true'
		}
	})
		.then(() => {
			// TODO: Magic
			return Promise.reject('nope')
		})
}

const token = document.getElementById('challenge-poll-token').value
console.debug('starting challenge polling', token)

const url = OC.linkToOCS('apps/twofactor_nextcloud_notification/api/v1/poll', 2) + token
const poller = promisPoller({
	taskFn: poll(url),
	interval: 300,
	retries: 5,
	progressCallback: (retries, error) => {
		console.debug('polling', retries, error)
	}
})

poller.then(r => {
	console.debug('polling finished', r)
}).catch(err => {
	console.error('polling failed', err)
})
