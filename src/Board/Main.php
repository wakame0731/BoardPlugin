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

class Main extends PluginBase implements Listener{
	//サーバー起動時に呼び出される処理
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        switch (strtolower($command->getName())) {
            case "sign":
                if(!isset($args[0])) return false;
                if($sender instanceof Player){
                    if(mb_strlen($args[0])>=1 and mb_strlen($args[0])<=42){
                        $this->setBoard($sender,$args[0]);
                        $sender->sendMessage("掲示板に「" . $args[0] . "」という文を登録しました。");
                        return true;
                    }
                    $sender->sendMessage("42文字以上の文は登録できません");
                    return false;
                }else{        
                    $s->sendMessage("コンソールからは実行できません");
                    return false;
                }
            break;
        }
        return false;
    }
    public function setBoard($player,$message){
        $x=$player->getX();
        $y=$player->getY();
        $z=$player->getZ();   
        $tx1=mb_substr($message,0,14);
        $tx2=mb_substr($message,15,14);
        $tx3=mb_substr($message,29,14);
        $nbt = new CompoundTag("", [
            "id" => new StringTag("id", Tile::SIGN),
            "x" => new IntTag("x", $x),
            "y" => new IntTag("y", $y),
            "z" => new IntTag("z", $z),
            "Text1" => new StringTag("Text1",$tx1),
            "Text2" => new StringTag("Text2",$tx2 ),
            "Text3" => new StringTag("Text3",$tx3 ),
            "Text4" => new StringTag("Text4", "By ".$player->getName())
        ]);
        $nbt->Creator = new StringTag("Creator", $player->getRawUniqueId());
        $player->getLevel()->setBlock(new Vector3($x,$y,$z), Block::get(Block::SIGN_POST,0));
        $sign=Tile::createTile(Tile::SIGN,$player->getLevel(), $nbt);
        $sign->spawnToAll();
    }
}
