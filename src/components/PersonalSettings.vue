<!--
  - SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<div id="twofactor-notification-settings">
		<NcCheckboxRadioSwitch type="switch"
			:checked.sync="enabled"
			:loading="loading"
			@update:checked="toggleEnabled">
			{{ t('twofactor_nextcloud_notification', 'Use two-factor authentication via Nextcloud notifications') }}
		</NcCheckboxRadioSwitch>
	</div>
</template>

<script>
import NcCheckboxRadioSwitch from '@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js'

export default {
	name: 'PersonalSettings',

	components: {
		NcCheckboxRadioSwitch,
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
