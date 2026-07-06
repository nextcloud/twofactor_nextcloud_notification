/*!
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { recommendedJavascript } from '@nextcloud/eslint-config'
import globals from 'globals'

export default [
	...recommendedJavascript,
	{
		rules: {
			// TODO: migrate to @nextcloud/logger
			'no-console': 'off',
		},
	},
]
