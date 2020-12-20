<?php declare(strict_types=1);

namespace Surda\Maker\Maker;

use Nette\Utils\Strings;
use Surda\Maker\ConsoleStyle;
use Surda\Maker\FileManager;
use Surda\Maker\Generator;
use Surda\Maker\Util\ClassNameDetails;

class EntityMaker extends AbstractMaker
{
    /** @var Generator */
    private $generator;

    /** @var FileManager */
    private $fileManager;

    /** @var ConsoleStyle */
    private $io;

    /**
     * @param Generator   $generator
     * @param FileManager $fileManager
     */
    public function __construct(Generator $generator, FileManager $fileManager)
    {
        $this->generator = $generator;
        $this->fileManager = $fileManager;
    }

    /**
     * @param string       $name
     * @param ConsoleStyle $io
     */
    public function make(string $name, ConsoleStyle $io): void
    {
        $this->io = $io;

        $entityClassDetails = $this->generator->createClassNameDetails($name, 'Model');
        $entityFactoryClassDetails = $this->generator->createClassNameDetails($name, 'Model', 'Factory');
        $exceptionClassDetails = $this->generator->createClassNameDetails($name, 'Model', 'NotFoundException');
        $repoClassDetails = $this->generator->createClassNameDetails($name, 'Model', 'Repository');
        $entityQueryClassDetails = $this->generator->createClassNameDetails($name, 'Model', 'Query');
        $entityDataClassDetails = $this->generator->createClassNameDetails($name, 'Model', 'Data');

        $this->generateEntityClass($entityClassDetails, $repoClassDetails);
        $this->generateEntityFactoryClass($entityFactoryClassDetails, $entityClassDetails);
        $this->generateExceptionClass($exceptionClassDetails);
        $this->generateRepositoryClass($repoClassDetails, $entityClassDetails);
        $this->generateEntityQueryClass($entityQueryClassDetails, $entityClassDetails);
        $this->generateEntityDataClass($entityDataClassDetails, $entityClassDetails);
    }

    protected function generateEntityClass(ClassNameDetails $entityClassDetails, ClassNameDetails $repoClassDetails): string
    {
        $fullClassName = $entityClassDetails->getFullName();

        $content = $this->generator->generateClassContent(
            $fullClassName,
            'Doctrine/Entity.latte.template',
            [
                'repository_full_class_name' => $repoClassDetails->getFullName(),
                'repository_class_name' => $repoClassDetails->getShortName(),
                'should_escape_table_name' => TRUE,
                'table_name' => Strings::lower($entityClassDetails->getShortName()),
            ]
        );

        $entityPath = $this->fileManager->writeContent($fullClassName, $content);

        $this->writeCommentMessage($this->io, $this->fileManager->relativizePath($this->fileManager->getRelativePathForFutureClass($fullClassName)));

        return $entityPath;
    }

    protected function generateRepositoryClass(ClassNameDetails $repoClassDetails, ClassNameDetails $entityClassDetails): string
    {
        $fullClassName = $repoClassDetails->getFullName();

        $content = $this->generator->generateClassContent(
            $fullClassName,
            'Doctrine/Repository.latte.template',
            [
//                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
                'doctrine_repository_full_class_name' => 'Doctrine\ORM\EntityRepository',
                'doctrine_repository_class_name' => 'EntityRepository',
            ]
        );

        $entityPath = $this->fileManager->writeContent($fullClassName, $content);

        $this->writeCommentMessage($this->io, $this->fileManager->relativizePath($this->fileManager->getRelativePathForFutureClass($fullClassName)));

        return $entityPath;
    }

    protected function generateEntityQueryClass(ClassNameDetails $entityQueryClassDetails, ClassNameDetails $entityClassDetails): string
    {
        $fullClassName = $entityQueryClassDetails->getFullName();

        $content = $this->generator->generateClassContent(
            $fullClassName,
            'Doctrine/EntityQuery.latte.template',
            [
//                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
            ]
        );

        $entityPath = $this->fileManager->writeContent($fullClassName, $content);

        $this->writeCommentMessage($this->io, $this->fileManager->relativizePath($this->fileManager->getRelativePathForFutureClass($fullClassName)));

        return $entityPath;
    }

        protected function generateEntityDataClass(ClassNameDetails $entityDataClassDetails, ClassNameDetails $entityClassDetails): string
    {
        $fullClassName = $entityDataClassDetails->getFullName();

        $content = $this->generator->generateClassContent(
            $fullClassName,
            'Doctrine/EntityData.latte.template',
            [
                'entity_class_name' => $entityClassDetails->getShortName(),
            ]
        );

        $entityPath = $this->fileManager->writeContent($fullClassName, $content);

        $this->writeCommentMessage($this->io, $this->fileManager->relativizePath($this->fileManager->getRelativePathForFutureClass($fullClassName)));

        return $entityPath;
    }

    protected function generateEntityFactoryClass(ClassNameDetails $entityFactoryClassDetails, ClassNameDetails $entityClassDetails): string
    {
        $fullClassName = $entityFactoryClassDetails->getFullName();

        $content = $this->generator->generateClassContent(
            $fullClassName,
            'Doctrine/EntityFactory.latte.template',
            [
                'entity_class_name' => $entityClassDetails->getShortName(),
            ]
        );

        $entityPath = $this->fileManager->writeContent($fullClassName, $content);

        $this->writeCommentMessage($this->io, $this->fileManager->relativizePath($this->fileManager->getRelativePathForFutureClass($fullClassName)));

        return $entityPath;
    }

    protected function generateExceptionClass(ClassNameDetails $exceptionClassDetails): string
    {
        $fullClassName = $exceptionClassDetails->getFullName();

        $content = $this->generator->generateClassContent(
            $fullClassName,
            'Doctrine/EntityNotFoundException.latte.template',
            []
        );

        $entityPath = $this->fileManager->writeContent($fullClassName, $content);


        $b = $this->fileManager->getRelativePathForFutureClass($fullClassName);
        $c = $this->fileManager->relativizePath($b);

        $this->writeCommentMessage($this->io, $c);

//        $this->writeCommentMessage($this->io, $this->fileManager->relativizePath($this->fileManager->getRelativePathForFutureClass($fullClassName)));

        return $entityPath;
    }
}