<!--
  - SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<p v-if="state === State.POLLING">
		{{ t('twofactor_nextcloud_notification', 'Please accept the request on one of your logged in devices.') }}
		{{ t('twofactor_nextcloud_notification', 'You will be redirected automatically once this login has been accepted.') }}
	</p>
	<p v-else-if="state === State.VERIFYING">
		<span class="icon-loading-small" />
		{{ t('twofactor_nextcloud_notification', 'Please wait …') }}
	</p>
	<p v-else-if="state === State.REJECTED">
		<span class="icon-loading-small" />
		{{ t('twofactor_nextcloud_notification', 'Your login attempt was rejected.') }}
	</p>
</template>

<script>
import { t } from '@nextcloud/l10n'
import { challengeOnLoginForm } from '../services/ChallengeService.js'

const State = Object.freeze({
	POLLING: 0,
	VERIFYING: 1,
	REJECTED: 2,
})

export default {
	name: 'Challenge',

	data() {
		return {
			state: State.POLLING,
			State,
		}
	},

	async created() {
		const accepted = await challengeOnLoginForm()
		this.state = accepted ? State.VERIFYING : State.REJECTED
	},

	methods: {
		t,
	},
}
</script>

<style scoped>
	.icon-loading-small {
		display: inline-block;
		vertical-align: sub;
		padding-right: 12px;
	}
</style>
