<?php


namespace Dothiv\BusinessBundle\Model;

class VatRuleModel
{

    /**
     * @var boolean
     */
    private $showReverseChargeNote;

    /**
     * @var int
     */
    private $vatPercent;

    /**
     * @param int     $vatPercent
     * @param boolean $showReverseChargeNote
     */
    public function __construct($vatPercent, $showReverseChargeNote)
    {
        $this->vatPercent            = $vatPercent;
        $this->showReverseChargeNote = $showReverseChargeNote;
    }

    public function showReverseChargeNote()
    {
        return $this->showReverseChargeNote;
    }

    public function vatPercent()
    {
        return $this->vatPercent;
    }
}
