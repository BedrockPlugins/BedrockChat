<?php

namespace BedrockPlugins\BedrockChat;

use BedrockPlugins\BedrockChat\tasks\BedrockChatTask;
use MongoDB\Driver\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class BedrockChat extends PluginBase {

    public static $instance;

    public static $prefix = TextFormat::GRAY . "BedrockChat " . TextFormat::DARK_GRAY . "» ". TextFormat::RESET;

    public static $last_messages = [];
    public static $forbidden_words = [];

    public static $delay = 4;
    public static $countdowns = [];

    public static $anticaps = true, $mincaps = 5;
    public static $antiswearing = true;
    public static $antispamming = true;
    public static $antiduplicate = true;

    public function onEnable() {

        self::$instance = $this;

        $this->saveResource("config.yml", false);

        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        if ($config->exists("anticapslock") && is_bool($config->get("anticapslock"))) {
            self::$anticaps = $config->get("anticapslock");
        }
        if (self::$anticaps && $config->exists("mincaps") && is_numeric($config->get("mincaps"))) {
            self::$mincaps = $config->get("mincaps");
        }
        if ($config->exists("antiswearing") && is_bool($config->get("antiswearing"))) {
            self::$antiswearing = $config->get("antiswearing");
        }
        if (self::$antiswearing && $config->exists("swearwords") && is_array($config->get("swearwords"))) {
            self::$forbidden_words = $config->get("swearwords");
        }
        if ($config->exists("antispamming") && is_bool($config->get("antispamming"))) {
            self::$antispamming = $config->get("antispamming");
        }
        if (self::$antispamming && $config->exists("delay") && is_numeric($config->get("delay"))) {
            self::$delay = $config->get("delay");
        }
        if ($config->exists("antiduplicate") && is_bool($config->get("antiduplicate"))) {
            self::$antiduplicate = $config->get("antiduplicate");
        }

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new BedrockChatTask(), 20);

        $this->getLogger()->info("BedrockChat has been loaded");
    }

    public static function getInstance() : self {
        return self::$instance;
    }

    public static function isAntiSpam() : bool {
        return self::$antispamming;
    }

    public static function isAntiSwearing() : bool {
        return self::$antiswearing;
    }

    public static function isAntiCaps() : bool {
        return self::$anticaps;
    }

    public static function isAntiDuplicate() : bool {
        return self::$antiduplicate;
    }

    public static function hasBadWord($message) : bool {
        $array = explode(" ", $message);
        foreach ($array as $value) {
            if (in_array($value, self::$forbidden_words)) return true;
        }
        return false;
    }

    public static function isDuplicated($sendername, $message) : bool {
        if (array_key_exists($sendername, self::$last_messages)) {
            $lastmessage = self::$last_messages[$sendername];
            return $message === $lastmessage;
        }
        return false;
    }

}