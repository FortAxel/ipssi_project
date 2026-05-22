<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260522140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Reading progress: started_at, last_read_at, is_completed';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reading_progress ADD is_completed TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE reading_progress ADD started_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE reading_progress SET started_at = updated_at');
        $this->addSql('ALTER TABLE reading_progress MODIFY started_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE reading_progress CHANGE updated_at last_read_at DATETIME NOT NULL');
        $this->addSql(
            'UPDATE reading_progress SET is_completed = 1 WHERE last_page_number >= (
                SELECT cnt FROM (SELECT COUNT(*) AS cnt FROM page p WHERE p.story_id = reading_progress.story_id) AS sub
            )',
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reading_progress DROP started_at');
        $this->addSql('ALTER TABLE reading_progress DROP is_completed');
        $this->addSql('ALTER TABLE reading_progress CHANGE last_read_at updated_at DATETIME NOT NULL');
    }
}
