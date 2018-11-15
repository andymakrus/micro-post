<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 25/10/2018
 * Time: 14:00
 */

namespace App\Form;


use App\Entity\MicroPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MicroPostType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		parent::buildForm($builder, $options);

		$builder->add('text', TextareaType::class, ['label' => false])
			->add('save', SubmitType::class);

	}

	public function configureOptions(OptionsResolver $resolver)
	{
		parent::configureOptions($resolver);
		$resolver->setDefaults([
			'data_class' => MicroPost::class
		]);
	}


}