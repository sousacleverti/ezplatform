<?php

namespace CustomSearchBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CustomSearchBundle\Entity\ContentType;

class SearchType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        // Get Search entity data to pre-populate a form field
        $searchEntity = $builder->getData();
        $availableContentTypes = $searchEntity->getContentTypes();

        $builder->add('searchQuery', 'text', array(
            'label' => ' ',
            'required' => true,
            'empty_data' => null,
        ));

        $builder->add('contentTypes', 'choice', array(
            'choices' => $availableContentTypes,
            'multiple' => true,
            'required' => true,
            'empty_data' => null,
            'expanded' => false, // true -> Show as checkboxes; false -> Show as combobox
            'label' => ' ',
            'data' => array('16', '17') // preselected data: 16->Article; 17->Blog
       ));
        $builder->add('save', 'submit', array('label' => 'Search'));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CustomSearchBundle\Entity\Search',
        ));
    }

    public function getName() {
        return 'search';
    }

}
