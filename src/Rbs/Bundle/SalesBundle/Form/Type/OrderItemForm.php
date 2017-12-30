<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Repository\ItemRepository;
use Rbs\Bundle\SalesBundle\Entity\Agent;
use Rbs\Bundle\SalesBundle\RbsSalesBundle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderItemForm extends AbstractType
{
    /** @var Agent */
    private $agent;

    public function __construct($agent = null)
    {
        $this->agent = $agent;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $itemTypeId = $this->agent && $this->agent->getItemType() ? $this->agent->getItemType()->getId() :  null;

        $builder
            ->add('item', 'entity', array(
                'class' => 'RbsCoreBundle:Item',
                'property' => 'getItemCodeName',
                'required' => true,
                'empty_value' => 'Select Item',
                'query_builder' => function (ItemRepository $repository) use ($itemTypeId)
                {
                    $qb = $repository->createQueryBuilder('i')
                        ->where('i.deletedAt IS NULL')
                        ->orderBy('i.name','ASC')
                        ->join('i.bundles', 'bundles')
                        ->andWhere('bundles.id = :saleBundleId')->setParameter('saleBundleId', RbsSalesBundle::ID);
                    if ($itemTypeId) {
                        $qb->join('i.itemType', 'it');
                        $qb->andWhere($qb->expr()->eq('it.id', $itemTypeId));
                    }

                    return $qb;
                },
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Item should not be blank'
                    )),
                ),
            ))
            ->add('quantity', 'text', array(
                'empty_data' => 0,
            ))
            ->add('totalAmount', 'text', array(
                'read_only' => true
            ))
            ->add('price', 'text', array(
                'read_only' => true
            ))
            ->add('remove', 'button')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\OrderItem'
        ));
    }

    public function getName()
    {
        return 'order_item';
    }
}
