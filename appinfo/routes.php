<?php
/**
 * @copyright Copyright (c) 2018 Joas Schilling <coding@schilljs.com>
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
 *
 */

return [
	'routes' => [
		[
			'name' => 'Settings#getState',
			'url' => '/settings/state',
			'verb' => 'GET',
		],
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
