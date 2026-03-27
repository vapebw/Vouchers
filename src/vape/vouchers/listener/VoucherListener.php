<?php

declare(strict_types=1);

namespace vape\vouchers\listener;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use vape\vouchers\VoucherPlugin;

class VoucherListener implements Listener {

    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        
        if ($item->isNull()) return;
        
        $nbt = $item->getNamedTag();
        $voucherId = $nbt->getString("voucher_id", "");
        
        if ($voucherId === "") return;
        
        $event->cancel();
        
        $voucher = VoucherPlugin::getInstance()->getVoucherManager()->getVoucher($voucherId);
        if ($voucher === null) {
            $player->sendMessage("§cThis voucher is no longer valid.");
            return;
        }

        $command = trim(ltrim(str_replace("{player}", $player->getName(), $voucher->getCommand()), "/"));
        
        if ($voucher->getExecutor() === "CONSOLE") {
            VoucherPlugin::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(VoucherPlugin::getInstance()->getServer(), VoucherPlugin::getInstance()->getServer()->getLanguage()), $command);
        } else {
            VoucherPlugin::getInstance()->getServer()->dispatchCommand($player, $command);
        }
        
        $item->setCount($item->getCount() - 1);
        $player->getInventory()->setItemInHand($item);
        
        $player->sendMessage(\pocketmine\utils\TextFormat::colorize("§aYou have successfully redeemed the §f" . $voucher->getName() . "§a!"));
    }
}
