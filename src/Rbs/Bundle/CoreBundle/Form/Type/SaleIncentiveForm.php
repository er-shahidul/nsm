<?php

namespace Rbs\Bundle\CoreBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\CoreBundle\Entity\SaleIncentive;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleIncentiveForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount')
            ->add('quantity')
            ->add('durationType', 'choice', array(
                'choices'  => array(
                    'MONTH' => SaleIncentive::MONTH,
                    'YEAR' => SaleIncentive::YEAR,
                ),
                'data' => SaleIncentive::MONTH
            ))
            ->add('category', 'entity', array(
                'class' => 'Rbs\Bundle\CoreBundle\Entity\Category',
                'query_builder' => function(EntityRepository $categoryRepository) {
                    return $categoryRepository->createQueryBuilder('c')
                        ->where("c.deletedAt IS NULL");
                },
                'property' => 'name',
                'multiple' => true
            ))
            ->add('group', 'choice', array(
                'choices'  => array(
                    'GROUP ONE' => SaleIncentive::GROUP_ONE,
                    'GROUP_TWO' => SaleIncentive::GROUP_TWO,
                    'GROUP_THREE' => SaleIncentive::GROUP_THREE,
                    'GROUP_FOUR' => SaleIncentive::GROUP_FOUR,
                )
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
            'data_class' => 'Rbs\Bundle\CoreBundle\Entity\SaleIncentive'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sale_incentive';
    }
}
