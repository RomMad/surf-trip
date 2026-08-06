<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260805151300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Alter the "roles" column in the "user" table to use JSONB type instead of JSON type for indexing capabilities.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ALTER COLUMN roles TYPE JSONB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ALTER COLUMN roles TYPE JSON');
    }
}
