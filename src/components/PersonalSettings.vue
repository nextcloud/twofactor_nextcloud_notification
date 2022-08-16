<!--
  - @copyright 2018 Christoph Wurst <christoph@winzerhof-wurst.at>
  -
  - @author 2018 Christoph Wurst <christoph@winzerhof-wurst.at>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
	<div id="twofactor-notification-settings">
		<CheckboxRadioSwitch type="switch"
			:checked.sync="enabled"
			:loading="loading"
			@update:checked="toggleEnabled">
			{{ t('twofactor_nextcloud_notification', 'Use two-factor authentication via Nextcloud notifications') }}
		</CheckboxRadioSwitch>
	</div>
</template>

<script>
import CheckboxRadioSwitch from '@nextcloud/vue/dist/Components/CheckboxRadioSwitch.js'

export default {
	name: 'PersonalSettings',

	components: {
		CheckboxRadioSwitch,
	},

	data() {
		return {
			enabled: this.$store.state.enabled,
			loading: false,
		}
	},
	methods: {
		toggleEnabled() {
			this.loading = true

			let action
			if (this.enabled) {
				action = this.$store.dispatch('enable')
			} else {
				action = this.$store.dispatch('disable')
			}

			action
				.then(enabled => {
					this.enabled = enabled
				})
				.catch(console.error.bind(this))
				.then(() => {
					this.loading = false
				})
		},
	},
}
</script>

<style scoped>
	.loading {
		display: inline-block;
		vertical-align: middle;
		margin-left: -2px;
		margin-right: 1px;
	}
</style>
