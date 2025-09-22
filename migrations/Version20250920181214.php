<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250920181214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, ADD notes LONGTEXT DEFAULT NULL, CHANGE etudiant_id etudiant_id INT NOT NULL, CHANGE classe_id classe_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inscription DROP created_at, DROP updated_at, DROP notes, CHANGE etudiant_id etudiant_id INT DEFAULT NULL, CHANGE classe_id classe_id INT DEFAULT NULL');
    }
}
