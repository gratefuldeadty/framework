<?php

/**
 * table.arena_players
 * arena_playersid (int) 3
 * arena_battleid (int) 3
 * teamid (enum) 1,2
 * userid (int) 3
 * 
 * arena_battles
 * battleid
 * time_formed
 */

class BattleArena
{
    public function __construct(Database $dbh)
    {
        $this->dbh = $dbh;
    }
    
    public function teamCheckCount($userid, $team)
    {
        $query = $this->dbh->prepare('SELECT `arena_playersid` FROM `arena_players`
            WHERE `userid` = ?
            AND `team` = ?');
        $query->execute(array($userid, $team));
        return ($query->rowCount() == 1) ? true : false;
    }
    
    public function createTeam($team, $teamName)
    {
        $query = $this->dbh->prepare('INSERT INTO `arena_battles` (`team`,`team_name`) VALUES (?,?)');
        $query->execute(array($team, $teamName));
    }
    
    
if ($_GET['action'] == 'lead')
{
    $i = 1;
    while ($i < 3)
    {
        if (!$arena->teamCheckCount($userid, $i))
        {
            echo 'You are already joined in this battle!';
            exit;
        }
    }
    if ($user['rage'] < 25)
    {
        echo 'You must have atleast 25 attacks to create a battle team.';
    }
    
    $team = (int)$_GET['team'];
    $oppositeTeam = ($team === 1) ? 2 : 1;
    
    if (!$arena->teamCheckCount($userid, $oppositeTeam))
    {
        $teamName = htmlentities($_POST['team_name']);
        $arena->createTeam($team, $teamName);
    }
    else
    {
        $arena->update
    }
}
    

else {
mysql_query("update battle_arena set team$team='$tname'");
mysql_query("update battle_arena set active='yes'");
}
mysql_query("insert into battle_team$team (leader, user) values('yes','$stat[id]')")or die("Could not add to team."); 
mysql_query("update users set attacks_left=attacks_left-25 where id=$stat[id]");
}
$action = $_GET['action'];
if ($action == join) {
$team1chkon = mysql_num_rows(mysql_query("select * from battle_team1 where user=$stat[id]"));
$team2chkon = mysql_num_rows(mysql_query("select * from battle_team2 where user=$stat[id]"));
if ($team1chkon >= 1 || $team2chkon >= 1) {
print "You are already on a team";
include("footer.php");
exit;
}
if ($stat[attacks_left] < 10) {
print "You must have at least 10 attacks to join a team.";
include("footer.php");
exit;
}

$team = $_GET['team'];
if ($team == 1) {
mysql_query("insert into battle_team1 (user) values('$stat[id]')")or die("Could not join."); 
print "Succesfully joined team 1.";
}
$team = $_GET['team'];
if ($team == 2) {
mysql_query("insert into battle_team2 (user) values('$stat[id]')")or die("Could not join."); 
print "Succesfully joined team 2.";
}
mysql_query("update users set attacks_left=attacks_left-10 where id=$stat[id]");

}
print "
<table width=80%>";
if ($arena[active] == yes) {
print "<center>This battle will launch in: <b>$arena[timeleft]</b> minutes</center>";
}
print "
<tr>
<td width=50% class=mail valign=top>
<img src=images/team1.gif><br>";
$t1check = mysql_num_rows(mysql_query("select * from battle_team1"));
if ($t1check <= 0) {
print "No team currently formed<p>";
$onteam1 = mysql_num_rows(mysql_query("select * from battle_team1 where user=$stat[id]"));
$onteam2 = mysql_num_rows(mysql_query("select * from battle_team2 where user=$stat[id]"));
if ($onteam1 <= 0 AND $onteam2 <= 0) {
print "
<b>Start A Team</b><br>
<form method=post action=battlearena.php?action=lead&team=1>
Team Name: <input type=text name=tname><br>
<input type=submit value=\"Start Team\">
</form>
";
}
}
else {
$teaminfo = mysql_fetch_array(mysql_query("select * from battle_arena"));
print "<div class=batitle>$teaminfo[team1]</div><p>";

	$t1a = mysql_query("select * from battle_team1");
	while ($t1 = mysql_fetch_array($t1a)) {
$team1memb = mysql_fetch_array(mysql_query("select * from users where id=$t1[user]"));
print "<a href=profile.php?view=$t1[user]>$team1memb[name]</a><br>";
}
$onteam1 = mysql_num_rows(mysql_query("select * from battle_team1 where user=$stat[id]"));
$onteam2 = mysql_num_rows(mysql_query("select * from battle_team2 where user=$stat[id]"));
if ($onteam1 <= 0 AND $onteam2 <= 0) {
print "
<br>
<form method=post action=battlearena.php?action=join&team=1>
<input type=submit value=\"Join Team 1\">
</form>
";
}
}
print "
</td>
<td width=50% class=mail valign=top>
<img src=images/team2.gif><br>";
$t2check = mysql_num_rows(mysql_query("select * from battle_team2"));
if ($t2check <= 0) {
print "No team currently formed<p>";
$onteam1 = mysql_num_rows(mysql_query("select * from battle_team1 where user=$stat[id]"));
$onteam2 = mysql_num_rows(mysql_query("select * from battle_team2 where user=$stat[id]"));
if ($onteam1 <= 0 AND $onteam2 <= 0) {
print "
<b>Start A Team</b><br>
<form method=post action=battlearena.php?action=lead&team=2>
Team Name: <input type=text name=tname><br>
<input type=submit value=\"Start Team\">
</form>
";
}
}
else {
$teaminfo = mysql_fetch_array(mysql_query("select * from battle_arena"));
print "<div class=batitle>$teaminfo[team2]</div><p>";
	$t21 = mysql_query("select * from battle_team2");
	while ($t2 = mysql_fetch_array($t21)) {
$team2memb = mysql_fetch_array(mysql_query("select * from users where id=$t2[user]"));
print "<a href=profile.php?view=$t2[user]>$team2memb[name]</a><br>";
}
$onteam1 = mysql_num_rows(mysql_query("select * from battle_team1 where user=$stat[id]"));
$onteam2 = mysql_num_rows(mysql_query("select * from battle_team2 where user=$stat[id]"));
if ($onteam1 <= 0 AND $onteam2 <= 0) {
print "
<br>
<form method=post action=battlearena.php?action=join&team=2>
<input type=submit value=\"Join Team 2\">
</form>
";
}
}
print "
</tr>
</table>
<table>
<tr>
<td>
<center>";
if ($arena[timeleft] <= 0 AND $arena[active] == yes) {
print "<h1><a href=dobattle.php>LAUNCH BATTLE!</a></h1>";}

print "
<b>
It will cost 10 attacks to join a team<br>
It will cost 25 attacks to create a team
</b>
</center>
</td>
</tr>
</table>
";

