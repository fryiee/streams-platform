<?php namespace Anomaly\Streams\Platform\Ui\Form;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Illuminate\Container\Container;
use Illuminate\Validation\Factory;

/**
 * Class FormExtender
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Ui\Form
 */
class FormExtender
{

    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * Create a new FormExtender instance.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Extend the validation factory.
     *
     * @param Factory     $factory
     * @param FormBuilder $builder
     */
    public function extend(Factory $factory, FormBuilder $builder)
    {
        foreach ($builder->getFormFields() as $fieldType) {
            $this->registerValidators($factory, $builder, $fieldType);
        }
    }

    /**
     * Register field's custom validators.
     *
     * @param Factory     $factory
     * @param FormBuilder $builder
     * @param FieldType   $fieldType
     */
    protected function registerValidators(Factory $factory, FormBuilder $builder, FieldType $fieldType)
    {
        foreach ($fieldType->getValidators() as $rule => $validator) {

            $handler = array_get($validator, 'handler');

            if (class_exists($handler) && class_implements($handler, 'Illuminate\Contracts\Bus\SelfHandling')) {
                $handler .= '@handle';
            }

            $factory->extend(
                $rule,
                function ($attribute, $value, $parameters) use ($handler, $builder) {
                    return $this->container->call(
                        $handler,
                        compact('attribute', 'value', 'parameters', 'builder')
                    );
                },
                array_get($validator, 'message')
            );
        }
    }
}
