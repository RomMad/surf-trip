<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260517165005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user association to surf sessions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE surf_session ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE surf_session ADD CONSTRAINT FK_BB9F2D91A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_BB9F2D91A76ED395 ON surf_session (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE surf_session DROP CONSTRAINT FK_BB9F2D91A76ED395');
        $this->addSql('DROP INDEX IDX_BB9F2D91A76ED395');
        $this->addSql('ALTER TABLE surf_session DROP user_id');
    }
}
