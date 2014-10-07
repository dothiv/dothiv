<?php


namespace Dothiv\PayitforwardBundle\Service;

use Doctrine\ORM\EntityManager;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Repository\VoucherRepositoryInterface;

class OrderService implements OrderServiceInterface
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var VoucherRepositoryInterface
     */
    private $voucherRepo;

    /**
     * @param VoucherRepositoryInterface $voucherRepo
     */
    public function __construct(VoucherRepositoryInterface $voucherRepo, EntityManager $em)
    {
        $this->em          = $em;
        $this->voucherRepo = $voucherRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function assignVouchers(Order $order)
    {
        $requiredVouchers = $order->getNumVouchers();
        $assignedVouchers = $this->voucherRepo->findAssigned($order);
        $requiredVouchers -= $assignedVouchers->count();
        if ($requiredVouchers <= 0) {
            return $assignedVouchers;
        }
        $this->em->transactional(function (EntityManager $em) use ($order, $assignedVouchers) {
            $vouchers = $this->voucherRepo->findUnassigned($order->getNumVouchers());
            foreach ($vouchers as $voucher) {
                $voucher->setOrder($order);
                $this->voucherRepo->persist($voucher);
                $assignedVouchers->add($voucher);
            }
            $this->voucherRepo->flush();
        });
        return $assignedVouchers;
    }

} 
