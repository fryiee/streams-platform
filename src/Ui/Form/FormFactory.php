<?php namespace Anomaly\Streams\Platform\Ui\Form;

use Illuminate\Contracts\Container\Container;

/**
 * Class FormFactory
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Ui\Form
 */
class FormFactory
{

    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

    /**
     * Create a new FormFactory instance.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Make the form.
     *
     * @param null  $builder
     * @param array $parameters
     * @return FormCriteria
     */
    public function make($builder = null, array $parameters = [])
    {
        $builder = $this->container->make($builder ?: 'Anomaly\Streams\Platform\Ui\Form\FormBuilder');

        $criteria = substr(get_class($builder), 0, -7) . 'Criteria';

        if (!class_exists($criteria)) {
            $criteria = 'Anomaly\Streams\Platform\Ui\Form\FormCriteria';
        }

        return $this->container->make(
            $criteria,
            [
                'builder'    => $builder,
                'parameters' => $parameters
            ]
        );
    }
}