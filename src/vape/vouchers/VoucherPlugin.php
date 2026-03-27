<?php

declare(strict_types=1);

namespace vape\vouchers;

use pocketmine\network\mcpe\command\BedrockCommandRegistry;
use pocketmine\network\mcpe\command\BedrockOverload;
use pocketmine\network\mcpe\command\BedrockParameter;
use pocketmine\network\mcpe\command\SoftEnumManager;
use pocketmine\network\mcpe\protocol\types\command\CommandParameterTypes;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use vape\vouchers\command\VoucherCommand;
use vape\vouchers\listener\VoucherListener;
use vape\vouchers\manager\VoucherManager;

class VoucherPlugin extends PluginBase {

    use SingletonTrait;

    private VoucherManager $voucherManager;

    protected function onEnable(): void {
        self::setInstance($this);
        
        $this->voucherManager = new VoucherManager();
        $this->getServer()->getCommandMap()->register("vouchers", new VoucherCommand());
        $this->getServer()->getPluginManager()->registerEvents(new VoucherListener(), $this);

        $this->registerAutocomplete();
    }

    private function registerAutocomplete(): void {
        if (!class_exists(SoftEnumManager::class) || !class_exists(BedrockCommandRegistry::class)) {
            return;
        }

        SoftEnumManager::getInstance()->registerEnum("voucher_ids", array_keys($this->voucherManager->getVouchers()));

        $overloads = [
            new BedrockOverload([
                new BedrockParameter("action", CommandParameterTypes::ID, false, ["create", "edit", "list"])
            ]),
            new BedrockOverload([
                new BedrockParameter("action", CommandParameterTypes::ID, false, ["delete"]),
                new BedrockParameter("id", CommandParameterTypes::ID, false, null, "voucher_ids")
            ]),
            new BedrockOverload([
                new BedrockParameter("action", CommandParameterTypes::ID, false, ["give"]),
                new BedrockParameter("player", CommandParameterTypes::SELECTION, false),
                new BedrockParameter("id", CommandParameterTypes::ID, false, null, "voucher_ids"),
                new BedrockParameter("amount", CommandParameterTypes::INT, true)
            ])
        ];
        BedrockCommandRegistry::getInstance()->register("voucher", $overloads);
    }

    public function getVoucherManager(): VoucherManager {
        return $this->voucherManager;
    }
}
