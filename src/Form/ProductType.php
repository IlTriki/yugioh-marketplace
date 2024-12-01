<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Enum\ProductStatus;
use App\Form\DataTransformer\ImageToStringTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ProductType extends AbstractType
{
    private ImageToStringTransformer $imageTransformer;

    public function __construct(ImageToStringTransformer $imageTransformer)
    {
        $this->imageTransformer = $imageTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 3, 'max' => 255])
                ]
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'EUR',
                'scale' => 2,
                'attr' => ['step' => '.01'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Positive()
                ]
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('stock', NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual(0)
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('status', EnumType::class, [
                'class' => ProductStatus::class,
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('type', TextType::class, [
                'required' => false,
            ])
            ->add('frameType', TextType::class, [
                'required' => false,
            ])
            ->add('atk', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\GreaterThanOrEqual(0),
                ]
            ])
            ->add('def', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\GreaterThanOrEqual(0),
                ]
            ])
            ->add('level', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThanOrEqual(12)
                ]
            ])
            ->add('race', TextType::class, [
                'required' => false,
            ])
            ->add('attribute', TextType::class, [
                'required' => false,
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'attr' => ['placeholder' => 'Image URL'],
                    'constraints' => [
                        new Assert\Url(),
                    ],
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Images',
            ]);

            $builder->get('images')->addModelTransformer($this->imageTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
