<?php

// this servlet really doesn't do much at all

date_default_timezone_set('CET');

mysql_connect('localhost', 'snapchess', 'snapchess');
mysql_select_db('snapchess');

const WAIT_INTERVAL = 10; // in seconds


$action = $_GET['action'];

if($action !== 'ticker'){

    session_start(); // we can't start the session for the ticker because that would make the sleep blocking

}

if($action === 'ping'){

    $uniqueID = $_GET['uniqueID'];
    $school = $_GET['school'];

    $firstUserCount = $_GET['userCount'];
    $firstTeamMateCount = $_GET['teamMateCount'];

    // let's see if the user is added

    mysql_query('SELECT * FROM `users` WHERE `uniqueID` = "'.mysql_real_escape_string($uniqueID).'" ');
    if(mysql_affected_rows() < 1){

        mysql_query('INSERT INTO `users` SET `uniqueID` = "'.mysql_real_escape_string($uniqueID).'", `lastPing` = "'.mysql_real_escape_string(date('Y-m-d H:i:s')).'" ');

    }

    mysql_query('UPDATE `users` SET `school` = "'.mysql_real_escape_string($school).'" WHERE `uniqueID` = "'.mysql_real_escape_string($uniqueID).'" ');





    ignore_user_abort(true); // we will handle the abort ourselves

    define('UPDATE_CHECK_INTERVAL', 250 * 1000); // we will check that every 0.25 seconds
    define('CONNECTION_ABORTION_CHECK_INTERVAL', 5 * 1000 * 1000); // we will check that every 5 seconds

    // that way, less data is sent


    $timeSinceLastConnectionAbortionCheck = 0;

    while(true){

        // $updateNecessary = $token->needsHeartbeatPush();

        // let's delete the ones that are no longer present
        mysql_query('DELETE FROM `users` WHERE `lastPing` < "'.mysql_real_escape_string(date('Y-m-d H:i:s', time() - CONNECTION_ABORTION_CHECK_INTERVAL / 1000000)).'" ');

        // let's count the users
        mysql_query('SELECT * FROM `users` ');
        $userCount = mysql_affected_rows();

        mysql_query('SELECT * FROM `users` WHERE `school` = "'.mysql_real_escape_string($school).'" ');
        $teamMateCount = mysql_affected_rows();

        $updateNecessary = (($userCount != $firstUserCount) || ($teamMateCount != $firstTeamMateCount));

        if($updateNecessary){

            $newData = [];
            $newData['userCount'] = $userCount;
            $newData['teamMateCount'] = $teamMateCount;
            echo json_encode($newData);

            ob_flush();
            flush();

            if(connection_aborted()){ die(); } // only works if doing output beforehand, it needs to verify the socket connectivity

            die(); // since socket stuff does not work, we will just have the client reconnect every time there is something new

        }



        // we don't wanna check the connection and send some chunk of data every 0.25 seconds, so we do it every 5 seconds

        if($timeSinceLastConnectionAbortionCheck >= CONNECTION_ABORTION_CHECK_INTERVAL){

            $timeSinceLastConnectionAbortionCheck = 0;

            // trying to do output so the next check works
            echo ' ';
            ob_flush();
            flush();

            if(connection_aborted()){ die(); } // only works if doing output beforehand, it needs to verify the socket connectivity

            mysql_query('UPDATE `users` SET `lastPing` = "'.mysql_real_escape_string(date('Y-m-d H:i:s')).'" WHERE `uniqueID` = "'.mysql_real_escape_string($uniqueID).'" ');

        }



        // and now we sleep

        usleep(UPDATE_CHECK_INTERVAL);

        $timeSinceLastConnectionAbortionCheck += UPDATE_CHECK_INTERVAL;

    }

}else if($action === 'ticker'){

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