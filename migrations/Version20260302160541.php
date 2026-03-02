<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260302160541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unused indexes on trip entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_trip_title');
        $this->addSql('DROP INDEX idx_trip_end_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_trip_title ON trip (title)');
        $this->addSql('CREATE INDEX idx_trip_end_at ON trip (end_at)');
    }
}
