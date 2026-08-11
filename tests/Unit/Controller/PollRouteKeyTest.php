<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2026 Leamsi Fontanez
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;

/**
 * Regression lock for appinfo/routes.php — guards against the
 * `twofactor_nextcloud_notification` 8.0.0 silent-no-op (refs #550)
 * where the poll route regex `[a-zA-Z0-9]{40}` was bound under
 * `attemptId` (a non-existent URL placeholder) rather than the actual
 * `token` placeholder, so the constraint never fired at the router.
 *
 * Keep this test in sync with route fixes.
 */
final class PollRouteKeyTest extends TestCase {
	public function testPollRouteRegexIsBoundToTokenPlaceholder(): void {
		$routesFile = dirname(__DIR__, 3) . '/appinfo/routes.php';
		$routes = require $routesFile;

		self::assertIsArray($routes);
		self::assertArrayHasKey('ocs', $routes);

		$poll = null;
		foreach ($routes['ocs'] as $entry) {
			$url = $entry['url'] ?? '';
			if (is_string($url) && str_starts_with($url, '/api/{apiVersion}/poll/')) {
				$poll = $entry;
				break;
			}
		}

		self::assertNotNull($poll, 'No /api/{apiVersion}/poll/ OCS route registered');
		self::assertSame('GET', $poll['verb']);
		self::assertArrayHasKey('requirements', $poll, 'poll route missing requirements block');
		self::assertArrayHasKey(
			'token',
			$poll['requirements'],
			"poll route requirement must be keyed under the URL placeholder 'token' "
			. "(was 'attemptId' on stable34 -- silent no-op)"
		);
		self::assertSame(
			'[a-zA-Z0-9]{40}',
			(string)$poll['requirements']['token'],
			'poll route 40-char constraint must match public-token shape'
		);
		self::assertArrayNotHasKey(
			'attemptId',
			$poll['requirements'],
			"poll route must not declare the regex under 'attemptId' -- silent no-op trap"
		);
	}
}
