<?php

namespace Rbs\Bundle\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\CoreBundle\Entity\SaleIncentive;
use Rbs\Bundle\CoreBundle\Entity\TransportIncentive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransportIncentiveForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount')
            ->add('itemType', 'entity', array(
                'class' => 'Rbs\Bundle\CoreBundle\Entity\ItemType',
                'query_builder' => function(EntityRepository $itemTypeRepository) {
                    return $itemTypeRepository->createQueryBuilder('it')
                        ->where("it.deletedAt IS NULL");
                },
                'property' => 'itemType',
            ))
            ->add('depo', 'entity', array(
                'class' => 'Rbs\Bundle\CoreBundle\Entity\Depo',
                'query_builder' => function(EntityRepository $depoRepository) {
                    return $depoRepository->createQueryBuilder('d')
                        ->where("d.deletedAt IS NULL");
                },
                'property' => 'name',
            ))
            ->add('level1', 'entity', array(
                'label' => 'District',
                'class' => 'Rbs\Bundle\CoreBundle\Entity\Location',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.level = :level')->setParameter('level', 4)->orderBy('a.name');
                },
                'attr' => array(
                    'class' => 'zilla-selector select2me'
                ),
                'required' => false,
                'mapped' => false
            ))
            ->add('level2', 'entity', array(
                'label' => 'Station',
                'class' => 'Rbs\Bundle\CoreBundle\Entity\Location',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('a')
                        ->where('a.level = :level')->setParameter('level', 5)->orderBy('a.name');
                    return $qb;
                },
                'attr' => array(
                    'class' => 'thana-selector select2me'
                ),
                'placeholder' => 'Choose an option',
                'required' => false,
                'mapped' => false
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
            'data_class' => 'Rbs\Bundle\CoreBundle\Entity\TransportIncentive'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'transport_incentive';
    }
}
