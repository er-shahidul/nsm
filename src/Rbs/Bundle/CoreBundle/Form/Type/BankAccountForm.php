<?php

namespace Rbs\Bundle\CoreBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Form\Transformer\BranchTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BankAccountForm extends AbstractType
{
    private $bankBranchList;
    private $em;
    public function __construct($manager, $getBankBranchList)
    {
        $this->em = $manager;
        $this->bankBranchList = $getBankBranchList;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('code')
            ->add('branch', 'choice', array(
                'choices' => $this->bankBranchList,
                'attr' => array('class' => 'select2me'),
            ))
        ;

        $builder->get('branch')
            ->addModelTransformer(new BranchTransformer($this->em));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\CoreBundle\Entity\BankAccount'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rbs_bundle_corebundle_bankaccount';
    }
}
