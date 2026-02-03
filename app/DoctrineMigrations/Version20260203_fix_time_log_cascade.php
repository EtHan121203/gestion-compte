<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to fix TimeLog foreign key constraint
 * 
 * Changes ON DELETE SET NULL to ON DELETE CASCADE on shift_id
 * to automatically delete time_log entries when shifts are deleted.
 */
final class Version20260203_fix_time_log_cascade extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change time_log.shift_id foreign key from ON DELETE SET NULL to ON DELETE CASCADE';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Drop the old constraint and recreate with CASCADE
        $this->addSql('ALTER TABLE time_log DROP FOREIGN KEY FK_55BE03AFBB70BC0E');
        $this->addSql('ALTER TABLE time_log ADD CONSTRAINT FK_55BE03AFBB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Revert to ON DELETE SET NULL
        $this->addSql('ALTER TABLE time_log DROP FOREIGN KEY FK_55BE03AFBB70BC0E');
        $this->addSql('ALTER TABLE time_log ADD CONSTRAINT FK_55BE03AFBB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) ON DELETE SET NULL');
    }
}
