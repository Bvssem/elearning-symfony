<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251224212757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_lesson_completion (user_id INT NOT NULL, lesson_id INT NOT NULL, INDEX IDX_CDC0C584A76ED395 (user_id), INDEX IDX_CDC0C584CDF80196 (lesson_id), PRIMARY KEY (user_id, lesson_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_lesson_completion ADD CONSTRAINT FK_CDC0C584A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_lesson_completion ADD CONSTRAINT FK_CDC0C584CDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_lesson_completion DROP FOREIGN KEY FK_CDC0C584A76ED395');
        $this->addSql('ALTER TABLE user_lesson_completion DROP FOREIGN KEY FK_CDC0C584CDF80196');
        $this->addSql('DROP TABLE user_lesson_completion');
    }
}
