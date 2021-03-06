<?php

namespace Rbs\Bundle\SalesBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class OrderIdDataTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Order|null $bankAccount
     * @return string
     */
    public function transform($bankAccount)
    {
        if (null === $bankAccount) {
            return '';
        }

        return $bankAccount->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $bankAccountId
     * @return Order|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($bankAccountId)
    {
        // no issue number? It's optional, so that's ok
        if (!$bankAccountId) {
            return;
        }

        $order = $this->manager
            ->getRepository('RbsSalesBundle:Order')
            // query for the issue with this id
            ->find($bankAccountId)
        ;

        if (null === $order) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An order with number "%s" does not exist!',
                $bankAccountId
            ));
        }

        return $order;
    }
}