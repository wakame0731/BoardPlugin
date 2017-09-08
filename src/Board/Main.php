<?php
namespace Board;
//必須
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
//コマンド関係
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\CommandExecutor;
//ブロック設置
use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\Player;
//看板関係
use pocketmine\tile\Tile;
use pocketmine\tile\Sign;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Spawnable;
//コンフィグ関係
use pocketmine\utils\Config;
//イベント関係
use pocketmine\event\player\PlayerInteractEvent;

class Main extends PluginBase implements Listener{

    public static $SectionList=["§0","§1","§2","§3","§4","§5","§6","§7",
                                "§8","§9","§a","§b","§c","§d","§e","§f",
                                "§k","§l","§m","§n","§o","§r"];
    public static $NGWordList=["死ね","カス"];

	//サーバー起動時に呼び出される処理
	public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        if(!file_exists($this->getDataFolder())){
            mkdir($this->getDataFolder(), 0744, true);
        }         
    }
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        switch (strtolower($command->getName())) {
            case "sign":
                if(!isset($args[0])) return false;
                if($sender instanceof Player){
                        $message=$this->CheckSection($args[0]);
                        $message=$this->CheckNG($message);
                    if(mb_strlen($message)<=42){
                        $this->setBoard($sender,$message);
                        $sender->sendMessage("§f掲示板に「" . $args[0] . "§f」という文を登録しました。");
                        return true;
                    }
                    $sender->sendMessage("42文字以上の文は登録できません");
                    return false;
                }else{        
                    $sender->sendMessage("コンソールからは実行できません");
                    return false;
                }
            break;
            case "board":
                
            break;
            case "regist":
            break;
        }
        return false;
    }
    public function PlayerInteractEvent(PlayerInteractEvent $event){
        if($event->getAction()===1){
            if($event->getBlock()->getId()===Block::SIGN_POST){
                $sign = $event->getPlayer()->getLevel()->getTile(new Vector3($event->getBlock()->getX(),$event->getBlock()->getY(),$event->getBlock()->getZ()));
                $Text=$sign->getText();
                $event->getPlayer()->sendMessage("看板をタップしました\n".$Text[0]."\n".$Text[1]."\n".$Text[2]."\n".$Text[3]);
            }
        }
    }

    public function setBoard($player,$message){
        $x=$player->getX();
        $y=$player->getY();
        $z=$player->getZ();
        $tx1=mb_substr($message,0,14);
        $tx2=mb_substr($message,14,14);
        $tx3=mb_substr($message,28,14);
        $nbt = new CompoundTag("", [
            "id" => new StringTag("id", Tile::SIGN),
            "x" => new IntTag("x", $x),
            "y" => new IntTag("y", $y),
            "z" => new IntTag("z", $z),
            "Text1" => new StringTag("Text1","§f".$tx1),
            "Text2" => new StringTag("Text2","§f".$tx2 ),
            "Text3" => new StringTag("Text3","§f".$tx3 ),
            "Text4" => new StringTag("Text4", "§fBy ".$player->getName())
        ]);
        $nbt->Creator = new StringTag("Creator", $player->getRawUniqueId());
        $player->getLevel()->setBlock(new Vector3($x,$y,$z), Block::get(Block::SIGN_POST, floor((($player->yaw + 180) * 16 / 360) + 0.5) & 0x0f),true);
        $sign=Tile::createTile(Tile::SIGN,$player->getLevel(), $nbt);
        $sign->spawnToAll();
    }

    public function CheckSection($message){
        foreach (self::$SectionList as $string) {
            $message=str_replace($string,"",$message);
        }
        return $message;
    }
    public function CheckNG($message){
        foreach (self::$NGWordList as $string) {
            $message="";
        }
        return $message;
    }

    public function loadData($Name){

    }

    public function SaveData($Name){

    }

    public function RegistBoard(){

    }
} 
