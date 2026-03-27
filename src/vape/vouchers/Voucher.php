<?php

declare(strict_types=1);

namespace vape\vouchers;

class Voucher {
    
    public function __construct(
        private string $id,
        private string $name,
        private array $lore,
        private string $command,
        private string $executor
    ) {}

    public function getId(): string {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getLore(): array {
        return $this->lore;
    }

    public function getCommand(): string {
        return $this->command;
    }

    public function getExecutor(): string {
        return $this->executor;
    }
}
