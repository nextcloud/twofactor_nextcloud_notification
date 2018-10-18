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

import Vue from 'vue'
import Vuex from 'vuex'

import {persist} from './services/SettingsService'

Vue.use(Vuex)

export const mutations = {
	setEnabled (state, enabled) {
		state.enabled = enabled
	}
}

export const actions = {
	enable ({commit}) {
		return persist(true)
			.then(({enabled}) => {
				commit('setEnabled', enabled)
				return enabled
			})
	},

	disable ({commit}) {
		return persist(false)
			.then(({enabled}) => {
				commit('setEnabled', enabled)
				return enabled
			})
	}
}

export default new Vuex.Store({
	strict: process.env.NODE_ENV !== 'production',
	state: {
		enabled: false
	},
	mutations,
	actions
})
