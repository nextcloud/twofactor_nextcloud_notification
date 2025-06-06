<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2018 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorNextcloudNotification\Migration;

use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version0001Date20180411172140 extends SimpleMigrationStep {
	/**
	 * @param IOutput $output
	 * @param \Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	#[\Override]
	public function changeSchema(IOutput $output, \Closure $schemaClosure, array $options) {
		/**
		 * Dropped by Version4000Date20240802134536
		 * Recreated with oracle compatible names in Version4000Date20240802134537
		 * if (!$schema->hasTable(Application::APP_ID . '_tokens')) {
		 * $table = $schema->createTable(Application::APP_ID . '_tokens');
		 *
		 * $table->addColumn('id', Types::INTEGER, [
		 * 'autoincrement' => true,
		 * 'notnull' => true,
		 * 'length' => 20,
		 * ]);
		 * $table->addColumn('user_id', Types::STRING, [
		 * 'notnull' => true,
		 * 'length' => 64,
		 * ]);
		 * $table->addColumn('token', Types::STRING, [
		 * 'notnull' => true,
		 * 'length' => 40,
		 * ]);
		 * $table->addColumn('timestamp', Types::INTEGER, [
		 * 'notnull' => true,
		 * 'length' => 20,
		 * ]);
		 * $table->addColumn('status', Types::INTEGER, [
		 * 'notnull' => true,
		 * 'length' => 2,
		 * ]);
		 *
		 * $table->setPrimaryKey(['id'], Application::APP_ID . '_tokens_id_idx');
		 * $table->addIndex(['token'], Application::APP_ID . '_tokens_token_idx');
		 *
		 * return $schema;
		 * }
		 */
		return null;
	}
}
