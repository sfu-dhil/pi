<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190923170900 extends AbstractMigration {
    public function up(Schema $schema) : void {
        $this->addSql('ALTER TABLE video ADD hidden TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema) : void {
        $this->addSql('ALTER TABLE video DROP hidden');
    }
}
