<?php

namespace FUxCon2013\ProjectsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('attr' => array(
                'class' => 'title-field input-block-level'
            )))
            ->add('picture', 'file', array('required' => false))
            ->add('startDate', 'date_entry')
            ->add('endDate', 'date_entry')
            ->add('about', null, array('attr' => array(
                'class' => 'about-field input-block-level'
            )))
            ->add('tags', 'tags_entry', array('attr' => array(
                'class' => 'tags-field input-block-level'
            )))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FUxCon2013\ProjectsBundle\Entity\Project'
        ));
    }

    public function getName()
    {
        return 'fuxcon2013_projectsbundle_projecttype';
    }
}
