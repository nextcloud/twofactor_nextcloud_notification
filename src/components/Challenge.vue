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
	<p v-if="state === State.POLLING">
		{{ t('twofactor_nextcloud_notification', 'Please accept the request on one of your logged in devices.') }}
		{{ t('twofactor_nextcloud_notification', 'You will be redirected automatically once this login has been accepted.') }}
	</p>
	<p v-else-if="state === State.VERIFYING">
		<span class="icon-loading-small"></span>
		{{ t('twofactor_nextcloud_notification', 'Please wait …') }}
	</p>
	<p v-else-if="state === State.REJECTED">
		<span class="icon-loading-small"></span>
		{{ t('twofactor_nextcloud_notification', 'Your login attempt was rejected.') }}
	</p>
</template>

<script>
	const State = Object.freeze({
		POLLING: 0,
		VERIFYING: 1,
		REJECTED: 2,
	})

	export default {
		name: 'Challenge',
		data () {
			return {
				state: State.POLLING,
				State,
			}
		}
	}
</script>

<style scoped>
	.icon-loading-small {
		display: inline-block;
		vertical-align: sub;
		padding-right: 12px;
	}
</style>
