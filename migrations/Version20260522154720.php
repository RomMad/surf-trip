<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260522154720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add trip relation to surf session';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE surf_session ADD trip_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE surf_session ADD CONSTRAINT FK_BB9F2D91A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_BB9F2D91A5BC2E0E ON surf_session (trip_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE surf_session DROP CONSTRAINT FK_BB9F2D91A5BC2E0E');
        $this->addSql('DROP INDEX IDX_BB9F2D91A5BC2E0E');
        $this->addSql('ALTER TABLE surf_session DROP trip_id');
    }
}
