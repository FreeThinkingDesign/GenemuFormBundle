<?php

namespace Genemu\Bundle\FormBundle\Form\Core\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\Extension\Core\Type\DateType as BaseDateType;

/**
 * A Form type that just renders the field as a p tag. This is useful for forms where certain field
 * need to be shown but not editable.
 *
 * @author Adam Kuśmierz <adam@kusmierz.be>
 */
class PlainType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget'  => 'field',
            'read_only' => true,
            'disabled' => true,
            'date_format' => null,
            'date_pattern' => null,
            'time_format' => null,
            'with_time' => true,
            'attr' => array(
                'class' => $this->getName()
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $value = $form->getViewData();

        // set string representation
        if (true === $value) {
            $value = 'true';
        } elseif (false === $value) {
            $value = 'false';
        } elseif (null === $value) {
            $value = 'null';
        } elseif (is_array($value)) {
            $value = implode(', ', $value);
        } elseif ($value instanceof \DateTime) {
            $dateFormat = is_int($options['date_format']) ? $options['date_format'] : BaseDateType::DEFAULT_FORMAT;
            $timeFormat = is_int($options['time_format']) ? $options['time_format'] : BaseDateType::DEFAULT_FORMAT;
            $calendar   = \IntlDateFormatter::GREGORIAN;
            $pattern    = is_string($options['date_pattern']) ? $options['date_pattern'] : null;

            $formatter  = new \IntlDateFormatter(
                \Locale::getDefault(),
                $dateFormat,
                $timeFormat,
                'UTC',
                $calendar,
                $pattern
            );
            $formatter->setLenient(false);
            $value = $formatter->format($value);
        } elseif (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $value = $value->__toString();
            } else {
                $value = get_class($value);
            }
        }

        $view->vars['value'] = (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'genemu_plain';
    }
}
