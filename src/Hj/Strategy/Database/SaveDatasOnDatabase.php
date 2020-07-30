<?php
/**
 * User: h.jacquir
 * Date: 29/01/2020
 * Time: 16:14
 */

namespace Hj\Strategy\Database;

use DateTime;
use Hj\Cloner\PersonCloner;
use Hj\Directory\BaseDirectory;
use Hj\Error\Database\DoctrinePersistenceError;
use Hj\File\Field\BirthDate;
use Hj\File\Field\FirstName;
use Hj\File\Field\LastName;
use Hj\File\RowAdapter;
use Hj\Helper\CatchedErrorHandler;
use Hj\Model\Person;
use Hj\Strategy\Data\CollectRowAdapterStrategy;
use Hj\Strategy\Strategy;

/**
 * Class SaveDatasOnDatabase
 *
 * @package Hj\Strategy\Database
 */
class SaveDatasOnDatabase implements Strategy
{
    /**
     * @var DoctrinePersistenceError
     */
    private $associatedError;

    /**
     * @var BaseDirectory
     */
    private $inProcessingDir;

    /**
     * @var InitializeEntityManagerStrategy
     */
    private $initializeEntityManagerStrategy;

    /**
     * @var CatchedErrorHandler
     */
    private $catchedErrorHandler;

    /**
     * @var PersonCloner
     */
    private $personCloner;

    /**
     * @var CollectRowAdapterStrategy
     */
    private $collectRowAdapterStrategy;

    /**
     * @var FirstName
     */
    private $firstName;

    /**
     * @var LastName
     */
    private $lastName;

    /**
     * @var BirthDate
     */
    private $birthDate;

    /**
     * @var array
     */
    private $savedDatas = [];

    /**
     * SaveAntibiogrammeWithResultat constructor.
     *
     * @param PersonCloner $personCloner
     * @param FirstName $firstName
     * @param LastName $lastName
     * @param BirthDate $birthDate
     * @param CollectRowAdapterStrategy $collectRowAdapterStrategy
     * @param CatchedErrorHandler $catchedErrorHandler
     * @param DoctrinePersistenceError $associatedError
     * @param InitializeEntityManagerStrategy $initializeEntityManagerStrategy
     * @param BaseDirectory $inProcessingDir
     */
    public function __construct(
        PersonCloner $personCloner,
        FirstName $firstName,
        LastName $lastName,
        BirthDate $birthDate,
        CollectRowAdapterStrategy $collectRowAdapterStrategy,
        CatchedErrorHandler $catchedErrorHandler,
        DoctrinePersistenceError $associatedError,
        InitializeEntityManagerStrategy $initializeEntityManagerStrategy,
        BaseDirectory $inProcessingDir
    )
    {
        $this->personCloner = $personCloner;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->birthDate = $birthDate;
        $this->collectRowAdapterStrategy = $collectRowAdapterStrategy;
        $this->catchedErrorHandler = $catchedErrorHandler;
        $this->associatedError = $associatedError;
        $this->initializeEntityManagerStrategy = $initializeEntityManagerStrategy;
        $this->inProcessingDir = $inProcessingDir;
    }

    /**
     * @return bool
     */
    public function isAppropriate()
    {
        return $this->inProcessingDir->hasFiles()
            && false === $this->catchedErrorHandler->getErrorCollector()->hasError()
            && $this->initializeEntityManagerStrategy->isInitialized();
    }

    /**
     * @throws \Hj\Exception\AttributeNotSetException
     * @throws \ReflectionException
     */
    public function apply()
    {
        $persistenceErrorThrown = false;

        $entityManager = $this->initializeEntityManagerStrategy->getDoctrineOrmEntityManager();

        $collector = $this->collectRowAdapterStrategy->getRowCollector();

        while ($collector->valid() && $persistenceErrorThrown === false) {
            /** @var RowAdapter $currentRow */
            $currentRow = $collector->current();

            $firstName = $currentRow->getCellNormalizedValueByField($this->firstName);
            $lastName = $currentRow->getCellNormalizedValueByField($this->lastName);
            $birthDate = $currentRow->getCellNormalizedValueByField($this->birthDate);

            /** @var Person $person */
            $person = $this->personCloner->replicate();
            $person->setBirthDate(DateTime::createFromFormat(BirthDate::DATE_DATABASE_FORMAT, $birthDate));
            $person->setFirstName($firstName);
            $person->setLastName($lastName);

            // @todo encapsulate this
                try {
                    $entityManager->persist($person);
                    $entityManager->flush($person);
                    array_push($this->savedDatas, $person);
                } catch (\Exception $e) {
                    $persistenceErrorThrown = true;
                    $this->catchedErrorHandler->handleErrorWhenPersistenceErrorOccurred($e, $this->associatedError);
                }

            $collector->next();
        }
    }

    /**
     * @return bool
     */
    public function hasSavedDatas()
    {
        return count($this->savedDatas) > 0;
    }
}