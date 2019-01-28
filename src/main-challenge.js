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
import Vue from 'vue'

import Challenge from './components/Challenge.vue'
import Nextcloud from './mixins/Nextcloud'
import {poll} from './util/poll'
import store from "./store";

Vue.mixin(Nextcloud)

const pollProducer = (url) => () => {
	return Axios.get(url, {})
		.then(resp => resp.data)
		.then(({ocs}) => {
			if (ocs.data.status === 'pending') {
				return Promise.reject(ocs.data.status)
			}
		})
}

const View = Vue.extend(Challenge)
const view = new View({
	store
}).$mount('#twofactor-notification-challenge')

const token = document.getElementById('challenge-poll-token').value
console.debug('starting challenge polling', token)

const url = OC.linkToOCS('apps/twofactor_nextcloud_notification/api/v1/poll', 2) + token

poll(pollProducer(url), 800).then(r => {
	console.debug('polling finished', r)
	view.state++
	document.getElementById("twofactor-form").submit()
}).catch(err => {
	console.error('polling failed', err)
})
