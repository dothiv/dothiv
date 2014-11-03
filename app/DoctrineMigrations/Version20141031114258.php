<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add non-profit flag to domain
 */
class Version20141031114258 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Domain ADD nonprofit TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE Domain DROP nonprofit');
    }
}
