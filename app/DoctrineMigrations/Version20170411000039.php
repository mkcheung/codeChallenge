<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170411000039 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE meeting_type (meeting_type_id SERIAL NOT NULL, meeting_type VARCHAR(255) NOT NULL, meeting_type_initials VARCHAR(10) NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, modifiedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(meeting_type_id))');
        $this->addSql('CREATE TABLE region (region_id SERIAL NOT NULL, region VARCHAR(255) NOT NULL, region_abbrev VARCHAR(10) NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, modifiedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(region_id))');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE meeting_type');
        $this->addSql('DROP TABLE region');
    }
}
