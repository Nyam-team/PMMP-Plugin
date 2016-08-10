<?php 

namespace CookierunPro;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
class Main extends PluginBase implements Listener{
	private $hcookie, $hcookiedb;
	private $scookie, $scookiedb;
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getLogger()->info("정상적으로 쿠키런 플러그인이 작동됩니다.");
		//콘피그
		$this->scookie = new Config($this->getDataFolder()."selectCookie.yml", Config::YAML);
		$this->scookiedb = $this->scookie->getAll();
		$this->hcookie = new Config($this->getDataFolder()."haveCookie.yml", Config::YAML);
		$this->hcookiedb = $this->hcookie->getAll();
	}
	public function onJoin(PlayerJoinEvent $e){
		if (!$this->hcookie->exists($e->getPlayer()->getName())){
			$e->getPlayer()->sendMessage("어머! 처음 오셨군요. 용감한 쿠키가 지급됩니다.");
			$e->getPlayer()->getInventory()->addItem(287, 1);
			$this->hcookie->set($e->getPlayer()->getName(),["brave"]);
		}
		$e->getPlayer()->sendMessage("[서버]".$e->getPlayer()->getName()."님 쿠키런 서버에 오신 것을 환영합니다.");
	}
	public function onCommand(CommandSender $sender,Command $command, $label,array $args){
		if ($command == "cookie"){
			switch ($args[0]){
				case "정보":
					foreach ($this->hcookie->get($sender->getName()) as $have){
						$sender->sendMessage("쿠키: ".$have);
					}
			}
		}
	}
}

?>