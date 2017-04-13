<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170413195658 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql("INSERT INTO region (region_abbrev, region, createdat, modifiedat) VALUES ('AL', 'Alabama', NOW(), NOW()),('AK', 'Alaska', NOW(), NOW()),('AZ', 'Arizona', NOW(), NOW()),('AR', 'Arkansas', NOW(), NOW()),('CA', 'California', NOW(), NOW()),('CO', 'Colorado', NOW(), NOW()),('CT', 'Connecticut', NOW(), NOW()),('DE', 'Delaware', NOW(), NOW()),('FL', 'Florida', NOW(), NOW()),('GA', 'Georgia', NOW(), NOW()),('HI', 'Hawaii', NOW(), NOW()),('ID', 'Idaho', NOW(), NOW()),('IL', 'Illinois', NOW(), NOW()),('IN', 'Indiana', NOW(), NOW()),('IA', 'Iowa', NOW(), NOW()),('KS', 'Kansas', NOW(), NOW()),('KY', 'Kentucky', NOW(), NOW()),('LA', 'Louisiana', NOW(), NOW()),('ME', 'Maine', NOW(), NOW()),('MD', 'Maryland', NOW(), NOW()),('MA', 'Massachusetts', NOW(), NOW()),('MI', 'Michigan', NOW(), NOW()),('MN', 'Minnesota', NOW(), NOW()),('MS', 'Mississippi', NOW(), NOW()),('MO', 'Missouri', NOW(), NOW()),('MT', 'Montana', NOW(), NOW()),('NE', 'Nebraska', NOW(), NOW()),('NV', 'Nevada', NOW(), NOW()),('NH', 'New Hampshire', NOW(), NOW()),('NJ', 'New Jersey', NOW(), NOW()),('NM', 'New Mexico', NOW(), NOW()),('NY', 'New York', NOW(), NOW()),('NC', 'North Carolina', NOW(), NOW()),('ND', 'North Dakota', NOW(), NOW()),('OH', 'Ohio', NOW(), NOW()),('OK', 'Oklahoma', NOW(), NOW()),('OR', 'Oregon', NOW(), NOW()),('PA', 'Pennsylvania', NOW(), NOW()),('RI', 'Rhode Island', NOW(), NOW()),('SC', 'South Carolina', NOW(), NOW()),('SD', 'South Dakota', NOW(), NOW()),('TN', 'Tennessee', NOW(), NOW()),('TX', 'Texas', NOW(), NOW()),('UT', 'Utah', NOW(), NOW()),('VT', 'Vermont', NOW(), NOW()),('VA', 'Virginia', NOW(), NOW()),('WA', 'Washington', NOW(), NOW()),('WV', 'West Virginia', NOW(), NOW()),('WI', 'Wisconsin', NOW(), NOW()),('WY', 'Wyoming', NOW(), NOW()),('AS', 'American Samoa', NOW(), NOW()),('DC', 'District Of Columbia', NOW(), NOW()),('FM', 'Federated States Of Micronesia', NOW(), NOW()),('GU', 'Guam', NOW(), NOW()),('MH', 'Marshall Islands', NOW(), NOW()),('MP', 'Northern Mariana Islands', NOW(), NOW()),('PW', 'Palau', NOW(), NOW()),('PR', 'Puerto Rico', NOW(), NOW()),('VI', 'Virgin Islands', NOW(), NOW()),('AE', 'Armed Forces Africa', NOW(), NOW()),('AA', 'Armed Forces Americas', NOW(), NOW()),('AE', 'Armed Forces Canada', NOW(), NOW()),('AE', 'Armed Forces Europe', NOW(), NOW()),('AE', 'Armed Forces Middle East', NOW(), NOW()),('AP', 'Armed Forces Pacific', NOW(), NOW());");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        $this->addSql("DELETE from region;");

    }
}
