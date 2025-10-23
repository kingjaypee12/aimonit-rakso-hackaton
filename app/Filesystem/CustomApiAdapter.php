<?php

namespace App\Filesystem;

use App\Actions\GenerateFileUrl;
use App\Actions\UploadFile;
use Illuminate\Support\Facades\File as LaravelFile;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathPrefixer;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;

class CustomApiAdapter implements FilesystemAdapter
{
    protected $uploadAction;

    protected $urlAction;

    protected $prefixer;

    public function __construct(array $config)
    {
        $this->uploadAction = new UploadFile;
        $this->urlAction = new GenerateFileUrl;
        $this->prefixer = new PathPrefixer($config['root'] ?? '');
    }

    public function fileExists(string $path): bool
    {
        // Implement if API supports, else return false or throw
        return false;
    }

    public function directoryExists(string $path): bool
    {
        return false;
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $tempPath = sys_get_temp_dir().'/'.basename($path);
        LaravelFile::put($tempPath, $contents);
        $file = new \Illuminate\Http\File($tempPath);
        $this->uploadAction->execute($file);
        unlink($tempPath);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->write($path, stream_get_contents($contents), $config);
    }

    public function read(string $path): string
    {
        // Implement API call to read file contents
        throw UnableToReadFile::fromLocation($path, 'API call not implemented.');
    }

    public function readStream(string $path): resource
    {
        // Implement
        throw UnableToReadFile::fromLocation($path, 'API call not implemented.');
    }

    public function delete(string $path): void
    {
        // Implement API delete
        throw UnableToDeleteFile::fromLocation($path, 'API call not implemented');
    }

    public function deleteDirectory(string $path): void
    {
        throw UnableToDeleteDirectory::fromLocation($path, 'API call not implemented');
    }

    public function createDirectory(string $path, Config $config): void
    {
        // If needed
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // Implement if applicable
    }

    public function visibility(string $path): FileAttributes
    {
        // Implement
        return new FileAttributes($path);
    }

    public function mimeType(string $path): FileAttributes
    {
        return new FileAttributes($path);
    }

    public function lastModified(string $path): FileAttributes
    {
        return new FileAttributes($path);
    }

    public function fileSize(string $path): FileAttributes
    {
        return new FileAttributes($path);
    }

    public function listContents(string $directory = '', bool $recursive = false): iterable
    {
        return [];
    }

    public function move(string $source, string $destination, Config $config): void
    {
        throw UnableToMoveFile::fromLocationTo($source, $destination);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        throw UnableToCopyFile::fromLocationTo($source, $destination);
    }

    public function getUrl(string $path): string
    {
        dump(GenerateFileUrl::execute($this->prefixer->prefixPath($path)));

        return GenerateFileUrl::execute($this->prefixer->prefixPath($path));
    }
}
