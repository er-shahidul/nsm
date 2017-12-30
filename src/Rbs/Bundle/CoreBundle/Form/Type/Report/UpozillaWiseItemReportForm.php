<?php

namespace Rbs\Bundle\CoreBundle\Form\Type\Report;

use Doctrine\ORM\EntityRepository;
use Rbs\Bundle\CoreBundle\Repository\ItemRepository;
use Rbs\Bundle\SalesBundle\RbsSalesBundle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpozillaWiseItemReportForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year', 'text', array(
                'attr' => array(
                    'placeholder' => 'Select Year'
                ),
                'read_only'=>true,

            ))
            ->add('month', 'text', array(
                'attr' => array(
                    'placeholder' => 'Select Month'
                ),
                'read_only'=>true
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(

        ));
    }

    public function getName()
    {
        return 'upozilla_report';
    }
}
