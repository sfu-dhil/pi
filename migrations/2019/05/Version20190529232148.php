<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190529232148 extends AbstractMigration {
    public function up(Schema $schema) : void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE figuration (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, label VARCHAR(120) NOT NULL, description LONGTEXT DEFAULT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, FULLTEXT INDEX IDX_A24173F4EA750E8 (label), FULLTEXT INDEX IDX_A24173F46DE44026 (description), FULLTEXT INDEX IDX_A24173F4EA750E86DE44026 (label, description), UNIQUE INDEX UNIQ_A24173F45E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE video ADD figuration_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CD14545AD FOREIGN KEY (figuration_id) REFERENCES figuration (id)');
        $this->addSql('CREATE INDEX IDX_7CC7DA2CD14545AD ON video (figuration_id)');
    }

    public function down(Schema $schema) : void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2CD14545AD');
        $this->addSql('DROP TABLE figuration');
        $this->addSql('DROP INDEX IDX_7CC7DA2CD14545AD ON video');
        $this->addSql('ALTER TABLE video DROP figuration_id');
    }
}
