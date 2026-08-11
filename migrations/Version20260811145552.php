<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260811145552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename the location column to location_label and add new columns for latitude, longitude, and place_id in the user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" RENAME COLUMN location TO location_label');
        $this->addSql('ALTER TABLE "user" ADD location_latitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD location_longitude DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD location_place_id VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" RENAME COLUMN location_label TO location');
        $this->addSql('ALTER TABLE "user" DROP location_latitude');
        $this->addSql('ALTER TABLE "user" DROP location_longitude');
        $this->addSql('ALTER TABLE "user" DROP location_place_id');
    }
}
