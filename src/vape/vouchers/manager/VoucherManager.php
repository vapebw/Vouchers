<?php

declare(strict_types=1);

namespace vape\vouchers\manager;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\command\SoftEnumManager;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use vape\vouchers\Voucher;
use vape\vouchers\VoucherPlugin;

class VoucherManager {

    private Config $config;
    /** @var Voucher[] */
    private array $vouchers = [];

    public function __construct() {
        $this->config = new Config(VoucherPlugin::getInstance()->getDataFolder() . "vouchers.yml", Config::YAML);
        $this->load();
    }

    private function load(): void {
        foreach ($this->config->getAll() as $id => $data) {
            $this->vouchers[$id] = new Voucher(
                $id,
                $data["name"],
                $data["lore"],
                $data["command"],
                $data["executor"]
            );
        }

        if (class_exists(SoftEnumManager::class)) {
            SoftEnumManager::getInstance()->setValues("voucher_ids", array_keys($this->vouchers));
        }
    }

    public function save(): void {
        $data = [];
        foreach ($this->vouchers as $id => $voucher) {
            $data[$id] = [
                "name" => $voucher->getName(),
                "lore" => $voucher->getLore(),
                "command" => $voucher->getCommand(),
                "executor" => $voucher->getExecutor()
            ];
        }
        $this->config->setAll($data);
        $this->config->save();
    }

    public function createVoucher(string $id, string $name, array $lore, string $command, string $executor): void {
        $this->vouchers[$id] = new Voucher($id, $name, $lore, $command, $executor);
        $this->save();

        if (class_exists(SoftEnumManager::class)) {
            SoftEnumManager::getInstance()->addValue("voucher_ids", $id);
        }
    }

    public function deleteVoucher(string $id): void {
        unset($this->vouchers[$id]);
        $this->save();

        if (class_exists(SoftEnumManager::class)) {
            SoftEnumManager::getInstance()->removeValue("voucher_ids", $id);
        }
    }

    public function getVoucher(string $id): ?Voucher {
        return $this->vouchers[$id] ?? null;
    }

    /** @return Voucher[] */
    public function getVouchers(): array {
        return $this->vouchers;
    }

    public function getVoucherItem(Voucher $voucher, int $amount = 1): Item {
        $item = VanillaItems::PAPER();
        $item->setCount($amount);
        $item->setCustomName(TextFormat::colorize($voucher->getName()));
        
        $lore = [];
        foreach ($voucher->getLore() as $line) {
            $lore[] = TextFormat::colorize($line);
        }
        $item->setLore($lore);
        
        $nbt = $item->getNamedTag();
        $nbt->setString("voucher_id", $voucher->getId());
        $item->setNamedTag($nbt);
        
        return $item;
    }
}
