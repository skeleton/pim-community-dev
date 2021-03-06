<?php

namespace Pim\Bundle\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use Pim\Bundle\CatalogBundle\Model\AttributeInterface;
use Pim\Bundle\CatalogBundle\Model\AttributeOptionInterface;
use Pim\Bundle\CatalogBundle\Model\AttributeOptionValueInterface;

/**
 * Attribute option
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @ExclusionPolicy("all")
 */
class AttributeOption implements AttributeOptionInterface
{
    /** @var int */
    protected $id;

    /** @var string $code */
    protected $code;

    /**
     * Overrided to change target entity name
     *
     * @var \Pim\Bundle\CatalogBundle\Model\AttributeInterface $attribute
     */
    protected $attribute;

    /** @var ArrayCollection */
    protected $optionValues;

    /**
     * Not persisted, allows to define the value locale
     * @var string $locale
     */
    protected $locale;

    /** @var int */
    protected $sortOrder = 1;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->optionValues = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute(AttributeInterface $attribute = null)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionValues()
    {
        return $this->optionValues;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        if ($sortOrder !== null) {
            $this->sortOrder = $sortOrder;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Override to use default value
     *
     * {@inheritdoc}
     */
    public function __toString()
    {
        $value = $this->getOptionValue();

        return ($value && $value->getValue()) ? $value->getValue() : '['.$this->getCode().']';
    }

    /**
     * {@inheritdoc}
     */
    public function getReference()
    {
        if (null === $this->code) {
            return null;
        }

        return ($this->attribute ? $this->attribute->getCode() : '') . '.' . $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslation()
    {
        $value = $this->getOptionValue();

        if (!$value) {
            $value = new AttributeOptionValue();
            $value->setLocale($this->locale);
            $this->addOptionValue($value);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function addOptionValue(AttributeOptionValueInterface $value)
    {
        $this->optionValues[] = $value;
        $value->setOption($this);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeOptionValue(AttributeOptionValueInterface $value)
    {
        $this->optionValues->removeElement($value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionValue()
    {
        return $this->getOptionValues()->get($this->getLocale()) ?: $this->getOptionValues()->first();
    }
}
