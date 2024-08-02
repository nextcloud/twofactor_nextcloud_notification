<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

return [
	'routes' => [
		[
			'name' => 'Settings#setState',
			'url' => '/settings/state',
			'verb' => 'POST',
		],
	],
	'ocs' => [
		[
			'name' => 'API#approve',
			'url' => '/api/{apiVersion}/attempt/{attemptId}',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v1',
				'attemptId' => '\d+',
			],
		],
		[
			'name' => 'API#disapprove',
			'url' => '/api/{apiVersion}/attempt/{attemptId}',
			'verb' => 'DELETE',
			'requirements' => [
				'apiVersion' => 'v1',
				'attemptId' => '\d+',
			],
		],
		[
			'name' => 'API#poll',
			'url' => '/api/{apiVersion}/poll/{token}',
			'verb' => 'GET',
			'requirements' => [
				'apiVersion' => 'v1',
				'attemptId' => '[a-zA-Z0-9]{40}',
			],
		],
	]
];
