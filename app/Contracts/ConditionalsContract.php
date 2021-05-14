<?php
declare(strict_types=1);

namespace App\Contracts;

interface ConditionalsContract
{
    public const ALL_COMPARATORS = [
        self::COMPARATOR_EQUAL,
        self::COMPARATOR_NOT_EQUAL,
        self::COMPARATOR_LIKE,
        self::COMPARATOR_NOT_LIKE,
        self::COMPARATOR_IN,
        self::COMPARATOR_NOT_IN,
        self::COMPARATOR_STARTS_WITH,
        self::COMPARATOR_ENDS_WITH,
        self::COMPARATOR_LESS_THAN,
        self::COMPARATOR_LESS_THAN_EQUAL,
        self::COMPARATOR_GREATER_THAN,
        self::COMPARATOR_GREATER_THAN_EQUAL,
    ];

    /**
     * @var string
     */
    public const COMPARATOR_EQUAL = 'EQUAL';

    /**
     * @var string
     */
    public const COMPARATOR_NOT_EQUAL = 'NOT_EQUAL';

    /**
     * @var string
     */
    public const COMPARATOR_LIKE = 'LIKE';

    /**
     * @var string
     */
    public const COMPARATOR_NOT_LIKE = 'NOTLIKE';

    /**
     * @var string
     */
    public const COMPARATOR_IN = 'IN';

    /**
     * @var string
     */
    public const COMPARATOR_NOT_IN = 'NOTIN';

    /**
     * @var string
     */
    public const COMPARATOR_STARTS_WITH = 'STARTS_WITH';

    /**
     * @var string
     */
    public const COMPARATOR_ENDS_WITH = 'ENDS_WITH';

    /**
     * @var string
     */
    public const COMPARATOR_LESS_THAN = 'LESS_THAN';

    /**
     * @var string
     */
    public const COMPARATOR_LESS_THAN_EQUAL = 'LESS_THAN_EQUAL';

    /**
     * @var string
     */
    public const COMPARATOR_GREATER_THAN = 'GREATER_THAN';

    /**
     * @var string
     */
    public const COMPARATOR_GREATER_THAN_EQUAL = 'GREATER_THAN_EQUAL';

    /**
     * Get the field that should be compared against
     *
     * @return string
     */
    public function getComparatorField(): string;

    /**
     * The comparator that should be used when comparing the value
     *
     * @return string
     */
    public function getComparator(): string;

    /**
     * The value that should be compared
     *
     * @return string|int
     */
    public function getComparatorValue();
}
