<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Updates for new vat rules
 */
class Version20150202182331 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Subscription ADD organization VARCHAR(255) DEFAULT NULL AFTER country');
        $this->addSql('ALTER TABLE PayitforwardOrder ADD organization VARCHAR(255) DEFAULT NULL AFTER country');
        $this->addSql('ALTER TABLE Invoice ADD organization VARCHAR(255) DEFAULT NULL AFTER country');

        $countries = [];
        $csv       = fopen(__DIR__ . '/../../src/Dothiv/BaseWebsiteBundle/Resources/public/data/countries.csv', 'r');
        $first     = true;
        while ($row = fgetcsv($csv)) {
            if ($first) {
                $first = false;
                continue;
            }
            $countries[] = $row;
        }
        fclose($csv);
        foreach (['Subscription', 'PayitforwardOrder'] as $table) {
            // Copy taxNo to vatNo
            $this->addSql("UPDATE $table SET vatNo = taxNo WHERE vatNo IS NULL AND taxNo IS NOT NULL");
            // Add org name
            $this->addSql("UPDATE $table SET organization = '(yes)' WHERE type LIKE '%org%'");
        }
        foreach (['Subscription', 'PayitforwardOrder', 'Invoice'] as $table) {
            // Update countries
            foreach ($countries as $country) {
                list($iso, , $en, $de,) = $country;
                $this->addSql("UPDATE $table SET country = ? WHERE country LIKE ?", [$iso, '%' . $en . '%']);
                $this->addSql("UPDATE $table SET country = ? WHERE country LIKE ?", [$iso, '%' . $de . '%']);
            }
        }

        $this->addSql('ALTER TABLE Subscription DROP type, DROP taxNo');
        $this->addSql('ALTER TABLE PayitforwardOrder DROP type, DROP taxNo');
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE PayitforwardOrder ADD type VARCHAR(255) NOT NULL, ADD taxNo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE Subscription ADD type VARCHAR(255) NOT NULL, ADD taxNo VARCHAR(255) DEFAULT NULL');
        // Update countries
        $countries   = json_decode(file_get_contents(__DIR__ . '/../../src/Dothiv/BaseWebsiteBundle/Resources/public/data/countries-en.json'));
        $euCountries = array_map(function ($el) {
            return "'" . $el[0] . "'";
        }, array_filter($countries, function ($el) {
            return $el[2];
        }));
        foreach (['Subscription', 'PayitforwardOrder'] as $table) {
            // Add type
            $this->addSql("UPDATE $table SET type = 'euorg' WHERE country IN (" . join(',', $euCountries) . ") AND organization IS NOT NULL");
            $this->addSql("UPDATE $table SET type = 'euorgnet' WHERE country IN (" . join(',', $euCountries) . ") AND organization IS NOT NULL AND vatNo IS NOT NULL");
            $this->addSql("UPDATE $table SET type = 'euprivate' WHERE country IN (" . join(',', $euCountries) . ") AND organization IS NULL");
            $this->addSql("UPDATE $table SET type = 'noneu' WHERE country NOT IN (" . join(',', $euCountries) . ")");
            $this->addSql("UPDATE $table SET type = 'deorg' WHERE country = 'DE' AND organization IS NOT NULL");
            // Update countries
            foreach ($countries as $country) {
                list($iso, $name,) = $country;
                $this->addSql("UPDATE $table SET country = ? WHERE country = ?", [$name, $iso]);
            }
            $this->addSql("ALTER TABLE $table DROP organization");
        }
        // Invoice country
        foreach ($countries as $country) {
            list($iso, $name,) = $country;
            $this->addSql("UPDATE Invoice SET country = ? WHERE country = ?", [$name, $iso]);
        }
    }
}
