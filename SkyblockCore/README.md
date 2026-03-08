# SkyblockCore

SkyblockCore is a high-performance, feature-rich Skyblock plugin for Paper/Spigot 1.21.x, rewritten from a legacy PocketMine-MP (PHP) source.

## Conversion Progress: ~25%

This project is a manual conversion of over 500 PHP files to a modern Java environment.

### System Status

| System | Status | Notes |
| :--- | :--- | :--- |
| **Core & DB** | ✅ Complete | HikariCP, MySQL/SQLite support, Auto-migration. |
| **Island Management** | ✅ Complete | Async world gen, home system, deletion, members, border enforcement. |
| **User Data** | ✅ Complete | Async loading/saving, stats tracking (blocks, kills, deaths, killstreak, gang). |
| **Custom Tiles** | ✅ Complete | PDC-Tagged item system. AutoSeller, AutoMiner, Ore Gens, Catalyst, Hopper. |
| **Custom Enchants** | ✅ Complete | Lore & PDC metadata system. Support for armor, swords, bows, and tools. |
| **Scoreboard** | ✅ Complete | Dual-mode display (Spawn vs Island), LuckPerms integration, real-time updates. |
| **Gangs** | 🟡 Partial | Repository and basic commands implemented. |
| **Pets** | 🟡 Partial | Manager and basic pet persistence implemented. |
| **Shop/Economy** | 🟡 Partial | Basic command & prices; needs full GUI conversion. |
| **Warps & Homes** | ✅ Complete | Persistence in database, multiple homes support. |

### Commands

| Command | Usage | Description |
| :--- | :--- | :--- |
| `/is` | `/is <subcommand>` | Main island management |
| `/is create <name>` | Create a new island | |
| `/is go` | Teleport to your island | |
| `/is info` | View island statistics | |
| `/is lock/unlock` | Toggle island access | |
| `/is invite/kick <player>` | Manage island members | |
| `/is members` | List island members | |
| `/is sethome` | Set island home location | |
| `/is delete` | Delete your island and world | |
| `/is setbiome <biome>` | Change island biome | |
| `/spawn` | `/spawn` | Teleport to spawn world |
| `/bal` | `/bal [player]` | Check balance |
| `/baltop` | `/baltop` | Top balances |
| `/pay` | `/pay <player> <amount>` | Send money |
| `/sell` | `/sell [hand/all]` | Sell items |
| `/gang` | `/gang <subcommand>` | Gang management |
| `/tilegive` | `/tilegive <type> [lvl] [ore]` | Give custom tile items (Admin only) |

### Tile & Ore Gen (PDC-Tagged Items)

Tiles must be created via `TileItemFactory` (or `/tilegive`) to function. Vanilla blocks will not activate tiles.

| Block Material | Tile Type | Description |
| :--- | :--- | :--- |
| **Barrel** | AutoSeller | Automatically sells chest contents. |
| **Slime Block** | AutoMiner | Automatically mines ores in radius. |
| **Purple Glazed Terracotta** | Catalyst | Boosts island ore generation. |
| **Hopper (Level > 1)** | HopperTile | Enhanced hopper functionality. |
| **Cyan Glazed Terracotta** | Diamond OreGen | Generates Diamond Ore above it. |
| **Green Glazed Terracotta** | Emerald OreGen | Generates Emerald Ore above it. |
| **White Glazed Terracotta** | Iron OreGen | Generates Iron Ore above it. |
| **Yellow Glazed Terracotta** | Gold OreGen | Generates Gold Ore above it. |
| **Blue Glazed Terracotta** | Lapis OreGen | Generates Lapis Ore above it. |
| **Black Glazed Terracotta** | Coal OreGen | Generates Coal Ore above it. |
| **Red Glazed Terracotta** | Quartz OreGen | Generates Nether Quartz Ore above it. |
| **Gray Glazed Terracotta** | Ancient Debris OreGen | Generates Ancient Debris above it. |

### Getting Tile Items
Use the admin command: `/tilegive <oregen|autoseller|autominer|catalyst> [level] [ore_type]`
Example: `/tilegive oregen 1 diamond`

### Known Limitations
- Bedrock Form UIs are being converted to Chest GUIs (In progress).
- Some complex NMS-based features from PHP require standard Bukkit API alternatives.
- Island biome changes require chunk reload to be fully visible.

### Installation
1. Clone the repository.
2. Build with Maven: `mvn clean package`.
3. Drop `SkyblockCore-1.0-SNAPSHOT.jar` into your Paper 1.21.x `plugins` folder.
4. Ensure **LuckPerms** is installed for rank display on the scoreboard.
