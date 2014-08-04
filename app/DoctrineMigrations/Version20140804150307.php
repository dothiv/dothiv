<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Do not drop domain when banner is removed.
 */
class Version20140804150307 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Domain DROP FOREIGN KEY FK_A0051B3DA7A91E0B");
        $this->addSql("ALTER TABLE Domain ADD CONSTRAINT FK_A0051B3DA7A91E0B FOREIGN KEY (domain) REFERENCES Banner (id) ON DELETE SET NULL");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE Domain DROP FOREIGN KEY FK_A0051B3DA7A91E0B");
        $this->addSql("ALTER TABLE Domain ADD CONSTRAINT FK_A0051B3DA7A91E0B FOREIGN KEY (domain) REFERENCES Banner (id) ON DELETE CASCADE");
    }
}
