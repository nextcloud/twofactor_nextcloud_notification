<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version4000Date20240802134537 extends SimpleMigrationStep {
	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	#[\Override]
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('twofactor_tnn_tokens')) {
			$table = $schema->createTable('twofactor_tnn_tokens');

			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('token', Types::STRING, [
				'notnull' => true,
				'length' => 40,
			]);
			$table->addColumn('timestamp', Types::INTEGER, [
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('status', Types::INTEGER, [
				'notnull' => true,
				'length' => 2,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['token'], 'twofactor_tnn_token_idx');

			return $schema;
		}
		return null;
	}
}
