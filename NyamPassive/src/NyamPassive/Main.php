<?php 

namespace RPGGame;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\entity\Effect;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use RPGGame\task\MessageTask;
use pocketmine\event\player\PlayerDeathEvent;
class Main extends PluginBase implements Listener{
	private $passive,$psdb;
	private $players, $playersdb;
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("RPGGame");
		@mkdir($this->getDataFolder());
		$this->passive = new Config($this->getDataFolder()."passive.yml", Config::YAML);
		$this->players = new Config($this->getDataFolder()."players.yml", Config::YAML);
		$task = new MessageTask("이 서버는 RPGGame을 사용 중 입니다.");
		$this->getServer()->getScheduler()->scheduleDelayedRepeatingTask($task,20*60*5);
	}
	public function onDisable(){
		$this->passive->save();
		$this->players->save();
	}
	public function onSpawn(PlayerRespawnEvent $e){
		$player = $e->getPlayer();
		$player->getEffect(10)->setDuration(60)->setAmplifier(8);
		$player->sendMessage("부활후 3초간 재생효과(*8)가 붙습니다");
	}
	public function onKill(PlayerDeathEvent $e){
		$victim = $e->getEntity();
		$damager = $e->getEntity()->getLastDamageCause()->getDamager()->getName();
		$passivea=$this->passive->get($damager);
		if($passivea=1){
			$effect1 = Effect::getEffect(1)->setDuration(100)->setAmplifier(2);
			$damager->addEffect($effect1);
			$effect2 = Effect::getEffect(5)->setDuration(100)->setAmplifier(3);
			$damager->addEffect($effect2);
			$effect3 = Effect::getEffect(8)->setDuration(100)->setAmplifier(5);
			$damager->addEffect($effect3);
		}
		//플레이어 처치시 신속2 힘3 점프강화5 패시브 구현
		if($passivea=2){
			$damager->setHealth($this->getHealth() + 10);
		}
		//나름대로 구현은 했는데 이게 맞는건지 모르겠네요 수정 환영입니다 :)
	}
	public function onJoin(PlayerJoinEvent $e){
		$pname = $e->getPlayer()->getName();
		$pjoint = $this->players->get($pname);
		$passivej = $this->passive->get($pname);
		if (!$pjoint == true){
			$this->players->set($e->getPlayer()->getName(),true);
			$this->passive->set($e->getPlayer()->getName(), mt_rand(1,12));
			$this->passive->save();
			$this->players->save();
			$passivej = $this->passive->get($pname);
			if ($passivej == 1){
				$e->getPlayer()->sendMessage(TextFormat::RED."당신의 패시브 코드는 1번.");
			}
			if ($passivej == 2){
				$e->getPlayer()->sendMessage(TextFormat::RED."당신의 패시브 코드는 2번.");
			}
			if ($passivej == 3){
				$e->getPlayer()->sendMessage(TextFormat::RED."당신의 패시브 코드는 3번.");
			}
			if ($passivej == 4){
				$e->getPlayer()->sendMessage(TextFormat::RED."당신의 패시브 코드는 4번.");
			}
			if ($passivej == 5){
				$e->getPlayer()->sendMessage(TextFormat::RED."당신의 패시브 코드는 5번.");
			}
			if ($passivej == 6){
				$e->getPlayer()->sendMessage(TextFormat::RED."당신의 패시브 코드는 6번.");
			}
			if ($passivej == 7){
				$e->getPlayer()->sendMessage(TextFormat::RED."당신의 패시브 코드는 7번.");
			}
			if ($passivej == 8){
				$e->getPlayer()->sendMessage(TextFormat::RED."당신의 패시브 코드는 8번.");
			}
			if ($passivej == 9){
				$e->getPlayer ()->sendMessage (TextFormat::RED."당신의 패시브 코드는 9번.");
			}
		}
		if (!$this->players->exists($e->getPlayer()->getName())){
			$this->players->set($e->getPlayer()->getName(),false);
			$this->players->save();
		}
	}
	public function onCommand(CommandSender $sender,Command $command, $label,array $args){
		$sname = $sender->getName();
		if ($command == "패시브"){
			switch ($args[0]){
				case "설정":
					if (!$sender->isOp()){
						$sender->sendMessage("당신은 오피가 아닙니다.");
					}else{
						$this->setPassive($sender, $args[1]);
						$sender->sendMessage("강제적으로 패시브가 변경되었습니다.");
					}
				case "정보":
					$this->viewPassive($sender);
			}
		}
	}
	public function setPassive($player,$number){
		$pname = $player->getName();
		$this->passive->set($pname,$number);
	}
	public function viewPassive($player){
		$pas = $this->passive->get($player->getName());
		return $pas;
	}
}

?>