<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Enum\User\Locale;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260423142352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add locale field to user entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" ADD locale VARCHAR(2) DEFAULT NULL');

        // Set default locale for existing users
        $this->addSql('UPDATE "user" SET locale = :defaultLocale WHERE locale IS NULL', [
            'defaultLocale' => Locale::DEFAULT->value,
        ]);

        // Make locale non-nullable
        $this->addSql('ALTER TABLE "user" ALTER COLUMN locale SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user" DROP locale');
    }
}
