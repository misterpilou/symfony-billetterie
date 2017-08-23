<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Booking;
use AppBundle\Entity\Event;
use AppBundle\Entity\Stock;
use AppBundle\Entity\TicketCategory;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class StockManager
 */
class StockManager
{
    private $em;

    /**
     * @var BookingManager $bookingManager
     */
    private $bookingManager;

    /**
     * StockManager constructor.
     *
     * @param EntityManager  $em
     * @param BookingManager $bookingManager
     */
    public function __construct(EntityManager $em, BookingManager $bookingManager)
    {
        $this->em             = $em;
        $this->bookingManager = $bookingManager;
    }

    /**
     * @param Booking $booking
     *
     * @return bool
     */
    public function checkStock(Booking $booking)
    {
        $newTickets = 0;

        foreach ($booking->getTickets() as $ticket) {
            if (null === $ticket->getId()) {
                $newTickets++;
            }
        }

        /** @var Stock $stock */
        $stock = $this->em->getRepository('AppBundle:Stock')->findOneBy(
            [
                'event'    => $booking->getEvent()->getId(),
                'category' => $booking->getTicketCategory()->getId(),
            ]
        );

        if ($stock) {
            $remainingTickets = $stock->getQuantity();
            if ($remainingTickets - $newTickets <= 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * @param Event          $event
     * @param TicketCategory $ticketCategory
     *
     * @return bool
     */
    public function updateStockQuantity(Event $event, TicketCategory $ticketCategory)
    {
        /** @var Stock $stock */
        $stock = $this->em->getRepository('AppBundle:Stock')->findOneBy(
            [
                'event'    => $event->getId(),
                'category' => $ticketCategory->getId(),
            ]
        );
        if ($stock) {
            $quantity = $this->bookingManager->countReservedTickets($stock);

            $stock->setQuantity($stock->getInitialQuantity() - $quantity);

            try {
                $this->em->persist($stock);
                $this->em->flush($stock);
            } catch (\Exception $e) {
                return false;
            }
        } else {
            throw new NotFoundHttpException("Page not found");
        }

        return true;
    }
}
