# SkyblockCore

SkyblockCore is a high-performance, feature-rich Skyblock plugin for Paper/Spigot 1.21.x, rewritten from a legacy PocketMine-MP (PHP) source.

## Conversion Progress: ~22%

This project is a manual conversion of over 500 PHP files to a modern Java environment.

### System Status

| System | Status | Notes |
| :--- | :--- | :--- |
| **Core & DB** | ✅ Complete | HikariCP, MySQL/SQLite support, Auto-migration. |
| **Island Management** | ✅ Complete | Async world gen, home system, deletion, members. |
| **User Data** | ✅ Complete | Async loading/saving, stats tracking (blocks, etc). |
| **Custom Tiles** | ✅ Complete | AutoSeller, AutoMiner, Ore Gens, HopperTile. |
| **Custom Enchants** | ✅ Complete | 50+ enchants ported with Lore & PDC metadata. |
| **Scoreboard** | ✅ Complete | Real-time updates with Adventure API. |
| **Gangs** | ✅ Complete | Basic creation, member management, repository. |
| **Pets** | 🟡 Partial | Manager implemented, individual pet logic in progress. |
| **Shop/Economy** | 🟡 Partial | Basic command & prices; needs full GUI conversion. |
| **Quests** | 🔴 Not started | Logic exists in PHP; needs Java implementation. |
| **NPCs** | 🔴 Not started | Extensive NPC logic in PHP; needs porting. |
| **Generators** | 🔴 Not started | Nether/End generator conversion pending. |

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

### Tile & Ore Gen Blocks

| Block | Tile Type | Description |
| :--- | :--- | :--- |
| **Barrel** | AutoSeller | Automatically sells chest contents. |
| **Slime Block** | AutoMiner | Automatically mines ores in radius. |
| **Purple Glazed Terracotta** | Catalyst | Boosts island ore generation. |
| **Hopper (Level > 1)** | HopperTile | Enhanced hopper functionality. |
| **Cyan Glazed Terracotta** | Diamond Ore Gen | Generates Diamond Ore above it. |
| **Green Glazed Terracotta** | Emerald Ore Gen | Generates Emerald Ore above it. |
| **White Glazed Terracotta** | Iron Ore Gen | Generates Iron Ore above it. |
| **Yellow Glazed Terracotta** | Gold Ore Gen | Generates Gold Ore above it. |
| **Blue Glazed Terracotta** | Lapis Ore Gen | Generates Lapis Ore above it. |

### Known Limitations
- Bedrock Form UIs are being converted to Chest GUIs (In progress).
- Some complex NMS-based features from PHP require standard Bukkit API alternatives.
- Island biome changes require chunk reload to be fully visible.

### Installation
1. Clone the repository.
2. Build with Maven: `mvn clean package`.
3. Drop `SkyblockCore-1.0-SNAPSHOT.jar` into your Paper 1.21.x `plugins` folder.
4. Configure `config.yml` (database settings).
