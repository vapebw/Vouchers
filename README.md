# Vouchers

[![](https://poggit.pmmp.io/shield.api/AdvancedVouchers)](https://poggit.pmmp.io/p/AdvancedVouchers)


A high-performance and feature-rich voucher system for **PocketMine-MP 5**, designed specifically for competitive servers. This plugin allows administrators to create custom items that execute commands when used by players.

## Features

*   **Dual Execution Modes**: Vouchers can execute commands as the **Player** or as the **Console**.
*   **Customizable Items**: Define custom names, lore, and items for each voucher.
*   **Persistent Storage**: All vouchers are saved and loaded dynamically.
*   **Autocomplete Support**: Fully integrated with my fork of pocketmine-mp for Bedrock autocomplete for a seamless administrative experience. (if the version of pmmp supports it)
*   **Smart Forms**: Easy-to-use UI for creating and editing vouchers in-game.
*   **Optimized Performance**: Lightweight logic to ensure zero impact on server TPS.

## Commands

The main command is `/voucher` (alias: `/vouchers`).

| Subcommand | Description | Permission |
|------------|-------------|------------|
| `/voucher create` | Opens the UI to create a new voucher. | `vouchers.admin` |
| `/voucher edit` | Opens the UI to edit an existing voucher. | `vouchers.admin` |
| `/voucher delete <id>` | Deletes a voucher by its ID. | `vouchers.admin` |
| `/voucher list` | Lists all currently active vouchers. | `vouchers.admin` |
| `/voucher give <player> <id> [amount]` | Gives a specific voucher to a player. | `vouchers.admin` |

## Placeholders

You can use the following placeholders in voucher commands:
- `{player}`: Returns the name of the player who used the voucher.

## Installation

1. Download the latest version of the plugin.
2. Place the `Vouchers.phar` (or folder) in your server's `plugins/` directory.
3. Restart the server.

## License

This project is licensed under the **GPL-3.0 License**.
