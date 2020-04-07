<?php

namespace BedrockPlugins\BedrockChat;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EventListener implements Listener {

    public function onChat(PlayerChatEvent $event) {

        $player = $event->getPlayer();
        $playername = $player->getName();
        $message = $event->getMessage();

        if ($player->hasPermission("bedrockchat.immunity")) return;

        if (BedrockChat::isAntiDuplicate()) {
            if (BedrockChat::isDuplicated($playername, $message)) {
                $player->sendMessage(BedrockChat::$prefix . "Please don't duplicate your messages");
                $event->setCancelled();
                return;
            }
            BedrockChat::$last_messages[$playername] = $message;
        }

        if (BedrockChat::isAntiCaps()) {
            if (strtoupper($message) === $message) {
                if (strlen($message) > BedrockChat::$mincaps) {
                    $player->sendMessage(BedrockChat::$prefix . "Too many capital letters");
                    $event->setCancelled();
                    return;
                }
            }
        }

        if (BedrockChat::isAntiSwearing()) {
            if (BedrockChat::hasBadWord($message)) {
                $player->sendMessage(BedrockChat::$prefix . "Please mind your language");
                $event->setCancelled();
                return;
            }
        }

        if (BedrockChat::isAntiSpam()) {
            if (array_key_exists($playername, BedrockChat::$countdowns)) {
                $player->sendMessage(BedrockChat::$prefix . "You have to wait " . BedrockChat::$countdowns[$playername] . " second(s)");
                $event->setCancelled();
                return;
            }
            BedrockChat::$countdowns[$playername] = BedrockChat::$delay;
        }

    }

    public function onQuit(PlayerQuitEvent $event) {

        $playername = $event->getPlayer()->getName();

        if (array_key_exists($playername, BedrockChat::$countdowns)) {
            unset(BedrockChat::$countdowns[$playername]);
        }

        if (array_key_exists($playername, BedrockChat::$last_messages)) {
            unset(BedrockChat::$last_messages[$playername]);
        }

    }

}