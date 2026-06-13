<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260612155012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add location comment to trip';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trip ADD location_comment VARCHAR(255) DEFAULT NULL');

        $this->addSql('DROP INDEX IF EXISTS idx_trip_location_label');
        $this->addSql('CREATE INDEX idx_trip_location ON trip (location_label, location_comment)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS idx_trip_location');
        $this->addSql('CREATE INDEX idx_trip_location_label ON trip (location_label)');

        $this->addSql('ALTER TABLE trip DROP location_comment');
    }
}
