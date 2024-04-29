<?php

namespace Obix\Validator\Rule;

use Psr\Http\Message\UploadedFileInterface;

final class FileUpload extends RuleBase
{
    private string $message = 'value should be instance of {{ class }}.';
    private string $maxSizeMessage = 'file is too large ({{ size }} MB; allowed maximum size is {{ limit }} MB';
    private string $mimeTypesMessage = 'the mime type \'{{ type }}\' of the file is not allowed; allowed mime types are: {{ types }}.';
    private ?int $maxSize = null;
    public array $mimeTypes = [];

    public function test($value, string $name, array $values): bool
    {
        if ($value === null) {
            return false;
        }

        if (!$value instanceof UploadedFileInterface) {
            $this->setError(
                $this->message, [
                    'value' => $value,
                    'class' => 'Psr\Http\Message\UploadedFileInterface',
                    'type' => gettype($value)
                ]
            );
            return false;
        }

        if (is_int($this->maxSize) && $value->getSize() > $this->maxSize) {
            $this->setError(
                $this->maxSizeMessage, [
                    'value' => $value,
                    'size' => $value->getSize(),
                    'limit' => $this->maxSize,
                ]
            );
            return false;
        }

        if ($this->mimeTypes !== [] && !in_array($value->getClientMediaType(), $this->mimeTypes)) {
            $this->setError(
                $this->mimeTypesMessage, [
                    'value' => $value,
                    'type' => $value->getClientMediaType(),
                    'types' => implode(', ', $this->mimeTypes),
                ]
            );
            return false;
        }
        return true;
    }

    public function setMaxSize(int $maxSize): self
    {
        $this->maxSize = $maxSize;

        return $this;
    }

    /**
     * @param array<string> $mimeTypes
     * @return $this
     */
    public function setMimeTypes(array $mimeTypes): self
    {
        $this->mimeTypes = $mimeTypes;

        return $this;
    }


    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function setMaxSizeMessage(string $maxSizeMessage): self
    {
        $this->maxSizeMessage = $maxSizeMessage;

        return $this;
    }

    public function setMimeTypesMessage(string $mimeTypesMessage): self
    {
        $this->mimeTypesMessage = $mimeTypesMessage;

        return $this;
    }
}
