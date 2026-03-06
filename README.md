# FallenTech Skyblock core

the required plugins are very simple to remove. can run without mysql, but must have it for records to be saved

the build script should work as long as you replace the php libs used

this is the branch we had with the most commits. i doubt it was the most recent one used in prod and probably has my slop i was pushing at the time. customies data isnt there so that stuff will break but it never changed any previous stuff

in resources/ 

arena.json is used for koth. check setpos command<br>
common_settings.json 
- season is the season number
- name is the display season used for various things, notably the info floating text
- tutorial-folder was part of the api 
- vaulted is the list of enchant ids to be made vaulted
- warps are set with setpos
tags.json are automatically loaded

