<?php
declare(strict_types=1);

namespace App\Services;

use Adbar\Dot;
use App\Exceptions\DataAnalyzeException;
use App\Services\FileHandler\CSVHandler;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\UnavailableFeature;

class Analyzer extends BaseService
{
    /** @var CSVHandler $csvHandler */
    private $csvHandler;

    /** @var array $expressionInArray */
    private $expressionInArray;

    /** @var string[] $expressionRules */
    private $expressionRules = [
        'fn' => '#^(\*|\+$|\-|\/)$#',
        'a' => '#^(\w{1,}|\d{1,})$#i',
        'b' => '#^(\w{1,}|\d{1,})$#i'
    ];

    public function __construct(CSVHandler $csvHandler)
    {
        $this->csvHandler = $csvHandler;
    }

    /**
     * @throws DataAnalyzeException
     */
    public function getFact(string $securityName = null, string $expressionInJson = null): float
    {
        $this->validateSecurityName($securityName);
        /** @var array $expressionInArray */
        $expressionInArray = $this->convertExpressionToArray($expressionInJson);
        /** @var array $finalExpression */
        $finalExpression = $this->processExpression($expressionInArray);

        return $this->getCalculatedValue($finalExpression);
    }

    /**
     * @throws InvalidArgument
     * @throws UnavailableFeature
     * @throws Exception
     * @throws DataAnalyzeException
     */
    private function validateSecurityName(string $securityName): void
    {
        /** @var array|null $securityRow */
        $securityRow = $this->csvHandler->getCSVRow(
            $this->appKernel->getProjectDir() . '/dataSource/securities.csv',
            'symbol',
            $securityName
        );

        if (!is_array($securityRow)) {
            throw new DataAnalyzeException("The security name '$securityName' seems to be incorrect");
        }
    }

    /**
     * @throws DataAnalyzeException
     */
    private function convertExpressionToArray(string $expressionInJson): array
    {
        /** @var array $expressionInArray */
        $expressionInArray = json_decode($expressionInJson, true);

        if (json_last_error()) {
            throw new DataAnalyzeException("The expression seems to be incorrect");
        }

        return $expressionInArray;
    }

    /**
     * @throws DataAnalyzeException
     */
    private function processExpression(array $expressionInArray): array
    {
        $expressionInDot = new Dot($expressionInArray);

        foreach ($this->expressionRules as $key => $rule) {
            $value = $expressionInDot->get($key);

            if (!$value) {
                throw new DataAnalyzeException("The key '$key' is missing in the expression");
            }

            if (!is_array($value) && !preg_match($rule, (string)$value)) {
                throw new DataAnalyzeException("The value of the key '$key' seems to be incorrect");
            }

            if ($key !== 'fn') {
                $expressionInArray[$key] = $this->getValueOfArgument($expressionInDot->get($key));
            }
        }

        return $expressionInArray;
    }

    /**
     * @return array|int
     * @throws UnavailableFeature
     * @throws DataAnalyzeException
     * @throws Exception
     * @throws InvalidArgument
     */
    private function getValueOfArgument($argumentInitialValue)
    {
        if (is_array($argumentInitialValue)) {
            /** @var array $expression */
            $expression = $this->processExpression($argumentInitialValue);

            return $this->getCalculatedValue($expression);
        }

        if (ctype_digit((string)$argumentInitialValue) && is_numeric($argumentInitialValue)) {
            return $argumentInitialValue;
        }

        //the value of the argument is an attribute
        if (preg_match('#^\w{1,}$#i', $argumentInitialValue)) {
            /** @var array|null $attributeRow */
            $attributeRow = $this->csvHandler->getCSVRow(
                $this->appKernel->getProjectDir() . '/dataSource/attributes.csv',
                'name',
                $argumentInitialValue
            );

            if (!is_array($attributeRow)) {
                throw new DataAnalyzeException("The attribute name '$argumentInitialValue' seems to be incorrect");
            }

            return (int)$attributeRow['id'];
        }

        throw new DataAnalyzeException("Can not identify the argument");
    }

    private function getCalculatedValue(array $finalExpression)
    {
        switch ($finalExpression['fn']) {
            case '+':
                return $finalExpression['a'] + $finalExpression['b'];
                break;
            case '-':
                return $finalExpression['a'] - $finalExpression['b'];
                break;
            case '*':
                return $finalExpression['a'] * $finalExpression['b'];
                break;
            case '/':
                return $finalExpression['a'] / $finalExpression['b'];
                break;
        }
    }
}
