<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260108090441 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Vérifier si la contrainte existe avant de la supprimer
        $connection = $this->connection;
        $sql = "SELECT COUNT(*) 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE CONSTRAINT_NAME = 'FK_6B5F3126BB70BC0E' 
                AND TABLE_NAME = 'shiftfreelog' 
                AND TABLE_SCHEMA = DATABASE()";

        $exists = (int) $connection->fetchOne($sql);

        if ($exists > 0) {
            $this->addSql('ALTER TABLE shiftfreelog DROP FOREIGN KEY FK_6B5F3126BB70BC0E');
        }

        $this->addSql('ALTER TABLE shiftfreelog ADD CONSTRAINT FK_6B5F3126BB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Vérifier si la contrainte existe avant de la supprimer
        $connection = $this->connection;
        $sql = "SELECT COUNT(*) 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE CONSTRAINT_NAME = 'FK_6B5F3126BB70BC0E' 
                AND TABLE_NAME = 'shiftfreelog' 
                AND TABLE_SCHEMA = DATABASE()";

        $exists = (int) $connection->fetchOne($sql);

        if ($exists > 0) {
            $this->addSql('ALTER TABLE shiftfreelog DROP FOREIGN KEY FK_6B5F3126BB70BC0E');
        }

        $this->addSql('ALTER TABLE shiftfreelog ADD CONSTRAINT FK_6B5F3126BB70BC0E FOREIGN KEY (shift_id) REFERENCES shift (id) ON DELETE CASCADE');
    }
}

