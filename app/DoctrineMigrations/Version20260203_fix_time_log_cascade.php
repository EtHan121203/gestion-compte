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
        return 'Fix foreign key constraints: time_log CASCADE, shift SET NULL on beneficiary/user delete';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Fix time_log: ON DELETE CASCADE for shift_id
        $this->addSql('ALTER TABLE time_log DROP FOREIGN KEY FK_55BE03AFBB70BC0E');
        $this->addSql('ALTER TABLE time_log ADD CONSTRAINT FK_55BE03AFBB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) ON DELETE CASCADE');

        // Fix shift: ON DELETE SET NULL for shifter_id, booker_id, last_shifter_id
        $this->addSql('ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B45A7DA74C1');
        $this->addSql('ALTER TABLE shift ADD CONSTRAINT FK_A50B3B45A7DA74C1 FOREIGN KEY (shifter_id) REFERENCES beneficiary (id) ON DELETE SET NULL');

        $this->addSql('ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B458B7E4006');
        $this->addSql('ALTER TABLE shift ADD CONSTRAINT FK_A50B3B458B7E4006 FOREIGN KEY (booker_id) REFERENCES fos_user (id) ON DELETE SET NULL');

        $this->addSql('ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B45AFA11FAE');
        $this->addSql('ALTER TABLE shift ADD CONSTRAINT FK_A50B3B45AFA11FAE FOREIGN KEY (last_shifter_id) REFERENCES beneficiary (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Revert time_log to ON DELETE SET NULL
        $this->addSql('ALTER TABLE time_log DROP FOREIGN KEY FK_55BE03AFBB70BC0E');
        $this->addSql('ALTER TABLE time_log ADD CONSTRAINT FK_55BE03AFBB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) ON DELETE SET NULL');

        // Revert shift foreign keys (remove ON DELETE SET NULL)
        $this->addSql('ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B45A7DA74C1');
        $this->addSql('ALTER TABLE shift ADD CONSTRAINT FK_A50B3B45A7DA74C1 FOREIGN KEY (shifter_id) REFERENCES beneficiary (id)');

        $this->addSql('ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B458B7E4006');
        $this->addSql('ALTER TABLE shift ADD CONSTRAINT FK_A50B3B458B7E4006 FOREIGN KEY (booker_id) REFERENCES fos_user (id)');

        $this->addSql('ALTER TABLE shift DROP FOREIGN KEY FK_A50B3B45AFA11FAE');
        $this->addSql('ALTER TABLE shift ADD CONSTRAINT FK_A50B3B45AFA11FAE FOREIGN KEY (last_shifter_id) REFERENCES beneficiary (id)');
    }
}
