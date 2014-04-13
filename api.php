<?php

// this servlet really doesn't do much at all



mysql_connect('localhost', 'snapchess', 'snapchess');
mysql_select_db('snapchess');

const WAIT_INTERVAL = 10; // in seconds


$action = $_GET['action'];

if($action !== 'ticker'){

    session_start(); // we can't start the session for the ticker because that would make the sleep blocking

}

if($action === 'ticker'){

    // here we wait until we have accumulated enough votes

    $currentTime = time();
    $modulus = $currentTime % WAIT_INTERVAL;
    $remainingTime = WAIT_INTERVAL - $modulus;

    sleep($remainingTime);

    $newFEN = processVotes();

    // if(!$newFEN){ die(); }

    $response = []; // we will reply with the new FEN
    // $newFEN = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';

    if($newFEN){
        $response['fen'] = $newFEN;
    }else{
        $response['continue'] = true;
    }

    echo json_encode($response);

    die();

}else if($action === 'vote'){

    // here we let the current user vote for their FEM



    $session = session_id();
    $fen = $_GET['fen'];

    mysql_query('INSERT INTO `votes` SET `session` = "'.mysql_real_escape_string($session).'", `fen` = "'.mysql_real_escape_string($fen).'" ');

    die();

}

function processVotes(){

    // first, check if there's something that is currently not valid

    $currentTime = time();
    $fenQuery = mysql_query('SELECT * FROM `fen_history` WHERE `validThrough` > '.mysql_real_escape_string($currentTime).' ORDER BY `validThrough` DESC LIMIT 0,1 ');
    if(mysql_affected_rows() > 0){

        $fenResult = mysql_fetch_assoc($fenQuery);
        return $fenResult['fen'];

        // return true; // the newest stuff has already been done

    }

    $modulus = $currentTime % WAIT_INTERVAL;
    $rest = WAIT_INTERVAL - $modulus;
    $validThrough = $currentTime + $rest;

    // otherwise, let's select the newest FEN and make it active
    $voteQuery = mysql_query('SELECT * FROM `votes` WHERE `isActive` = 1 GROUP BY `fen` ORDER BY COUNT(`fen`) DESC ');
    if(mysql_affected_rows() < 1){

        // we need the latest id
        $fenIDQuery = mysql_query('SELECT * FROM `fen_history` ORDER BY `validThrough` DESC LIMIT 0,1 ');
        $fenIDResult = mysql_fetch_assoc($fenIDQuery);
        $fenID = $fenIDResult['id'];

        mysql_query('UPDATE `fen_history` SET `validThrough` = '.mysql_real_escape_string($validThrough).' WHERE `id` = '.mysql_real_escape_string($fenID).' ');

        echo mysql_error();

        return false; // there are no votes

    }

    $mostVotedFEN = mysql_fetch_assoc($voteQuery);
    $fen = $mostVotedFEN['fen'];



    mysql_query('INSERT INTO `fen_history` SET `fen` = "'.mysql_real_escape_string($fen).'", `validThrough` = '.mysql_real_escape_string($validThrough).' ');
    mysql_query('UPDATE `votes` SET `isActive` = 0 ');

    return $fen;

}