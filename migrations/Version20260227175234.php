<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260227175234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add is_verified field to user entity and rename firstname and lastname fields to first_name and last_name.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD is_verified BOOLEAN DEFAULT NULL');

        $this->addSql('UPDATE "user" SET is_verified = true');

        $this->addSql('ALTER TABLE "user" ALTER is_verified SET NOT NULL');

        $this->addSql('ALTER TABLE "user" RENAME COLUMN firstname TO first_name');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN lastname TO last_name');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" RENAME COLUMN first_name TO firstname');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN last_name TO lastname');

        $this->addSql('ALTER TABLE "user" DROP is_verified');
    }
}
