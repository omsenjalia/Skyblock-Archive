# FallenTech Skyblock Core (Java Conversion)

Java port of the original FallenTech Skyblock plugin for Paper 1.21.x.

## Conversion Progress - Session 5

| Feature | Status | Notes |
| :--- | :--- | :--- |
| Core Plugin Structure | ✅ Complete | Manager-based architecture |
| Database Layer | ✅ Complete | HikariCP (SQLite/MySQL) |
| User System | ✅ Complete | Async loading, persistence |
| Island System | ✅ ~95% | Full persistence working |
| Gang (Guild) System | 🟡 Partial | Core logic and persistence |
| Custom Items/Enchants | 🟡 Partial | Lore-based system |
| Scoreboard | ✅ Complete | Live data, 1s updates |
| Custom Tiles | 🟡 Partial | AutoMiner, AutoSeller, Catalyst |

**Overall Progress: ~48%**

## Project Information
- **Target:** Paper 1.21.1 (Java 21)
- **Build System:** Maven
- **Database:** SQLite (default) / MySQL

## Development
- Build: `mvn clean package`
- Artifacts: `target/SkyblockCore-1.0-SNAPSHOT.jar`

---
*Original PHP project notes:*
the required plugins are very simple to remove. can run without mysql, but must have it for records to be saved
arena.json is used for koth. check setpos command
common_settings.json 
- season is the season number
- name is the display season used for various things, notably the info floating text
- vaulted is the list of enchant ids to be made vaulted
- warps are set with setpos
tags.json are automatically loaded
