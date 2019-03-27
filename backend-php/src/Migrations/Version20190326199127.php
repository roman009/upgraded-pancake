<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190326199127 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE google_calendar_event_attendee (id INT AUTO_INCREMENT NOT NULL, attendee_id INT NOT NULL, google_calendar_event_id INT NOT NULL, INDEX IDX_629619E3BCFD782A (attendee_id), INDEX IDX_629619E3D9661BB3 (google_calendar_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE google_calendar_event (id INT AUTO_INCREMENT NOT NULL, google_event_id VARCHAR(255) NOT NULL, description TINYTEXT NOT NULL, summary VARCHAR(255) NOT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE google_calendar_event_attendee ADD CONSTRAINT FK_629619E3BCFD782A FOREIGN KEY (attendee_id) REFERENCES attendee (id)');
        $this->addSql('ALTER TABLE google_calendar_event_attendee ADD CONSTRAINT FK_629619E3D9661BB3 FOREIGN KEY (google_calendar_event_id) REFERENCES google_calendar_event (id)');
        $this->addSql('ALTER TABLE user CHANGE google_refresh_token google_refresh_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE google_calendar_event_attendee DROP FOREIGN KEY FK_629619E3D9661BB3');
        $this->addSql('DROP TABLE google_calendar_event_attendee');
        $this->addSql('DROP TABLE google_calendar_event');
        $this->addSql('ALTER TABLE user CHANGE google_refresh_token google_refresh_token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
