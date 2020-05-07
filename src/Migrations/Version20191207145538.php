<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191207145538 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE estate ADD order_type VARCHAR(10) NOT NULL, ADD estate_type VARCHAR(10) NOT NULL, ADD structure VARCHAR(50) NOT NULL, ADD heating VARCHAR(50) NOT NULL, ADD balcony TINYINT(1) DEFAULT NULL, ADD build DATE DEFAULT NULL, ADD lift TINYINT(1) DEFAULT NULL, ADD estate_condition VARCHAR(50) NOT NULL, ADD floor VARCHAR(20) NOT NULL, ADD view VARCHAR(50) DEFAULT NULL, ADD handicap TINYINT(1) DEFAULT NULL, ADD description LONGTEXT NOT NULL, ADD country VARCHAR(50) DEFAULT NULL, ADD county VARCHAR(50) NOT NULL, ADD city VARCHAR(50) NOT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD permission TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE estate DROP order_type, DROP estate_type, DROP structure, DROP heating, DROP balcony, DROP build, DROP lift, DROP estate_condition, DROP floor, DROP view, DROP handicap, DROP description, DROP country, DROP county, DROP city, DROP address, DROP permission');
    }
}
