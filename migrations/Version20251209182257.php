<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251209182257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY `FK_F87474F3591CC992`');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('ALTER TABLE enrollment DROP FOREIGN KEY `FK_DBDCD7E1A76ED395`');
        $this->addSql('DROP INDEX IDX_DBDCD7E1A76ED395 ON enrollment');
        $this->addSql('ALTER TABLE enrollment DROP status, CHANGE user_id student_id INT NOT NULL');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT FK_DBDCD7E1CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DBDCD7E1CB944F1A ON enrollment (student_id)');
        $this->addSql('CREATE UNIQUE INDEX unique_enrollment ON enrollment (student_id, course_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, video_url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, lesson_order INT NOT NULL, course_id INT NOT NULL, INDEX IDX_F87474F3591CC992 (course_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT `FK_F87474F3591CC992` FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE enrollment DROP FOREIGN KEY FK_DBDCD7E1CB944F1A');
        $this->addSql('DROP INDEX IDX_DBDCD7E1CB944F1A ON enrollment');
        $this->addSql('DROP INDEX unique_enrollment ON enrollment');
        $this->addSql('ALTER TABLE enrollment ADD status VARCHAR(50) NOT NULL, CHANGE student_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT `FK_DBDCD7E1A76ED395` FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DBDCD7E1A76ED395 ON enrollment (user_id)');
    }
}
