<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Insert settings for dotHIV shop
 */
class Version20141216202821 extends AbstractMigration
{

    private $config = array(
        array('shop.price.eur', '14500'),
        array('shop.price.usd', '18000'),
        array('shop.promo.name4life.eur.mod', '-13000'),
        array('shop.promo.name4life.usd.mod', '-16100'),
    );

    public function up(Schema $schema)
    {
        foreach ($this->config as $v) {
            $this->addSql('INSERT INTO Config (name, value, created) VALUES (?, ?, NOW())', $v);
        }

    }

    public function down(Schema $schema)
    {
        foreach ($this->config as $v) {
            $this->addSql('DELETE FROM Config WHERE name = ?', array($v[0]));
        }
    }
}
