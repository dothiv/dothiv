<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Rename payitforward voucher table.
 */
class Version20141009102403 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('RENAME TABLE Voucher TO PayitforwardVoucher');
    }

    public function down(Schema $schema)
    {
        $this->addSql('RENAME TABLE PayitforwardVoucher TO Voucher');
    }
}
