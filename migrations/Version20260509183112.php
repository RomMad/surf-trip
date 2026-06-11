<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260509183112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Refactor trip location to support geocoding data';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS idx_trip_location');
        $this->addSql('DROP INDEX IF EXISTS idx_trip_search');

        $this->addSql('ALTER TABLE trip RENAME COLUMN location TO location_label');
        $this->addSql('ALTER TABLE trip ADD location_latitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD location_longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD location_place_id VARCHAR(255) DEFAULT NULL');

        $this->addSql('CREATE INDEX idx_trip_location_label ON trip (location_label)');
        $this->addSql('CREATE INDEX idx_trip_search ON trip (title, location_label)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_trip_location_label');
        $this->addSql('DROP INDEX idx_trip_search');

        $this->addSql('ALTER TABLE trip RENAME COLUMN location_label TO location');
        $this->addSql('ALTER TABLE trip DROP location_latitude');
        $this->addSql('ALTER TABLE trip DROP location_longitude');
        $this->addSql('ALTER TABLE trip DROP location_place_id');

        $this->addSql('CREATE INDEX idx_trip_location ON trip (location)');
        $this->addSql('CREATE INDEX idx_trip_search ON trip (title, location)');
    }
}
