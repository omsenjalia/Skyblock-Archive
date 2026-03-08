# SkyblockCore

A Paper 1.21 Java port of the FallenTech PocketMine-MP Skyblock core plugin.
Original source: [FallentechPE/Skyblock-Archive](https://github.com/FallentechPE/Skyblock-Archive)

---

## Conversion Progress

> Overall: **~42% converted** (4 of ~10 major systems fully implemented)

| System | PHP Files | Status | Notes |
|---|---|---|---|
| Plugin scaffold / Main | 1 | ✅ Complete | Manager init, lifecycle, MiniMessage broadcast |
| Database layer | 3 | ✅ Complete | HikariCP, MySQL + SQLite, all tables |
| User system | 2 | ✅ Complete | UUID fixed, async load/save, scoreboard hook |
| Island system | 3 | ✅ ~80% | Create, go, load on startup — full field persistence TODO |
| Gang system | 2 | ✅ Complete | CRUD, leaderboard, all commands |
| Custom enchants | 107 | ✅ Complete | All 80 enchants registered, PDC-based, event-wired |
| Custom tiles | 6 | ✅ Complete | AutoSeller, AutoMiner, Catalyst, Hopper — persisted |
| Commands | 261 | 🟡 ~15% | is, gang, pay, bal, baltop, sell, home, warp stubs |
| Custom items | 21 | 🔴 Stubbed | Tool sets only, no custom item logic |
| Pets | 61 | 🔴 Stubbed | ArmorStand spawn only, no follow or effects |
| UI / Shop | 4 | 🔴 Not started | Bedrock forms → inventory GUIs needed |
| Spawner system | 27 | 🔴 Not started | — |
| Generators | 8 | ✅ Complete | Void world generator for islands |
| Scoreboard | ~2 | 🟡 Partial | Hardcoded values, needs live data |
| Tasks / Scheduler | 14 | 🟡 Partial | Tile tasks done, broadcast/envoy TODO |
| Events | 4 | 🟡 Partial | Join/quit/break/place done |
| DB / Perms | 2 | ✅ Complete | Via LuckPerms recommendation |
| Chat | 2 | 🔴 Not started | Gang chat, rank tags |
| Particles | ~1 | 🔴 Not started | — |
| Block overrides | 6 | 🔴 Not started | — |

**Note:** The original plugin was Bedrock (PocketMine-MP). Several features are Bedrock-only and have been replaced with Java equivalents:
- Bedrock Forms UI → Chest inventory GUIs (TODO)
- Bedrock custom entity textures (Customies) → ArmorStand pets (stubbed)
- PMMP entity system → Paper entity API

---

## Requirements

- Java 21+
- Paper 1.21.4+
- Maven 3.8+
- MySQL 8+ or SQLite (auto-created)

---

## Building

```bash
cd SkyblockCore
mvn clean package
```

Output jar: `SkyblockCore/target/SkyblockCore-1.0-SNAPSHOT-shaded.jar`

---

## Installation

1. Drop the shaded jar into your Paper server's `plugins/` folder
2. Start the server once to generate `plugins/SkyblockCore/config.yml`
3. Edit `config.yml` for your database:

```yaml
database:
  type: sqlite       # or mysql
  file: skyblock.db  # sqlite only
  host: localhost    # mysql only
  port: 3306
  database: skyblock
  username: root
  password: ''
```

4. Restart the server

---

## Commands

| Command | Description | Status |
|---|---|---|
| `/is create <name>` | Create a new island | ✅ |
| `/is go` | Teleport to your island | ✅ |
| `/gang create <name>` | Create a gang | ✅ |
| `/gang invite <player>` | Invite a player | ✅ |
| `/gang leave` | Leave your gang | ✅ |
| `/gang disband` | Disband your gang | ✅ |
| `/gang info` | View gang info | ✅ |
| `/gang top` | Gang leaderboard | ✅ |
| `/pay <player> <amount>` | Send money | ✅ |
| `/bal [player]` | Check balance | ✅ |
| `/baltop` | Top balances | ✅ |
| `/sell` | Sell held item | ✅ |
| `/home [name]` | Teleport home | ✅ |
| `/sethome [name]` | Set home | ✅ |
| `/shop` | Open shop | 🔴 Stub |
| `/ce` | Custom enchant info | 🔴 Stub |
| `/warp` | Warp to location | 🔴 Stub |

---

## Custom Tiles

Place these blocks to activate custom tiles. Tile level is stored in the item's PDC (`tile_level` key).

| Block | Tile | Function |
|---|---|---|
| Cyan Glazed Terracotta | AutoSeller | Sells items from chest below to island receiver |
| Orange Glazed Terracotta | AutoMiner | Mines block below, deposits into chest above |
| Purple Glazed Terracotta | Catalyst | Spawns ore blocks above based on island ore preferences |
| Hopper (with PDC level) | HopperTile | Custom hopper respecting island upgrade limits |

---

## Custom Enchants

80 custom enchants implemented across 5 categories. All stored via PersistentDataContainer on items.

| Category | Count | Examples |
|---|---|---|
| Sword | 32 | Lifesteal, Vampire, Backstabber, Thunderbolt |
| Armor | 22 | LifeShield, Berserker, Frozen, Enlighten |
| Block | 17 | Smelting, Explosion, Replanter, Tinkerer |
| Bow | 5 | Healing, Paralyze, Piercing, Launcher |
| Touch | 2 | Demolisher, Scythe |

---

## Architecture

```
SkyblockCore/
├── db/          — HikariCP pool + repositories per entity
├── island/      — Island data model + world management
├── gang/        — Gang data model + manager
├── user/        — Player data + async load/save
├── enchants/    — PDC-based custom enchant system
├── tiles/       — Custom block entity system (YAML persisted)
├── command/     — CommandExecutor implementations
├── events/      — Bukkit event listeners
├── pets/        — ArmorStand-based pet system (stubbed)
├── scoreboard/  — Per-player sidebar scoreboard
└── util/        — SellPrices, constants
```

---

## Known Limitations

- Island full data (members, upgrades, homes) is not yet loaded from DB on restart — only name/world are restored
- Shop GUI not yet implemented (Bedrock forms had no Java equivalent, inventory GUI needed)
- Pets only spawn a static ArmorStand — no follow AI or passive effects yet
- Spawner system not yet ported
- Chat formatting (gang chat, rank tags) not yet implemented
