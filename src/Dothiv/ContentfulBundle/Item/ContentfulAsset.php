<?php

namespace Dothiv\ContentfulBundle\Item;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(name="contentful_asset__space_id_rev_uniq",columns={"spaceId", "id", "revision"})},
 *      indexes={
 *          @ORM\Index(name="contentful_asset__spaceId_idx", columns={"spaceId"})
 *      }
 * )
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
        return sprintf('ContentfulAsset: %s@%s, v%d', $this->getId(), $this->getSpaceId(), $this->getRevision());
    }

    /**
     * @return string
     */
    public function getContentfulUrl()
    {
        return sprintf('https://app.contentful.com/spaces/%s/assets/%s', $this->getSpaceId(), $this->getId());
    }

}
