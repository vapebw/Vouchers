<?php

declare(strict_types=1);

namespace vape\vouchers\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use vape\vouchers\form\VoucherForm;
use vape\vouchers\VoucherPlugin;

class VoucherCommand extends Command {

    public function __construct() {
        parent::__construct("voucher", "Voucher management", "/voucher <create|edit|delete|give|list>");
        $this->setPermission("vouchers.admin");
    }

    public function execute(CommandSender $sender, string $label, array $args): void {
        if (!$this->testPermission($sender)) return;
        
        if (empty($args)) {
            if ($sender instanceof Player) {
                VoucherForm::openMain($sender);
            } else {
                $this->sendHelp($sender);
            }
            return;
        }

        $sub = strtolower($args[0]);
        switch ($sub) {
            case "create":
            case "edit":
                if ($sender instanceof Player) {
                    VoucherForm::openMain($sender);
                } else {
                    $sender->sendMessage("§cOnly players can use Form UI.");
                }
                break;
                
            case "delete":
                if (!isset($args[1])) {
                    $sender->sendMessage("§eUsage: §f/voucher delete <id>");
                    return;
                }
                VoucherPlugin::getInstance()->getVoucherManager()->deleteVoucher($args[1]);
                $sender->sendMessage("§cVoucher deleted if it existed.");
                break;

            case "give":
                if (!isset($args[2])) {
                    $sender->sendMessage("§eUsage: §f/voucher give <player> <id> [amount]");
                    return;
                }
                
                $player = VoucherPlugin::getInstance()->getServer()->getPlayerByPrefix($args[1]);
                if ($player === null) {
                    $sender->sendMessage("§cPlayer not found.");
                    return;
                }
                
                $voucher = VoucherPlugin::getInstance()->getVoucherManager()->getVoucher($args[2]);
                if ($voucher === null) {
                    $sender->sendMessage("§cVoucher not found.");
                    return;
                }
                
                $amount = isset($args[3]) ? (int)$args[3] : 1;
                $item = VoucherPlugin::getInstance()->getVoucherManager()->getVoucherItem($voucher, $amount);
                $player->getInventory()->addItem($item);
                $sender->sendMessage("§aGave §f" . $amount . "x " . $voucher->getId() . " §ato §f" . $player->getName());
                break;

            case "list":
                $vouchers = VoucherPlugin::getInstance()->getVoucherManager()->getVouchers();
                if (empty($vouchers)) {
                    $sender->sendMessage("§cNo vouchers available.");
                    return;
                }
                $sender->sendMessage("§6§lVouchers List:");
                foreach ($vouchers as $v) {
                    $sender->sendMessage("§e- §f" . $v->getId() . " §7(" . $v->getName() . ")");
                }
                break;

            default:
                $this->sendHelp($sender);
                break;
        }
    }

    private function sendHelp(CommandSender $sender): void {
        $sender->sendMessage("§6§lVoucher Commands");
        $sender->sendMessage("§e/voucher create §7- Create via Form");
        $sender->sendMessage("§e/voucher edit §7- Edit via Form");
        $sender->sendMessage("§e/voucher delete <id> §7- Delete voucher");
        $sender->sendMessage("§e/voucher give <player> <id> [amount] §7- Give voucher");
        $sender->sendMessage("§e/voucher list §7- List all vouchers");
    }
}
