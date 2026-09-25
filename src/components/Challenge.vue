<!--
  - SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<div v-if="state === State.POLLING" class="challenge-polling">
		<p>
			{{ t('twofactor_nextcloud_notification', 'Please accept the request on one of your logged in devices.') }}
			{{ t('twofactor_nextcloud_notification', 'You will be redirected automatically once this login has been accepted.') }}
		</p>
		<NcLoadingIcon :size="32" />
	</div>
	<p v-else-if="state === State.VERIFYING" class="challenge-verifying">
		<NcLoadingIcon />
		{{ t('twofactor_nextcloud_notification', 'Please wait …') }}
	</p>
	<p v-else-if="state === State.REJECTED">
		{{ t('twofactor_nextcloud_notification', 'Your login attempt was rejected.') }}
	</p>
</template>

<script>
import { t } from '@nextcloud/l10n'
import NcLoadingIcon from '@nextcloud/vue/components/NcLoadingIcon'
import { challengeOnLoginForm } from '../services/ChallengeService.js'

const State = Object.freeze({
	POLLING: 0,
	VERIFYING: 1,
	REJECTED: 2,
})

export default {
	// TODO: Rename component to a multi-word
	// eslint-disable-next-line vue/multi-word-component-names
	name: 'Challenge',

	components: {
		NcLoadingIcon,
	},

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
.challenge-polling {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: calc(3 * var(--default-grid-baseline));
}

.challenge-verifying {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: calc(2 * var(--default-grid-baseline));
}
</style>
