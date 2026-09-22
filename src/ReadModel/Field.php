<?php

declare(strict_types=1);

namespace App\ReadModel;

class Field
{
    public ?string $formType = null;
    public array $formTypeOptions = [];
    public bool $showOnForm = true;
    public bool $showOnDetail = true;
    public bool $showOnIndex = true;

    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly ?string $format = null,
    ) {}

    public function setFormType(?string $formType): static
    {
        $this->formType = $formType;

        return $this;
    }

    public function setFormTypeOptions(array $formTypeOptions): static
    {
        $this->formTypeOptions = $formTypeOptions;

        return $this;
    }

    public function hideOnForm(): static
    {
        $this->showOnForm = false;

        return $this;
    }

    public function hideOnDetail(): static
    {
        $this->showOnDetail = false;

        return $this;
    }

    public function hideOnIndex(): static
    {
        $this->showOnIndex = false;

        return $this;
    }
}
