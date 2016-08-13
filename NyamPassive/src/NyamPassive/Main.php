<?php 

namespace NyamPassive;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\entity\Effect;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\entity\EntityDamageEvent;
class Main extends PluginBase implements Listener{
	private $passive,$psdb;
	private $players, $playersdb;
	private $cool1 = [], $cool2 = [];
	function makeTimeStamp(){
		$date = date("Y-m-d H:i:s");
		$yy = substr($date, 0, 4);
		$mm = substr($date, 5, 2);
		$dd = substr($date, 8, 2);
		$hh = substr($date, 11, 2);
		$ii = substr($date, 14, 2);
		$ss = substr($date, 17, 2);
		return mktime($hh, $ii, $ss, $mm, $dd, $yy);
	}
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("NyamPassive :)");
		@mkdir($this->getDataFolder());
		$this->passive = new Config($this->getDataFolder()."passive.yml", Config::YAML);
		$this->players = new Config($this->getDataFolder()."players.yml", Config::YAML);
	}
	public function onDisable(){
		$this->passive->save();
		$this->players->save();
	}
	public function onSpawn(PlayerRespawnEvent $e){
		$player = $e->getPlayer();
		$player->addEffect(Effect::getEffect(6)->setDuration(60)->setAmplifier(7));
		$player->sendMessage("부활후 3초간 재생효과(*8)가 붙습니다");
	}
	public function onKill(PlayerDeathEvent $e){
		$killed = $e->getEntity();
		if ($killed instanceof Player){
			$cause = $killed->getLastDamageCause();
			if ($cause instanceof EntityDamageByEntityEvent){
				$damager = $cause->getDamager();
				if ($damager instanceof Player){
					//여기서부터 코드 작성 시작 killed는 죽은 사람을 뜻함.
					$killed = $e->getEntity();
					$killedn = $killed->getName();
					$damagern = $damager->getName();
					$passivea=$this->passive->get($damagern);
					if ($passivea == 1){
						$effect1 = Effect::getEffect(1)->setDuration(100)->setAmplifier(1);
						$damager->addEffect($effect1);
						$effect2 = Effect::getEffect(5)->setDuration(100)->setAmplifier(2);
						$damager->addEffect($effect2);
						$effect3 = Effect::getEffect(8)->setDuration(100)->setAmplifier(4);
						$damager->addEffect($effect3);
					}
					//플레이어 처치시 신속2 힘3 점프강화5 패시브 구현
					if ($passivea == 2){
						$damager->setHealth($damager->getHealth() + 10);
						$damager->sendMessage("패시브의 효과로 체력 10을 회복합니다.");
					}
					//플레이어 처치시 damager은 hp10을 회복함
					if ($passivea == 3){
						 $this->cool2[$killedn] = $this->makeTimeStamp();
						 $respawnr = $this->cool1[$killedn] - $this->cool2[$killedn];
						 if ($respawnr >= 300){
						 	$x = $killed->getX();
						 	$y = $killed->getY();
						 	$z = $killed->getZ();
						 	$killed->teleport($x, $y, $z);
						 	$killed->setSpawn($x, $y, $z);
						 	$this->cool1[$killedn] = $this->makeTimeStamp();
						 	$killed->sendMessage("패시브의 효과로 죽은 자리에서 스폰됩니다.");
						 }
						 if ($respawnr < 300){
						 	$killed->sendMessage("아직 쿨타임이 지나지 않아 정상적으로 스폰 됩니다.");
						 }
						//부활 구현
					}
					if($passivea == 4){
						$this-> cool2[$killedn]=$this->makeTimeStamp();
						$cooltime=$this->cool1[$killedn] - $this->cool2[$killedn];
						if($cooltime >= 300);
						$damager->kill();
						$e->setDeathMessage($killedn."님이".$damagern."님과 함께 사망하였습니다");
						$this->cool1[$killedn] = $this->makeTimeStamp();
					}
				}
			}
		}
	}
	public function onJoin(PlayerJoinEvent $e){
		$pname = $e->getPlayer()->getName();
		$pjoint = $this->players->get($pname);
		$passivej = $this->passive->get($pname);
		if (!$pjoint == true){
			$this->players->set($e->getPlayer()->getName(),true);
			$this->passive->set($e->getPlayer()->getName(), mt_rand(1,4));
			$this->passive->save();
			$this->players->save();
			$passivej = $this->passive->get($pname);
			if ($passivej == 1){
				$e->getPlayer()->sendMessage(TextFormat::RED."당신의 패시브는 미치광이입니다.");
			}
			if ($passivej == 2){
				$e->getPlayer()->sendMessage(TextFormat::RED."당신의 패시브는 흡수입니다.");
			}
			if ($passivej == 3){
				$e->getPlayer()->sendMessage(TextFormat::RED."당신의 패시브는 부활입니다.");
			    $this->cool1[$pname] = $this->makeTimeStamp();
			}
			if ($passivej == 4){
				$e->getPlayer()->sendMessage(TextFormat::RED. "당신의 패시브는 길동무입니다");
				$this->cool1[$pname] = $this->makeTimeStamp();
			}
			if ($passivej == 5){
				$e->getPlayer()->sendMessage(TextFormat::RED. "당신의 패시브는 섬광입니다.");
				$this->cool1[$pname] = $this->makeTimeStamp();
			}
			if ($passivej == 6){
				$e->getPlayer()->sendMessage(TextFormat::RED. "당신의 패시브는 발악입니다.");
				$this->cool1[$pname] = $this->makeTimeStamp();
			}
			if ($passivej == 7){
				$e->getPlayer()->sendMessage(TextFormat::RED. "당신의 패시브는 광기입니다.");
			}
		}
		if (!$this->players->exists($e->getPlayer()->getName())){
			$this->players->set($e->getPlayer()->getName(),false);
			$this->players->save();
		}
	}
	public function onCommand(CommandSender $sender,Command $command, $label,array $args){
		$sname = $sender->getName();
		if ($command == "setPassive"){
						$this->setPassive($sender, $args[1]);
						$sender->sendMessage("강제적으로 패시브가 변경되었습니다.");
		}		
    }
    public function onAttack(EntityDamageByEntityEvent $e){
    	$damager = $e->getDamager();
    	$entity = $e->getEntity();
    	$passivea = $this->viewPassive($damager);
    	if ($passivea == 5){
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