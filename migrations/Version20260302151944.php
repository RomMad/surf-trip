<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260302151944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add indexes to Trip entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_trip_title ON trip (title)');
        $this->addSql('CREATE INDEX idx_trip_created_at ON trip (created_at)');
        $this->addSql('CREATE INDEX idx_trip_end_at ON trip (end_at)');
        $this->addSql('CREATE INDEX idx_trip_location ON trip (location)');
        $this->addSql('CREATE INDEX idx_trip_required_levels ON trip USING GIN (required_levels)');
        $this->addSql('CREATE INDEX idx_trip_search ON trip (title, location)');
        $this->addSql('CREATE INDEX idx_trip_start_at ON trip (start_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_trip_created_at');
        $this->addSql('DROP INDEX idx_trip_end_at');
        $this->addSql('DROP INDEX idx_trip_location');
        $this->addSql('DROP INDEX idx_trip_required_levels');
        $this->addSql('DROP INDEX idx_trip_search');
        $this->addSql('DROP INDEX idx_trip_start_at');
        $this->addSql('DROP INDEX idx_trip_title');
    }
}
