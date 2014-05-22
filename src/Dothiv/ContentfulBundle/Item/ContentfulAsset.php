<?php

namespace Dothiv\ContentfulBundle\Item;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="contentful_asset__id_rev_uniq",columns={"id", "revision"})})
 * @ORM\Entity(repositoryClass="Dothiv\ContentfulBundle\Repository\DoctrineContentfulAssetRepository")
 */
class ContentfulAsset implements ContentfulItem
{
    use Traits\ContentfulSys;
    use Traits\ContentfulItem;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('ContentfulAsset: %s, v%d', $this->getId(), $this->getRevision());
    }
}
