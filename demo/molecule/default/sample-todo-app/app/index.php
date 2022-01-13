<?php
/*
 * ThisTODO app is inspired by
 * https://gist.github.com/vinzenz/2872dfb74573fcbf56663a91f7182406
 */

require("./config.php");

$db = new PDO("mysql:host=".DB_HOST.";port=".DB_PORT.";dbname=".DB_NAME, DB_USER, DB_PASS);
$mysql_version = $db->query("SELECT version()")->fetchColumn();

$ITEMS = array();

function get(&$var, $default=null) {
    return isset($var) ? $var : $default;
}

switch(get($_POST["action"])) {
case "add":
  $title = get($_POST["title"]);
  $stmt = $db->prepare("INSERT INTO todo VALUES(NULL, ?, FALSE)");
  if(!$stmt->execute(array($title))) {
      die(print_r($stmt->errorInfo(), true));
  }
  header("Location: ".$_SERVER["DOCUMENT_URI"]);
  die();
case "toggle":
  $id = get($_POST["id"]);
  if(is_numeric($id)) {
    $stmt = $db->prepare("UPDATE todo SET done = !done WHERE id = ?");
    if(!$stmt->execute(array($id))) {
      die(print_r($stmt->errorInfo(), true));
    }
  }
  header("Location: ".$_SERVER["DOCUMENT_URI"]);
  die();
default:
  break;
}

$stmt = $db->prepare("SELECT * from todo");
if ($stmt->execute()) {
  $ITEMS = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>


<html>
<head>
  <title> Sample TODO App </title>
  <style>
    div, body, html {
      background-color: #eee;
    }
    h1 {
      margin-left: 30px;
    }
    #sys-info, #task-add, #task-list {
      margin-top: 30px;
      margin-left: 30px;
    }
    #task-add input {
      height: 28px;
      font-size: 1.2em;
    }
    #task-add button {
      height: 28px;
      font-size: 1.2em;
    }
    #task-list ul {
      margin: 0px;
      padding: 0px;      
      border: 1px solid #333;
      max-width: 500px;
      background-color: #ffe;
      box-shadow: 10px 10px 18px 1px rgba(0,0,0,0.18);
      border-radius: 5px 5px;
    }
    #task-list li a {
      font-size: 1.25em;
      display: block;
    }
    #task-list li:hover {
      background-color: #fff;
      box-shadow: 10px 10px 18px 1px rgba(0,0,0,0.18);
    }
    #task-list li {
      display: block;
    }
    li.checked span {
      text-decoration: line-through;
    }
    #task-list li.checked i:before {
      color:green;
      content: "\2713";
      padding:0 6px 0 0;
    }
    #task-list li.unchecked i:before {
      content: "\2713";
      color:transparent;
      padding:0 6px 0 0;
    }
    #task-list ul li{
      list-style-type: none;
      font-size: 1em;
    }
    .toggle-form {
      display: inline;
      margin: 0;
      padding: 0;
    }
    .toggle-form button {
      color: inherit;
      border-style: none;
    }

  </style>
</head>
<body>
  <h1>Sample TODO app</h1>

  <div id="sys-info">
    The app was deployed successfully.
  </p>
  <p>
    Here's some system information:
    <ul>
      <li>Hostname serving your request: <?= gethostname() ?></li>
      <li>MySQL version: <?= $mysql_version ?></li>
    </ul>
  </div>

  <hr>

  <form method="POST">
    <div id="task-add">
      <input type="hidden" name="action" value="add">
      <input id="task-title" name="title" type="text" placeholder="Task Title">
      <button type="submit">Add</button>
    </div>
  </form>

  <div id="task-list">
    <ul>
      <?php foreach($ITEMS as $ITEM): ?>
      <li class=<?php if($ITEM["done"]): ?>"checked"<?php else: ?>"unchecked"<?php endif;?>>
        <form class="toggle-form" method="POST">
          <input type="hidden" name="action" value="toggle">
          <input type="hidden" name="id" value="<?=$ITEM["id"]?>">
          <button type="submit">
            <i></i>
          </button>
        </form>
        <span><?=htmlspecialchars($ITEM["title"])?></span>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</body>
</html>
