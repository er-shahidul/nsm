<?php
namespace Rbs\Bundle\CoreBundle\Form\Transformer;

use Doctrine\Common\Persistence\ObjectManager;
use Rbs\Bundle\CoreBundle\Entity\BankBranch;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BranchTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object ($branch) to a string (number).
     *
     * @param  BankBranch|null $branch
     * @return string
     */
    public function transform($branch)
    {
        if (null === $branch) {
            return '';
        }

        return $branch->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $branchNo
     * @return BankBranch|null
     * @throws TransformationFailedException if object (branch) is not found.
     */
    public function reverseTransform($branchNo)
    {
        // no issue number? It's optional, so that's ok
        if (!$branchNo) {
            return;
        }

        $branch = $this->manager
            ->getRepository('RbsCoreBundle:BankBranch')
            // query for the issue with this id
            ->find($branchNo)
        ;

        if (null === $branch) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $branchNo
            ));
        }

        return $branch;
    }
}