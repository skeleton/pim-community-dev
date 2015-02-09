<?php

namespace Pim\Bundle\CatalogBundle\Doctrine\ORM\Filter;

use Pim\Bundle\CatalogBundle\Doctrine\ORM\Join\CompletenessJoin;
use Pim\Bundle\CatalogBundle\Exception\InvalidArgumentException;
use Pim\Bundle\CatalogBundle\Query\Filter\FieldFilterInterface;
use Pim\Bundle\CatalogBundle\Query\Filter\Operators;

/**
 * Completeness filter
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CompletenessFilter extends AbstractFilter implements FieldFilterInterface
{
    /** @var array */
    protected $supportedFields;

    /**
     * Instanciate the base filter
     *
     * @param array $supportedFields
     * @param array $supportedOperators
     */
    public function __construct(
        array $supportedFields = [],
        array $supportedOperators = []
    ) {
        $this->supportedFields    = $supportedFields;
        $this->supportedOperators = $supportedOperators;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldFilter($field, $operator, $value, $locale = null, $scope = null, $options = [])
    {
        $this->checkValue($field, $value, $locale, $scope);

        $alias = 'filterCompleteness';
        $field = $alias.'.ratio';
        $util = new CompletenessJoin($this->qb);
        $util->addJoins($alias, $locale, $scope);

        if (Operators::EQUALS === $operator) {
            $this->qb->andWhere($this->qb->expr()->eq($field, '100'));
        } elseif (Operators::LOWER_THAN === $operator) {
            $this->qb->andWhere($this->qb->expr()->lt($field, $value));
        }

        return $this;
    }

    /**
     * Check if value is valid
     *
     * @param string      $field
     * @param mixed       $value
     * @param string|null $locale
     * @param string|null $scope
     */
    protected function checkValue($field, $value, $locale, $scope)
    {
        if (!is_numeric($value) && null !== $value) {
            throw InvalidArgumentException::numericExpected($field, 'filter', 'completeness', gettype($value));
        }

        if (null === $locale || null === $scope) {
            throw new \InvalidArgumentException(
                'Cannot prepare condition on completenesses without locale and scope'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsField($field)
    {
        return in_array($field, $this->supportedFields);
    }
}
