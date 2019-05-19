<?php

ini_set('log_errors', 'on');
ini_set('error_log', 'php.log');
session_start();

// 主人公たちを格納
$humans = array();
// モンスターたちを格納
$monsters = array();

// 性別クラス(クラス定数)
class Sex{
  const MAN = 1;
  const WOMAN = 2;
}
// モンスター名クラス(クラス定数)
class MonsterName{
  const PIKATYU = 1;
  const REKKUZA = 2;
  const MYUTU = 3;
  const ZEKUROMU = 4;
  const MYU = 5;
  const DEOKISISU = 6;
  const RETHIOSU = 7;
  const SATOSHI = 8;
}

// 抽象クラス作成(必ず継承して使用するクラス = 直接新スタンス生成不可)(HumanクラスとMonsterクラスの共通部分)
abstract class Creature{
  // プロパティ
  protected $name;
  protected $hp;
  protected $defaultHp;
  protected $maxAttack;
  protected $minAttack;
  protected $specialMove;
  // セッター
  public function setName($str){ // 一応練習のため使う
    $this->name = $str;
  }
  public function setHp($str){
    $this->hp = filter_var($str, FILTER_VALIDATE_INT);// 値が整数かチェック
  }
  // ゲッター
  public function getName(){ // 一応練習のため使う
    return $this->name;
  }
  public function getHp(){
    return $this->hp;
  }
  public function getDefaultHp(){
    return $this->defaultHp;
  }
  public function getMinAttack(){
    return $this->minAttack;
  }
  public function getMaxAttack(){
    return $this->maxAttack;
  }
  public function getSpecialMove(){
    return $this->specialMove;
  }
  // 抽象メソッド(このクラスの継承先のクラスでオーバーライド必須)
  abstract public function scream();
  // メソッド
  public function attack($targetObj){
    $attackPoint = mt_rand($this->getMinAttack(), $this->getMaxAttack());
    if(!mt_rand(0, 19)){ // 1/20の確率で必殺技(0=false)
      History::setHistory($this->getName().'の必殺ワザ：'.$this->getSpecialMove().'!!'); // HistoryクラスのsetHistoryメソッドを使用
      $attackPoint *= 2.5;
      $attackPoint = round($attackPoint); // round:四捨五入(=結果は整数のはず)
    }else{
      if(!mt_rand(0,9)){ // 1/10の確率でクリティカル
        History::setHistory($this->getName().'のクリティカルヒット!!');
        $attackPoint *= 1.5;
        $attackPoint = round($attackPoint);
      }
    }
    // ターゲット(主人公かモンスター)のHPからダメージ分を引く
    $targetObj->setHp($targetObj->getHp() - $attackPoint);
    History::setHistory($targetObj->getName().'は'.$attackPoint.'のダメージ!');
  }
}

// 主人公(Human)クラス作成
class Human extends Creature{
  // プロパティ
  private $sex;
  // コンストラクタ
  public function __construct($sex, $name, $hp, $defaultHp, $maxAttack, $minAttack, $specialMove){
    $this->sex = $sex;
    $this->name = $name;
    $this->hp = $hp;
    $this->defaultHp = $defaultHp;
    $this->maxAttack = $maxAttack;
    $this->minAttack = $minAttack;
    $this->specialMove = $specialMove;
  }
  // セッター
  public function setSex($str){
    $this->sex = $str;
  }
  // ゲッター
  public function getSex(){
    return $this->sex;
  }
  // メソッド(抽象メソッドを実装)
  public function scream(){
    switch($this->sex){
      case SEX::MAN :
        History::setHistory('主人公(男)「あ゛あ゛あ゛あ゛あ゛！！」');
        break;
      case SEX::WOMAN :
        History::setHistory('主人公(女)「いちちちちっ！」');
        break;
    }
  }
}

// モンスター(Monster)クラス作成
class Monster extends Creature{
  // プロパティ
  private $img;
  // コンストラクタ
  public function __construct($img, $scream, $name, $hp, $defaultHp, $maxAttack, $minAttack, $specialMove){
    $this->img = $img;
    $this->scream = $scream;
    $this->name = $name;
    $this->hp = $hp;
    $this->defaultHp = $defaultHp;
    $this->maxAttack = $maxAttack;
    $this->minAttack = $minAttack;
    $this->specialMove = $specialMove;
  }
  // ゲッター
  public function getImg(){
    return $this->img;
  }
  // メソッド(抽象メソッドを実装)
  public function scream(){
    switch($this->scream){
      case MonsterName::PIKATYU :
        History::setHistory($this->getName().'「ピカ〜っ!!」');
        break;
      case MonsterName::REKKUZA :
        History::setHistory($this->getName().'「痛いでやんす」');
        break;
      case MonsterName::MYUTU :
        History::setHistory($this->getName().'「痛いやで〜」');
        break;
      case MonsterName::ZEKUROMU :
        History::setHistory($this->getName().'「痛いで」');
        break;
      case MonsterName::MYU :
        History::setHistory($this->getName().'「ミュ〜♡」');
        break;
      case MonsterName::DEOKISISU :
        History::setHistory($this->getName().'「痛いでござるよ」');
        break;
      case MonsterName::RETHIOSU :
          History::setHistory($this->getName().'「ほげ〜〜〜〜」');
          break;
      case MonsterName::SATOSHI :
          History::setHistory($this->getName().'「ゔあ゛〜〜〜〜〜〜〜〜!!!!!!!」');
          break;
    }
  }
}

// 履歴(History)クラス用のインターフェイス作成


// 履歴(History)クラス作成
class History{
  // メソッド
  public static function setHistory($str){
    if(empty($_SESSION['history'])){ // $_SESSION['history']が無かった場合に作成
      $_SESSION['history'] = '';
    }
    $_SESSION['history'] .= $str.'<br>';
  }
  public function historyClear(){
    unset($_SESSION['history']);
  }
}

// 主人公(Human)のインスタンス生成
$humans[] = new Human(SEX::MAN, '主人公(男)', 900, 900, 100, 50, '火竜の咆哮');
$humans[] = new Human(SEX::WOMAN, '主人公(女)', 1000, 1000, 90, 40, '循環の剣');
// モンスター(Monster)のインスタンス生成
$monsters[] = new Monster('img/monster01.png', MonsterName::PIKATYU, 'ピカチュウ', 150, 150, 55, 25, '10万ボルト');
$monsters[] = new Monster('img/monster02.png', MonsterName::REKKUZA, 'レックウザ', 320, 320, 80, 50, 'ドラゴンテール');
$monsters[] = new Monster('img/monster03.png', MonsterName::MYUTU, 'ミュウツー', 360, 360, 75, 35, 'サイコカッター');
$monsters[] = new Monster('img/monster04.png', MonsterName::ZEKUROMU, 'ゼクロム', 400, 400, 70, 40, 'らいげき');
$monsters[] = new Monster('img/monster05.png', MonsterName::MYU, 'ミュウ', 200, 200, 50, 20, 'サイコシュック');
$monsters[] = new Monster('img/monster06.png', MonsterName::DEOKISISU, 'デオキシス', 300, 300, 65, 35, 'はかいこうせん');
$monsters[] = new Monster('img/monster07.png', MonsterName::RETHIOSU, 'ラティオス', 280, 280, 60, 30, 'サイコキネシス');
$monsters[] = new Monster('img/monster08.png', MonsterName::SATOSHI, 'サトシ', 500, 500, 100, 50, 'サトシ');

// メソッド
function createHuman(){
  global $humans;
  $human = $humans[mt_rand(0,1)];
  $_SESSION['human'] = $human;
}

function createMonster(){
global $monsters;
$monster = $monsters[mt_rand(0,7)];
$_SESSION['monster'] = $monster;
History::setHistory($_SESSION['monster']->getName().'が現れた！！');
}

function init(){
  $_SESSION['knockDownCount'] = 0;
  createHuman();
  createMonster();
}

function gameOver(){
  $_SESSION = array();
}


if(!empty($_POST)){
  $startFlg = (!empty($_POST['start'])) ? true : false;
  $attackFlg = (!empty($_POST['attack'])) ? true : false;
  $escapeFlg = (!empty($_POST['escape'])) ? true : false;

  // もしも、$startFlgがtrueだった場合
  if($startFlg){
    History::setHistory('バトルスタート！');
    init();
  }else{
    // もしも、$attackFlgがtrueだった場合
    if($attackFlg){
      // モンスターへ攻撃
      History::setHistory($_SESSION['human']->getName().'の攻撃！！');
      $_SESSION['human']->attack($_SESSION['monster']);
      $_SESSION['monster']->scream();
      // 主人公へ攻撃
      History::setHistory($_SESSION['monster']->getName().'の攻撃！！');
      $_SESSION['monster']->attack($_SESSION['human']);
      $_SESSION['human']->scream();

      // 自分のHPが0になった場合
      if($_SESSION['human']->getHp() <= 0){
        gameOver();
      }else{
          if($_SESSION['monster']->getHp() <= 0){
          History::setHistory($_SESSION['monster']->getName().'を倒した！!');
          $_SESSION['knockDownCount'] += 1;
          createMonster();
        }
      }
    // もしも、$escapeFlgがtrueだった場合
    }elseif($escapeFlg){
      History::setHistory('逃げた!!');
      createMonster();
    }
  }
}

?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>pokemon</title>
    <link rel="stylesheet" type="text/css" href="app.css">
    </style>
  </head>
  <body>
    <!-- 1.セッションがない場合 -->
    <?php if(empty($_SESSION)){ ?>
      <div id="start-area">
        <div class="title">
          <img src="https://fontmeme.com/permalink/190304/8ff7afc6cad3a99330b5ae35d530a0c4.png" alt="font-pokemon-logo" border="0">
        </div>
        <form class="" action="" method="post">
          <div class="start-frame">
            <input type="submit" name="start" value="バトルスタート!!">
          </div>
        </form>
      </div>
    <!-- 2.セッションがある場合 -->
    <?php }else{ ?>
      <div id="battle">
        <div id="opponent-area">
          <h2 class="opponent-name"><?php echo $_SESSION['monster']->getName(); ?>が現れた！！</h2>
          <div class="image-frame">
            <img class="image" src="<?php echo $_SESSION['monster']->getImg(); ?>">
          </div>
          <p class="opponent-hp">HP : <?php echo $_SESSION['monster']->getHp(); ?></p>
        </div>
        <div id="myself-area">
          <div class="history-frame myself-frame">
            <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
          </div>
          <div class="kill-frame myself-frame">
            <p>倒したモンスター数 : <?php echo $_SESSION['knockDownCount']; ?></p>
          </div>
          <div class="hp-frame myself-frame">
            <p class="hp-val">自分のHP : <?php echo $_SESSION['human']->getHp(); ?></p>
            <meter class="hp-gage" value="<?php echo $_SESSION['human']->getHp(); ?>"
              min="0"
              low="<?php echo ($_SESSION['human']->getDefaultHp()*0.2); ?>"
              high="<?php echo ($_SESSION['human']->getDefaultHp()*0.6); ?>"
              max="<?php echo $_SESSION['human']->getDefaultHp(); ?>"
              optimum="<?php echo ($_SESSION['human']->getDefaultHp()*0.8); ?>"></meter> <!-- optimumにより最適領域(緑ゲージ)が100%~80%の間に指定 -->
          </div>
          <form class="behavior-frame myself-frame" action="" method="post">
            <div class="button-top">
              <input type="submit" name="attack" value="たたかう">
              <input type="submit" name="escape" value="逃げる">
            </div>
            <div class="button-bottom">
              <input type="submit" name="start" value="リスタート">
            </div>
          </form>
        </div>
      </div>
    <?php } ?>
  </body>
</html>
