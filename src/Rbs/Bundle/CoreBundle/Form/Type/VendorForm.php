<?php

namespace Rbs\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VendorForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vendorName')
            ->add('vendorAddress')
            ->add('contractPerson')
            ->add('contractNo')
            ->add('email')
            ->add('tradeLicenseNo')
            ->add('tinCertificateNo')
            ->add('vatCertificateNo')
            ->add('bankAccountNo')
            ->add('bankAccountName')
            ->add('branchName')
            ->add('PaymentType')
            ->add('area', null, array(
                'attr' => array('class' => 'select2me')
            ))
            ->add('itemTypes')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\CoreBundle\Entity\Vendor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rbs_bundle_corebundle_vendor';
    }
}
