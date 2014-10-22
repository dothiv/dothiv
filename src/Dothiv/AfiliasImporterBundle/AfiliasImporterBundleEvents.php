<?php

namespace Dothiv\AfiliasImporterBundle;

class AfiliasImporterBundleEvents
{
    // This event stems from the RO_domain-contact-details_hiv_hourly_YYYY-MM-DDTHH.csv files
    const DOMAIN_REGISTERED = 'dothiv_afilias_importer.domain_registered';

    // These events stem from the RO_transactions_ALL_daily_YYYY-MM-DD.txt files
    const DOMAIN_CREATED = 'dothiv_afilias_importer.domain_created';
    const DOMAIN_UPDATED = 'dothiv_afilias_importer.domain_updated';
    const DOMAIN_TRANSFERRED = 'dothiv_afilias_importer.domain_transferred';
    const DOMAIN_DELETED = 'dothiv_afilias_importer.domain_deleted';
} 
