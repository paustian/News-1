<?php
/**
 * News.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\NewsModule\Form\Type\Finder\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\CategoriesModule\Form\Type\CategoriesType;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use MU\NewsModule\Helper\FeatureActivationHelper;

/**
 * Message finder form type base class.
 */
abstract class AbstractMessageFinderType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;

    /**
     * MessageFinderType constructor.
     *
     * @param TranslatorInterface $translator Translator service instance
     * @param FeatureActivationHelper $featureActivationHelper FeatureActivationHelper service instance
     */
    public function __construct(TranslatorInterface $translator, FeatureActivationHelper $featureActivationHelper)
    {
        $this->setTranslator($translator);
        $this->featureActivationHelper = $featureActivationHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('objectType', HiddenType::class, [
                'data' => $options['object_type']
            ])
            ->add('editor', HiddenType::class, [
                'data' => $options['editor_name']
            ])
        ;

        if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $options['object_type'])) {
            $this->addCategoriesField($builder, $options);
        }
        $this->addImageFields($builder, $options);
        $this->addPasteAsField($builder, $options);
        $this->addSortingFields($builder, $options);
        $this->addAmountField($builder, $options);
        $this->addSearchField($builder, $options);

        $builder
            ->add('update', SubmitType::class, [
                'label' => $this->__('Change selection'),
                'icon' => 'fa-check',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->add('cancel', SubmitType::class, [
                'label' => $this->__('Cancel'),
                'icon' => 'fa-times',
                'attr' => [
                    'class' => 'btn btn-default',
                    'formnovalidate' => 'formnovalidate'
                ]
            ])
        ;
    }

    /**
     * Adds a categories field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addCategoriesField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('categories', CategoriesType::class, [
            'label' => $this->__('Categories') . ':',
            'empty_data' => [],
            'attr' => [
                'class' => 'category-selector',
                'title' => $this->__('This is an optional filter.')
            ],
            'help' => $this->__('This is an optional filter.'),
            'required' => false,
            'multiple' => true,
            'module' => 'MUNewsModule',
            'entity' => ucfirst($options['object_type']) . 'Entity',
            'entityCategoryClass' => 'MU\NewsModule\Entity\\' . ucfirst($options['object_type']) . 'CategoryEntity',
            'showRegistryLabels' => true
        ]);
    }

    /**
     * Adds fields for image insertion options.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addImageFields(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('onlyImages', CheckboxType::class, [
            'label' => $this->__('Only images'),
            'empty_data' => false,
            'help' => $this->__('Enable this option to insert images'),
            'required' => false
        ]);
        $builder->add('imageField', ChoiceType::class, [
            'label' => $this->__('Image field'),
            'empty_data' => 'imageUpload1',
            'help' => $this->__('You can switch between different image fields'),
            'choices' => [
                $this->__('Image upload 1') => 'imageUpload1',
                $this->__('Image upload 2') => 'imageUpload2',
                $this->__('Image upload 3') => 'imageUpload3',
                $this->__('Image upload 4') => 'imageUpload4'
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a "paste as" field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addPasteAsField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('pasteAs', ChoiceType::class, [
            'label' => $this->__('Paste as') . ':',
            'empty_data' => 1,
            'choices' => [
                $this->__('Relative link to the message') => 1,
                $this->__('Absolute url to the message') => 2,
                $this->__('ID of message') => 3,
                $this->__('Relative link to the image') => 6,
                $this->__('Image') => 7,
                $this->__('Image with relative link to the message') => 8,
                $this->__('Image with absolute url to the message') => 9
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds sorting fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSortingFields(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('sort', ChoiceType::class, [
                'label' => $this->__('Sort by') . ':',
                'empty_data' => '',
                'choices' => [
                    $this->__('Workflow state') => 'workflowState',
                    $this->__('Title') => 'title',
                    $this->__('Image upload 1') => 'imageUpload1',
                    $this->__('Display on index') => 'displayOnIndex',
                    $this->__('Creation date') => 'createdDate',
                    $this->__('Creator') => 'createdBy',
                    $this->__('Update date') => 'updatedDate',
                    $this->__('Updater') => 'updatedBy'
                ],
                'multiple' => false,
                'expanded' => false
            ])
            ->add('sortdir', ChoiceType::class, [
                'label' => $this->__('Sort direction') . ':',
                'empty_data' => 'asc',
                'choices' => [
                    $this->__('Ascending') => 'asc',
                    $this->__('Descending') => 'desc'
                ],
                'multiple' => false,
                'expanded' => false
            ])
        ;
    }

    /**
     * Adds a page size field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addAmountField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('num', ChoiceType::class, [
            'label' => $this->__('Page size') . ':',
            'empty_data' => 20,
            'attr' => [
                'class' => 'text-right'
            ],
            'choices' => [
                $this->__('5') => 5,
                $this->__('10') => 10,
                $this->__('15') => 15,
                $this->__('20') => 20,
                $this->__('30') => 30,
                $this->__('50') => 50,
                $this->__('100') => 100
            ],
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds a search field.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSearchField(FormBuilderInterface $builder, array $options = [])
    {
        $builder->add('q', SearchType::class, [
            'label' => $this->__('Search for') . ':',
            'required' => false,
            'attr' => [
                'maxlength' => 255
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'munewsmodule_messagefinder';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'object_type' => 'message',
                'editor_name' => 'ckeditor'
            ])
            ->setRequired(['object_type', 'editor_name'])
            ->setAllowedTypes('object_type', 'string')
            ->setAllowedTypes('editor_name', 'string')
            ->setAllowedValues('editor_name', ['ckeditor', 'quill', 'summernote', 'tinymce'])
        ;
    }
}
