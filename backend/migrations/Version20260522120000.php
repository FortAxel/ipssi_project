<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260522120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Favorites and reading progress (F4)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE favorite (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_A0E69B5A76ED395 (user_id), INDEX IDX_A0E69B5AA5D4036 (story_id), UNIQUE INDEX uniq_favorite_user_story (user_id, story_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reading_progress (id INT AUTO_INCREMENT NOT NULL, last_page_number INT NOT NULL, updated_at DATETIME NOT NULL, user_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_F3F9B9F0A76ED395 (user_id), INDEX IDX_F3F9B9F0AA5D4036 (story_id), UNIQUE INDEX uniq_progress_user_story (user_id, story_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_A0E69B5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_A0E69B5AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reading_progress ADD CONSTRAINT FK_F3F9B9F0A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reading_progress ADD CONSTRAINT FK_F3F9B9F0AA5D4036 FOREIGN KEY (story_id) REFERENCES story (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_A0E69B5A76ED395');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_A0E69B5AA5D4036');
        $this->addSql('ALTER TABLE reading_progress DROP FOREIGN KEY FK_F3F9B9F0A76ED395');
        $this->addSql('ALTER TABLE reading_progress DROP FOREIGN KEY FK_F3F9B9F0AA5D4036');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE reading_progress');
    }
}
