<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260107161251 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE membership_shift_exemption DROP FOREIGN KEY FK_BA11DB4C1FB354CD');
        $this->addSql('ALTER TABLE membership_shift_exemption DROP FOREIGN KEY FK_BA11DB4C24221478');
        $this->addSql('ALTER TABLE membership_shift_exemption DROP FOREIGN KEY FK_BA11DB4CB03A8386');
        $this->addSql('DROP INDEX idx_ba11db4cb03a8386 ON membership_shift_exemption');
        $this->addSql('CREATE INDEX IDX_2D388ADEB03A8386 ON membership_shift_exemption (created_by_id)');
        $this->addSql('DROP INDEX idx_ba11db4c24221478 ON membership_shift_exemption');
        $this->addSql('CREATE INDEX IDX_2D388ADE24221478 ON membership_shift_exemption (shift_exemption_id)');
        $this->addSql('DROP INDEX idx_ba11db4c1fb354cd ON membership_shift_exemption');
        $this->addSql('CREATE INDEX IDX_2D388ADE1FB354CD ON membership_shift_exemption (membership_id)');
        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_BA11DB4C1FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_BA11DB4C24221478 FOREIGN KEY (shift_exemption_id) REFERENCES shift_exemption (id)');
        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_BA11DB4CB03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE membership_shift_exemption DROP FOREIGN KEY FK_2D388ADEB03A8386');
        $this->addSql('ALTER TABLE membership_shift_exemption DROP FOREIGN KEY FK_2D388ADE24221478');
        $this->addSql('ALTER TABLE membership_shift_exemption DROP FOREIGN KEY FK_2D388ADE1FB354CD');
        $this->addSql('DROP INDEX idx_2d388ade1fb354cd ON membership_shift_exemption');
        $this->addSql('CREATE INDEX IDX_BA11DB4C1FB354CD ON membership_shift_exemption (membership_id)');
        $this->addSql('DROP INDEX idx_2d388adeb03a8386 ON membership_shift_exemption');
        $this->addSql('CREATE INDEX IDX_BA11DB4CB03A8386 ON membership_shift_exemption (created_by_id)');
        $this->addSql('DROP INDEX idx_2d388ade24221478 ON membership_shift_exemption');
        $this->addSql('CREATE INDEX IDX_BA11DB4C24221478 ON membership_shift_exemption (shift_exemption_id)');
        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_2D388ADEB03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_2D388ADE24221478 FOREIGN KEY (shift_exemption_id) REFERENCES shift_exemption (id)');
        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_2D388ADE1FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id) ON DELETE CASCADE');
    }
}
