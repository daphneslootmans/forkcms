<?php

namespace ForkCMS\Core\Domain\Header\Asset;

use DateTimeImmutable;
use ForkCMS\Core\Domain\Application\Application;
use ForkCMS\Modules\Extensions\Domain\Module\ModuleName;
use InvalidArgumentException;

final class Asset
{
    public readonly DateTimeImmutable $createdOn;
    public readonly string $filePath;

    public function __construct(
        public readonly string $file,
        public readonly bool $addTimestamp = true,
        public readonly Priority $priority = Priority::STANDARD
    ) {
        $this->createdOn = new DateTimeImmutable();
        $this->filePath = $this->getFilePath();
    }

    public function compare(Asset $asset): int
    {
        $comparison = $this->priority->compare($asset->priority);

        if ($comparison !== 0) {
            return $comparison;
        }

        return $this->createdOn <=> $asset->createdOn;
    }

    public function __toString(): string
    {
        if (!$this->addTimestamp) {
            return $this->file;
        }

        if (!file_exists($this->filePath)) {
            return $this->file;
        }

        // check if we need to use a ? or &
        $separator = str_contains($this->file, '?') ? '&' : '?';

        return $this->file . $separator . 'm=' . filemtime($this->filePath);
    }

    private function getFilePath(): string
    {
        static $root = null;
        static $rootRealPath = null;
        if ($root === null) {
            $root = __DIR__ . '/../../../../../';
            $rootRealPath = realpath($root);
        }

        $path = $root . 'public/' . $this->file;
        if (!file_exists($path)) {
            $path = preg_replace(
                '/assets\/modules\/([A-Z]\w*)\/([A-Z]\w*)\/(.*)/',
                $root . 'src/Modules/$2/assets/$1/public/$3',
                $path
            );
        }
        $realPath = realpath($path);
        if ($realPath === false || !str_starts_with($realPath, $rootRealPath)) {
            throw new InvalidArgumentException('File does not exist or is in a location that is not allowed: ' . $path);
        }

        return $realPath;
    }

    public static function forModule(
        Application $application,
        ModuleName $moduleName,
        string $file,
        bool $addTimestamp = true,
        Priority $priority = Priority::STANDARD
    ): self {
        return new self(
            'assets/modules/' . ucfirst($application->value) . '/' . $moduleName->getName() . '/' . $file,
            $addTimestamp,
            $priority
        );
    }
}