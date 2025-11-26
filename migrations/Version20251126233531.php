<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251126233531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enrollment DROP FOREIGN KEY `FK_DBDCD7E1CB944F1A`');
        $this->addSql('DROP INDEX IDX_DBDCD7E1CB944F1A ON enrollment');
        $this->addSql('ALTER TABLE enrollment DROP paid_at, CHANGE status status VARCHAR(50) NOT NULL, CHANGE created_at enrolled_at DATETIME NOT NULL, CHANGE student_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT FK_DBDCD7E1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DBDCD7E1A76ED395 ON enrollment (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enrollment DROP FOREIGN KEY FK_DBDCD7E1A76ED395');
        $this->addSql('DROP INDEX IDX_DBDCD7E1A76ED395 ON enrollment');
        $this->addSql('ALTER TABLE enrollment ADD paid_at DATETIME DEFAULT NULL, CHANGE status status VARCHAR(20) NOT NULL, CHANGE enrolled_at created_at DATETIME NOT NULL, CHANGE user_id student_id INT NOT NULL');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT `FK_DBDCD7E1CB944F1A` FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DBDCD7E1CB944F1A ON enrollment (student_id)');
    }
}
