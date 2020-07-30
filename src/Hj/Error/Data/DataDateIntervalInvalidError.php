<?php
/**
 * User: h.jacquir
 * Date: 24/02/2020
 * Time: 13:29
 */

namespace Hj\Error\Data;

/**
 * Class DataDateIntervalInvalidError
 * @package Hj\Error\Data
 */
class DataDateIntervalInvalidError extends AbstractDataError
{
    /**
     * @var string
     */
    private $currentDateAsString;

    /**
     * @var string
     */
    private $previousDateAsString;

    /**
     * @return string
     */
    public function getCurrentDateAsString(): string
    {
        return $this->currentDateAsString;
    }

    /**
     * @param string $currentDateAsString
     */
    public function setCurrentDateAsString(string $currentDateAsString): void
    {
        $this->currentDateAsString = $currentDateAsString;
    }

    /**
     * @return string
     */
    public function getPreviousDateAsString(): string
    {
        return $this->previousDateAsString;
    }

    /**
     * @param string $previousDateAsString
     */
    public function setPreviousDateAsString(string $previousDateAsString): void
    {
        $this->previousDateAsString = $previousDateAsString;
    }

    /**
     * @return string
     */
    protected function getContextualMessage()
    {
        $message = "La validation des données a échoué." .
        " Les dates ci-dessous sont invalides, car la période est en dehors de l'intervalle d'acceptation. La date NE DOIT PAS ÊTRE SUPÉRIEURE à la date d'extraction : '{$this->getCurrentDateAsString()}' et elle NE DOIT PAS ÊTRE INFÉRIEURE à : '{$this->getPreviousDateAsString()}'.".
        " \n";

        foreach ($this->getCellAdapterWithErrors() as $cellAdapterWithError) {
            $message .= "En-tête : {$cellAdapterWithError->getAssociatedHeader()} (Colonne : {$cellAdapterWithError->getColumnName()})";
            $message .= " - Ligne : {$cellAdapterWithError->getRowIndex()} Valeur actuelle : {$cellAdapterWithError->getCell()->getValue() }\n";
        }

        return $message;
    }
}