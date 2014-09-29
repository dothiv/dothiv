<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Rename activeBanner columen in Domain
 */
class Version20140929183137 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Domain DROP FOREIGN KEY FK_A0051B3DA7A91E0B');
        $this->addSql('DROP INDEX UNIQ_A0051B3DA7A91E0B ON Domain');
        $this->addSql('ALTER TABLE Domain CHANGE domain activeBanner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Domain ADD CONSTRAINT FK_A0051B3D3EF37E2F FOREIGN KEY (activeBanner_id) REFERENCES Banner (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A0051B3D3EF37E2F ON Domain (activeBanner_id)');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE Domain DROP FOREIGN KEY FK_A0051B3D3EF37E2F');
        $this->addSql('DROP INDEX UNIQ_A0051B3D3EF37E2F ON Domain');
        $this->addSql('ALTER TABLE Domain CHANGE activebanner_id domain INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Domain ADD CONSTRAINT FK_A0051B3DA7A91E0B FOREIGN KEY (domain) REFERENCES Banner (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A0051B3DA7A91E0B ON Domain (domain)');
    }
}
