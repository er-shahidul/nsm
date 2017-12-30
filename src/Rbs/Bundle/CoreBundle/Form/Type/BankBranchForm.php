<?php

namespace Rbs\Bundle\CoreBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Repository\BankBranchRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BankBranchForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bank = $options['data'] && method_exists($options['data'], 'getBank') ? $options['data']->getBank() : null;

        $builder
            ->add('name')
            ->add('bank', 'entity', array(
                'class' => 'Rbs\Bundle\CoreBundle\Entity\Bank',
                'property' => 'name',
                'required' => true,
                'disabled' => $bank
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\CoreBundle\Entity\BankBranch'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rbs_bundle_corebundle_bankbranch';
    }
}
