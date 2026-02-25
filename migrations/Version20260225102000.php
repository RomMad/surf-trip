<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260225102000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add firstname and lastname columns to user table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD firstname VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD lastname VARCHAR(100) DEFAULT NULL');

        // Update existing users with default value for firstname (proper case)
        $this->addSql('UPDATE "user" SET firstname = INITCAP(LOWER(SUBSTRING(email FROM 1 FOR POSITION(\'@\' IN email) - 1))) WHERE firstname IS NULL');
        $this->addSql('ALTER TABLE "user" ALTER COLUMN firstname SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" DROP firstname');
        $this->addSql('ALTER TABLE "user" DROP lastname');
    }
}
