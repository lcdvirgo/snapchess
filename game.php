<?php

mysql_connect('localhost', 'root');
mysql_select_db('snapchess');

$currentTime = time();
$fenQuery = mysql_query('SELECT * FROM `fen_history` WHERE `validThrough` > '.mysql_real_escape_string($currentTime).' ORDER BY `validThrough` DESC LIMIT 0,1');
$fen = 'start';
if(mysql_affected_rows() > 0){

    $fenResult = mysql_fetch_assoc($fenQuery);
    $fen = $fenResult['fen'];

}

?>
<!DOCTYPE html>
<html>

    <head>

        <title>Snapchess</title>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script type="text/javascript" src="apis/chessboard/js/chessboard-0.3.0.min.js"></script>
        <script type="text/javascript" src="apis/chessboard/js/chess.min.js"></script>
        <link rel="stylesheet" href="apis/chessboard/css/chessboard-0.3.0.min.css"/>

        <noscript>
            <meta http-equiv="refresh" content="0;URL=index.html" />
        </noscript>

        <script type="text/javascript">

            SERVER_API_ENDPOINT = 'api.php?';

            var school = sessionStorage['school'];
            if(!school){
                location.href = 'index.html';
            }

            var team;
            if(school == 'ucla'){
                team = 'w';
            }else if(school == 'usc'){
                team = 'b';
            }

            $(document).ready(function () {

                // alert(JSON.stringify(sessionStorage));

                var waitingForServer = false;

                var board,
                        game = new Chess(),
                        statusEl = $('#status'),
                        fenEl = $('#fen'),
                        pgnEl = $('#pgn');

                // do not pick up pieces if the game is over
                // only pick up pieces for the side to move
                var onDragStart = function (source, piece, position, orientation) {

                    if(waitingForServer){ return false; } // the player has just played their move

                    // alert(game.turn());

                    if (game.game_over() === true ||
                            (game.turn() === 'w' && piece.search(/^b/) !== -1) ||
                            (game.turn() === 'b' && piece.search(/^w/) !== -1)) {
                        return false;
                    }

                    if(game.turn() !== team){
                        return false; // we are not the team that is supposed to play this piece
                    }

                };

                var onDrop = function (source, target) {
                    // see if the move is legal
                    var move = game.move({
                        from: source,
                        to: target,
                        promotion: 'q' // NOTE: always promote to a queen for example simplicity
                    });

                    // illegal move
                    if (move === null) return 'snapback';

                    updateStatus();
                    // voteAndWait();

                };

    // update the board position after the piece snap
    // for castling, en passant, pawn promotion
                var onSnapEnd = function () {
                    board.position(game.fen());
                };

                var updateStatus = function () {
                    var status = '';

                    var moveColor = 'White';
                    if (game.turn() === 'b') {
                        moveColor = 'Black';
                    }

                    // checkmate?
                    if (game.in_checkmate() === true) {
                        status = 'Game over, ' + moveColor + ' is in checkmate.';
                    }

                    // draw?
                    else if (game.in_draw() === true) {
                        status = 'Game over, drawn position';
                    }

                    // game still on
                    else {
                        status = moveColor + ' to move';

                        // check?
                        if (game.in_check() === true) {
                            status += ', ' + moveColor + ' is in check';
                        }
                    }

                    statusEl.html(status);
                    fenEl.html(game.fen());
                    pgnEl.html(game.pgn());



                };

                var voteAndWait = function(){

                    // the person has just moved their piece. Now, we need to send the movement's vote to the server, and make the game uneditable

                    waitingForServer = true;

                    var currentFEN = game.fen();
                    var path = SERVER_API_ENDPOINT + 'action=vote&fen='+encodeURIComponent(currentFEN);

                    $.ajax({url: path, async: true, success:function(data){

                        // alert('the FEN has just been voted for!');

                    }});

                }

                var listenForServerFENChanges = function(){

                    var path = SERVER_API_ENDPOINT + 'action=ticker';

                    $.ajax({url: path, async: true, success:function(data){

                        var dataJSON;

                        try{
                            dataJSON = JSON.parse(data);
                        }catch(e){
                            return; // this didn't work out
                        }

                        if(!dataJSON){ return; } // JSON conversion has not worked

                        waitingForServer = false;

                        var newFEN = dataJSON['fen'];

                        if(newFEN){

                            game = new Chess(newFEN);
                            board.position(newFEN);
                            // board

                            // waitingForServer = false;

                        }

                        listenForServerFENChanges(); // now we wait for the next time the server decides to change stuff

                    }});

                }

                var cfg = {
                    draggable: true,
                    position: '<?php echo $fen; ?>',
                    onDragStart: onDragStart,
                    onDrop: onDrop,
                    onSnapEnd: onSnapEnd
                };
                board = new ChessBoard('board', cfg);

                updateStatus();
                listenForServerFENChanges();




                $('#load_board_button').click(function(){

                    var fen = 'rnbqkbnr/ppp1pppp/8/3p4/8/5P2/PPPPP1PP/RNBQKBNR w KQkq d6 0 2';

                    // game.load(fen);
                    game = new Chess(fen);
                    board.position(fen);

                });




            });

        </script>

    </head>

    <body>

        <div id="board" style="width: 400px;"></div>

        <p>Status: <span id="status"></span></p>

        <p>FEN: <span id="fen"></span></p>

        <p>PGN: <span id="pgn"></span></p>

        <input type="button" value="Test load board" id="load_board_button" />

    </body>

</html>