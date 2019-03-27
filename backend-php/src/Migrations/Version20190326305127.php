<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190326305127 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE google_calendar_event CHANGE description description LONGTEXT DEFAULT NULL, CHANGE real_end_time real_end_time DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE google_refresh_token google_refresh_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE google_calendar_event CHANGE description description TINYTEXT NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE real_end_time real_end_time DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE google_refresh_token google_refresh_token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
