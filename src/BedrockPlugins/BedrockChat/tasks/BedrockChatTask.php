<?php

namespace BedrockPlugins\BedrockChat\tasks;

use BedrockPlugins\BedrockChat\BedrockChat;
use pocketmine\scheduler\Task;

class BedrockChatTask extends Task {

    public function onRun(int $currentTick) {
        foreach (BedrockChat::getInstance()->getServer()->getOnlinePlayers() as $player) {
            if (array_key_exists($player->getName(), BedrockChat::$countdowns)) {
                BedrockChat::$countdowns[$player->getName()]--;
                if (BedrockChat::$countdowns[$player->getName()] <= 0) {
                    unset(BedrockChat::$countdowns[$player->getName()]);
                }
            }
        }
    }

}