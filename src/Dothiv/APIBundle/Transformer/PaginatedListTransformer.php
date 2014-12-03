<?php

namespace Dothiv\APIBundle\Transformer;

use Dothiv\APIBundle\Model\PaginatedList;
use Dothiv\BusinessBundle\Repository\CRUD;
use Dothiv\ValueObject\URLValue;
use Symfony\Component\Routing\RouterInterface;

class PaginatedListTransformer extends AbstractTransformer
{
    /**
     * @param CRUD\PaginatedResult $result
     * @param string               $route
     * @param array                $routeParams
     *
     * @return PaginatedList
     */
    public function transform(CRUD\PaginatedResult $result, $route, array $routeParams)
    {
        $paginatedList = new PaginatedList();
        $paginatedList->setItemsPerPage($result->getItemsPerPage());
        $paginatedList->setTotal($result->getTotal());
        $paginatedList->setJsonLdId(
            new URLValue($this->router->generate(
                    $route,
                    $routeParams,
                    RouterInterface::ABSOLUTE_URL
                )
            )
        );
        if ($result->getNextPageKey()->isDefined()) {
            $paginatedList->setNextPageUrl(
                new URLValue(
                    $this->router->generate(
                        $route,
                        array_merge($routeParams, array('offsetKey' => $result->getNextPageKey()->get())),
                        RouterInterface::ABSOLUTE_URL
                    )
                )
            );
        }
        if ($result->getPrevPageKey()->isDefined()) {
            $paginatedList->setPrevPageUrl(
                new URLValue(
                    $this->router->generate(
                        $route,
                        array_merge($routeParams, array('offsetKey' => $result->getPrevPageKey()->get(), 'sortDir' => 'desc')),
                        RouterInterface::ABSOLUTE_URL
                    )
                )
            );
        }
        return $paginatedList;
    }
}
