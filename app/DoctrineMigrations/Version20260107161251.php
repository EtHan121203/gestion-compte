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

        // Vérifier et supprimer les contraintes si elles existent
        $connection = $this->connection;
        $constraints = ['FK_BA11DB4C1FB354CD', 'FK_BA11DB4C24221478', 'FK_BA11DB4CB03A8386'];

        foreach ($constraints as $constraint) {
            $sql = "SELECT COUNT(*) 
                    FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE CONSTRAINT_NAME = '$constraint' 
                    AND TABLE_NAME = 'membership_shift_exemption' 
                    AND TABLE_SCHEMA = DATABASE()";

            $exists = (int) $connection->fetchOne($sql);

            if ($exists > 0) {
                $this->addSql("ALTER TABLE membership_shift_exemption DROP FOREIGN KEY $constraint");
            }
        }

        // Gérer les index - vérifier s'ils existent avant de les supprimer
        $oldIndexes = ['idx_ba11db4cb03a8386', 'idx_ba11db4c24221478', 'idx_ba11db4c1fb354cd'];
        foreach ($oldIndexes as $indexName) {
            $sql = "SELECT COUNT(*) 
                    FROM information_schema.STATISTICS 
                    WHERE INDEX_NAME = '$indexName' 
                    AND TABLE_NAME = 'membership_shift_exemption' 
                    AND TABLE_SCHEMA = DATABASE()";

            $exists = (int) $connection->fetchOne($sql);

            if ($exists > 0) {
                $this->addSql("DROP INDEX $indexName ON membership_shift_exemption");
            }
        }

        // Créer les nouveaux index s'ils n'existent pas déjà
        $newIndexes = [
            'IDX_2D388ADEB03A8386' => 'created_by_id',
            'IDX_2D388ADE24221478' => 'shift_exemption_id',
            'IDX_2D388ADE1FB354CD' => 'membership_id'
        ];

        foreach ($newIndexes as $indexName => $column) {
            $sql = "SELECT COUNT(*) 
                    FROM information_schema.STATISTICS 
                    WHERE INDEX_NAME = '$indexName' 
                    AND TABLE_NAME = 'membership_shift_exemption' 
                    AND TABLE_SCHEMA = DATABASE()";

            $exists = (int) $connection->fetchOne($sql);

            if ($exists == 0) {
                $this->addSql("CREATE INDEX $indexName ON membership_shift_exemption ($column)");
            }
        }

        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_BA11DB4C1FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_BA11DB4C24221478 FOREIGN KEY (shift_exemption_id) REFERENCES shift_exemption (id)');
        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_BA11DB4CB03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Vérifier et supprimer les contraintes si elles existent
        $connection = $this->connection;
        $constraints = ['FK_2D388ADEB03A8386', 'FK_2D388ADE24221478', 'FK_2D388ADE1FB354CD'];

        foreach ($constraints as $constraint) {
            $sql = "SELECT COUNT(*) 
                    FROM information_schema.TABLE_CONSTRAINTS 
                    WHERE CONSTRAINT_NAME = '$constraint' 
                    AND TABLE_NAME = 'membership_shift_exemption' 
                    AND TABLE_SCHEMA = DATABASE()";

            $exists = (int) $connection->fetchOne($sql);

            if ($exists > 0) {
                $this->addSql("ALTER TABLE membership_shift_exemption DROP FOREIGN KEY $constraint");
            }
        }

        // Gérer les index - vérifier s'ils existent avant de les supprimer
        $oldIndexes = ['idx_2d388ade1fb354cd', 'idx_2d388adeb03a8386', 'idx_2d388ade24221478'];
        foreach ($oldIndexes as $indexName) {
            $sql = "SELECT COUNT(*) 
                    FROM information_schema.STATISTICS 
                    WHERE INDEX_NAME = '$indexName' 
                    AND TABLE_NAME = 'membership_shift_exemption' 
                    AND TABLE_SCHEMA = DATABASE()";

            $exists = (int) $connection->fetchOne($sql);

            if ($exists > 0) {
                $this->addSql("DROP INDEX $indexName ON membership_shift_exemption");
            }
        }

        // Créer les anciens index s'ils n'existent pas déjà
        $newIndexes = [
            'IDX_BA11DB4C1FB354CD' => 'membership_id',
            'IDX_BA11DB4CB03A8386' => 'created_by_id',
            'IDX_BA11DB4C24221478' => 'shift_exemption_id'
        ];

        foreach ($newIndexes as $indexName => $column) {
            $sql = "SELECT COUNT(*) 
                    FROM information_schema.STATISTICS 
                    WHERE INDEX_NAME = '$indexName' 
                    AND TABLE_NAME = 'membership_shift_exemption' 
                    AND TABLE_SCHEMA = DATABASE()";

            $exists = (int) $connection->fetchOne($sql);

            if ($exists == 0) {
                $this->addSql("CREATE INDEX $indexName ON membership_shift_exemption ($column)");
            }
        }

        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_2D388ADEB03A8386 FOREIGN KEY (created_by_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_2D388ADE24221478 FOREIGN KEY (shift_exemption_id) REFERENCES shift_exemption (id)');
        $this->addSql('ALTER TABLE membership_shift_exemption ADD CONSTRAINT FK_2D388ADE1FB354CD FOREIGN KEY (membership_id) REFERENCES membership (id) ON DELETE CASCADE');
    }
}

