<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version_2025_10_22_1820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'This create table contacts BBDD';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('contacts');

        $table->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
        $table->addColumn('name', 'string', ['length' => 255, 'notnull' => true]);
        $table->addColumn('phone', 'string', ['length' => 50, 'notnull' => true]);
        $table->addColumn('email', 'string', ['length' => 255, 'notnull' => true]);
        $table->addColumn('created_at', 'datetime', ['notnull' => true, 'default' => 'CURRENT_TIMESTAMP']);
        $table->addColumn('updated_at', 'datetime', ['notnull' => true, 'default' => 'CURRENT_TIMESTAMP']);

        $table->setPrimaryKey(['id']);

        $table->addUniqueIndex(['email'], 'uniq_contacts_email', ['lengths' => [191]]);
    }

    public function down(Schema $schema): void
    {
        // Eliminamos la tabla si se hace rollback
        $schema->dropTable('contacts');
    }
}
