<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260303145455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add slug field to trip table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trip ADD slug VARCHAR(255) DEFAULT NULL');

        // Generate slugs for existing trips
        $this->addSql('UPDATE trip SET slug = LOWER(REPLACE(title, \' \', \'-\'))');

        $this->addSql('ALTER TABLE trip ALTER slug SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trip DROP slug');
    }
}
