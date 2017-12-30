<?php

namespace Rbs\Bundle\CoreBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Entity\ItemType;
use Rbs\Bundle\CoreBundle\Repository\ItemRepository;
use Rbs\Bundle\SalesBundle\RbsSalesBundle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChickenSetForLocationForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('item', 'entity', array(
                'class' => 'RbsCoreBundle:Item',
                'property' => 'name',
                'required' => true,
                'empty_value' => 'Select Item',
                'query_builder' => function (ItemRepository $repository)
                {
                    return $repository->createQueryBuilder('i')
                        ->join('i.itemType', 'it')
                        ->where('i.deletedAt IS NULL')
                        ->andWhere('it.itemType = :chicken')->setParameter('chicken', ItemType::Chick)
                        ->orderBy('i.name','ASC')
                        ->join('i.bundles', 'bundles')
                        ->andWhere('bundles.id = :saleBundleId')->setParameter('saleBundleId', RbsSalesBundle::ID);
                },
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Item should not be blank'
                    )),
                ),
            ))
            ->add('submit', 'submit', array(
                'attr'     => array('class' => 'btn green')
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\CoreBundle\Entity\ChickenSet'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sale_chicken_set';
    }
}
