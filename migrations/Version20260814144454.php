<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260814144454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add index on trip.published_at column to improve query performance for published trips.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_trip_published_at ON trip (published_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_trip_published_at');
    }
}
