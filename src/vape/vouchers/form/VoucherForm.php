<?php

declare(strict_types=1);

namespace vape\vouchers\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use vape\vouchers\VoucherPlugin;

class VoucherForm {

    public static function openMain(Player $player): void {
        $vouchers = VoucherPlugin::getInstance()->getVoucherManager()->getVouchers();
        $buttons = [];
        
        foreach ($vouchers as $voucher) {
            $buttons[] = ["text" => "§d" . $voucher->getId() . "\n§7" . $voucher->getName()];
        }
        
        $buttons[] = ["text" => "§a+ Create Voucher"];

        $form = new class($buttons, array_keys($vouchers)) implements Form {
            public function __construct(private array $buttons, private array $ids) {}

            public function jsonSerialize(): array {
                return [
                    "type" => "form",
                    "title" => "§6§lVoucher Manager",
                    "content" => "§7Total Vouchers: §f" . count($this->ids),
                    "buttons" => $this->buttons
                ];
            }

            public function handleResponse(Player $player, $data): void {
                if ($data === null) return;
                
                if ($data === count($this->ids)) {
                    VoucherForm::openCreate($player);
                } else {
                    VoucherForm::openEditMenu($player, $this->ids[$data]);
                }
            }
        };
        $player->sendForm($form);
    }

    public static function openCreate(Player $player): void {
        $form = new class implements Form {
            public function jsonSerialize(): array {
                return [
                    "type" => "custom_form",
                    "title" => "§6Create Voucher",
                    "content" => [
                        ["type" => "input", "text" => "Unique ID", "placeholder" => "e.g. money_1k"],
                        ["type" => "input", "text" => "Display Name", "placeholder" => "§a$1,000 Voucher"],
                        ["type" => "input", "text" => "Lore (comma separated)", "placeholder" => "§7Right-click to redeem"],
                        ["type" => "input", "text" => "Command ({player} placeholder)", "placeholder" => "money add {player} 1000"],
                        ["type" => "dropdown", "text" => "Executor", "options" => ["CONSOLE", "PLAYER"]]
                    ]
                ];
            }

            public function handleResponse(Player $player, $data): void {
                if ($data === null) return;
                
                $id = trim($data[0]);
                if ($id === "") return;
                
                $lore = array_map("trim", explode(",", $data[2]));
                $executor = $data[4] === 0 ? "CONSOLE" : "PLAYER";
                
                VoucherPlugin::getInstance()->getVoucherManager()->createVoucher($id, $data[1], $lore, $data[3], $executor);
                $player->sendMessage("§aVoucher §f" . $id . " §acreated successfully!");
            }
        };
        $player->sendForm($form);
    }

    public static function openEditMenu(Player $player, string $id): void {
        $voucher = VoucherPlugin::getInstance()->getVoucherManager()->getVoucher($id);
        if ($voucher === null) return;

        $form = new class($id, $voucher) implements Form {
            public function __construct(private string $id, private $voucher) {}

            public function jsonSerialize(): array {
                return [
                    "type" => "form",
                    "title" => "§6Editing: §f" . $this->id,
                    "content" => "§7What would you like to do?",
                    "buttons" => [
                        ["text" => "§eGive to Self"],
                        ["text" => "§cDelete Voucher"]
                    ]
                ];
            }

            public function handleResponse(Player $player, $data): void {
                if ($data === null) return;
                
                if ($data === 0) {
                    $item = VoucherPlugin::getInstance()->getVoucherManager()->getVoucherItem($this->voucher);
                    $player->getInventory()->addItem($item);
                    $player->sendMessage("§aReceived voucher §f" . $this->id);
                } else {
                    VoucherPlugin::getInstance()->getVoucherManager()->deleteVoucher($this->id);
                    $player->sendMessage("§cVoucher §f" . $this->id . " §cdeleted.");
                }
            }
        };
        $player->sendForm($form);
    }
}
