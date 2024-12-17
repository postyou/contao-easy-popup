<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoEasyPopupBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class PublishedMigration extends AbstractMigration
{
    public function __construct(
        private readonly Connection $connection,
    ) {}

    public function shouldRun(): bool
    {
        $schemaManager = $this->connection->createSchemaManager();

        if (!$schemaManager->tablesExist(['tl_node'])) {
            return false;
        }

        $columns = $schemaManager->listTableColumns('tl_node');

        return isset($columns['published']) && !isset($columns['popuppublished']);
    }

    public function run(): MigrationResult
    {
        $this->connection->executeStatement("ALTER TABLE tl_node ADD popupPublished TINYINT(1) DEFAULT 1 NOT NULL");

        $this->connection->executeStatement("UPDATE tl_node SET easyPopupSettings='1', popupPublished='0' WHERE published='0' AND type='content'");

        return $this->createResult(true);
    }
}
