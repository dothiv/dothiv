<?php


namespace Dothiv\BusinessBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Parses WHOIS reports
 */
class WhoisReportParser
{

    /**
     * @param string $data
     *
     * @return ArrayCollection
     */
    public function parse($data)
    {
        $report = new ArrayCollection();
        foreach (explode("\n", $data) as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }
            list($k, $v) = explode(':', $line, 2);
            $k = trim($k);
            $v = trim($v);
            if (empty($v)) {
                continue;
            }
            if ($report->containsKey($k)) {
                if (!($report->get($k) instanceof ArrayCollection)) {
                    $report->set($k, new ArrayCollection(array($report->get($k))));
                }
                $report->get($k)->add($v);
            } else {
                $report->set($k, $v);
            }
        }
        return $report;
    }
} 
