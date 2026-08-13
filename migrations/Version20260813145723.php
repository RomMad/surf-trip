<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260813145723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add updatedAt and createdBy to trip table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trip ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BB03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) ON DELETE SET NULL NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_7656F53BB03A8386 ON trip (created_by_id)');

        $this->addSql('UPDATE trip SET updated_at = created_at');
        $this->addSql('UPDATE trip SET created_by_id = (SELECT user_id FROM trip_user WHERE trip_id = trip.id LIMIT 1)');

        $this->addSql('ALTER TABLE trip ALTER COLUMN updated_at SET NOT NULL');
        $this->addSql('ALTER TABLE trip ALTER COLUMN created_by_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trip DROP CONSTRAINT FK_7656F53BB03A8386');
        $this->addSql('DROP INDEX IDX_7656F53BB03A8386');
        $this->addSql('ALTER TABLE trip DROP updated_at');
        $this->addSql('ALTER TABLE trip DROP created_by_id');
    }
}
