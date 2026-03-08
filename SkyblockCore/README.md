# SkyblockCore

SkyblockCore is a comprehensive Skyblock plugin for Paper 1.21.x, converted from a monolithic PocketMine-MP plugin. It features islands, gangs, custom enchants, custom items, and more.

## Features

- **Island System**: Create and manage private islands in void worlds.
- **Gang System**: Team up with other players, level up your gang, and compete on leaderboards.
- **Custom Enchants**: Lore-based custom enchants stored via Persistent Data Container (PDC).
- **Custom Tiles**: Advanced block entities like AutoMiners and AutoSellers.
- **Database Support**: HikariCP connection pooling supporting both MySQL and SQLite.
- **Scoreboard**: Dynamic per-player sidebar scoreboard.

## Requirements

- **Java**: 21 or higher.
- **Server**: Paper 1.21.4 or compatible.
- **Build System**: Maven.

## Installation & Setup

1. **Build the Plugin**:
   ```bash
   mvn clean package
   ```
   The compiled jar will be in `target/SkyblockCore-1.0-SNAPSHOT.jar`.

2. **Deploy**:
   Place the jar in your Paper server's `plugins/` folder.

3. **Configure**:
   Start the server once to generate the `plugins/SkyblockCore/config.yml`.
   Configure your database settings:
   ```yaml
   database:
     type: sqlite # or mysql
     host: localhost
     port: 3306
     database: skyblock
     username: root
     password: ''
   ```

4. **Restart**:
   Restart the server to apply configuration changes.

## Commands

- `/is` - Main island command (create, go, etc.)
- `/gang` - Main gang command
- `/ce` - Custom enchants
- `/shop` - Open the GUI shop
- `/bal` - Check your balance

## Architecture

The plugin follows a modular manager-based architecture:
- **Managers**: Handle runtime logic and state (e.g., `IslandManager`).
- **Repositories**: Handle database persistence (e.g., `IslandRepository`).
- **PDC**: Used for storing custom data on items and blocks without NMS.

## Development

To add new custom enchants, extend the `BaseEnchant` class and register it in `EnchantManager`.
To add new custom items, use the `ItemManager`.
