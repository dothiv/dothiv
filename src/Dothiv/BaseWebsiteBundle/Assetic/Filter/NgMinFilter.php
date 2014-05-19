<?php

namespace Dothiv\BaseWebsiteBundle\Assetic\Filter;

use Assetic\Filter\BaseProcessFilter;
use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;

/**
 * Applies ngmin to Javascript.
 *
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class NgMinFilter extends BaseProcessFilter
{
    private $bin;

    public function __construct($bin = '/usr/bin/ngmin')
    {
        $this->bin = $bin;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $input  = tempnam(sys_get_temp_dir(), 'assetic_ngmin');
        $output = tempnam(sys_get_temp_dir(), 'assetic_ngmin');

        file_put_contents($input, $asset->getContent());

        $pb = $this->createProcessBuilder()
            ->add($this->bin)
            ->add($input)
            ->add($output)
        ;

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 !== $code) {
            if (file_exists($output)) {
                unlink($output);
            }

            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        if (!file_exists($output)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $asset->setContent(file_get_contents($output));
        unlink($output);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
