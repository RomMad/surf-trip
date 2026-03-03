<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260303134538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove created_at index from trip entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_trip_created_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_trip_created_at ON trip (created_at)');
    }
}
