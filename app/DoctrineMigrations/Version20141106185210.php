<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Make updated timestamp default to null
 */
class Version20141106185210 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE UserProfileChange ADD CONSTRAINT FK_7D5DEFFCA76ED395 FOREIGN KEY (user_id) REFERENCES User (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE NonProfitRegistration CHANGE updated updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Registrar CHANGE updated updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE PremiumBid CHANGE updated updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Attachment CHANGE updated updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Invoice CHANGE updated updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Banner CHANGE updated updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Domain CHANGE updated updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE UserNotification CHANGE updated updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Config CHANGE updated updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE PremiumBanner CHANGE updated updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE Subscription CHANGE updated updated DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE PayitforwardVoucher CHANGE updated updated DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE Attachment CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE Banner CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE Config CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE Domain CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE Invoice CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE NonProfitRegistration CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE PayitforwardVoucher CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE PremiumBanner CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE PremiumBid CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE Registrar CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE Subscription CHANGE updated updated DATETIME NOT NULL');
        $this->addSql('ALTER TABLE UserNotification CHANGE updated updated DATETIME NOT NULL');
    }
}
