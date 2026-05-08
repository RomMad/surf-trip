<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260507145457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add username, avatar, description, level, location, instagram, createdAt, updatedAt and lastActiveAt fields to user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "user"
            ADD username VARCHAR(50) DEFAULT NULL,
            ADD avatar VARCHAR(255) DEFAULT NULL,
            ADD description TEXT DEFAULT NULL,
            ADD level INT DEFAULT NULL,
            ADD location VARCHAR(255) DEFAULT NULL,
            ADD instagram VARCHAR(64) DEFAULT NULL,
            ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            ADD last_active_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL
        ');

        $this->addSql('UPDATE "user"
            SET
                username = CONCAT(LOWER(first_name), \'.\', LOWER(last_name)),
                created_at = NOW(),
                updated_at = NOW()
        ');

        $this->addSql('ALTER TABLE "user"
            ALTER username SET NOT NULL,
            ALTER created_at SET NOT NULL,
            ALTER updated_at SET NOT NULL
        ');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_USER_USERNAME ON "user" (username)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_USER_USERNAME');

        $this->addSql('ALTER TABLE "user"
            DROP username,
            DROP avatar,
            DROP description,
            DROP level,
            DROP location,
            DROP instagram,
            DROP created_at,
            DROP updated_at,
            DROP last_active_at
        ');
    }
}
