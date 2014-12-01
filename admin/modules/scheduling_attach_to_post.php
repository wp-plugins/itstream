<!DOCTYPE html>
<html>
<head>
    <title>ItStream</title>
    <style>
        body{
            color: #444;
            font-family: "Open Sans",sans-serif;
            font-size: 13px;
            line-height: 24px;
        }
        iframe#fame {
            margin: 5px 10px;
        }
        table {
            margin: 5px;
            width:95%;
            border-collapse: collapse;
        }
        tr.border {
            border-bottom: 1px solid #DFDFDF;
        }
        td.thumb {
            width: 90px;
        }
        td.action {
            width: 50px;
            padding: 0 5px;
        }
        tr.details td.thumb {
            width: 130px;
        }
        tr.details td.insert {
            padding: 0 0 0 35px;
            text-align: right;
        }
        td.label {
            width: 150px;
        }
        td.label,
        td.field {
            padding: 2px 0;
        }
        td.insert{
            text-align: right;
        }
        .button {
            vertical-align: top;
            border-radius: 3px;
            border-style: solid;
            border-width: 1px;
            box-sizing: border-box;
            display: inline-block;
            font-size: 13px;
            height: 30px;
            line-height: 28px;
            margin: 0;
            padding: 0 12px 2px;
            text-decoration: none;
            white-space: nowrap;
            background: none repeat scroll 0 0 #2ea2cc;
            border-color: #0074a2;
            box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset, 0 1px 0 rgba(0, 0, 0, 0.15);
            color: #fff;
            text-decoration: none;
        }
        .button:hover {
            background: none repeat scroll 0 0 #1e8cbe;
            border-color: #0074a2;
            box-shadow: 0 1px 0 rgba(120, 200, 230, 0.6) inset;
        }
    </style>
</head>
<body>
<div id="attach_to_post_tabs">
    <form id="shortcodelive" action="" method="post">
        <table style="text-align: right">
            <tr>
                <td class="field">
                    Player&nbsp;&nbsp;
                    <select id="player_type" name="playertype" onchange="changePlayerType(this)">
                        <option value="live">Live</option>
                        <option value="palinsesto">Palinsesto</option>
                    </select>
                </td>
            </tr>
        </table>

        <div id="thePlayer">
            <iframe width="640" height="360" src="http://embed.itstream.tv/live.php?code=<?php echo $_REQUEST['id']; ?>&amp;autostart=false" frameborder="0" scrolling="no" id="fame"></iframe>
        </div>

        <table>
            <tr>
                <td class="label">Autoplay</td>
                <td class="field">
                    <input id="player_autoplay" type="checkbox" name="player[<?php echo $_REQUEST['id']; ?>][autoplay]" value="1" />
                </td>
            </tr>
            <tr>
                <td class="label">Disattiva audio</td>
                <td class="field">
                    <input id="player_mute" type="checkbox" name="player[<?php echo $_REQUEST['id']; ?>][mute]" value="1" />
                </td>
            </tr>
            <tr>
                <td class="label">Dimensioni player</td>
                <td class="field">
                    <select id="player_size" name="player[size]">
                        <option value="640">Standard (640x360)</option>
                        <option value="560">Grande (560x315)</option>
                        <option value="480">Medio (480x270)</option>
                        <option value="320">Piccolo (320x180)</option>
                    </select>
                </td>
            </tr>
        </table>

        <div style="text-align:right">
            <button id="submit-image-url" name="send" value="<?php echo $_REQUEST['id']; ?>" class="button" type="submit">Inserisci nell'articolo</button>
        </div>
    </form>
</div>
<script>
    document.getElementById("submit-image-url").onclick = function(){
        selectedOption = document.getElementById( 'player_type' );
        playerType = selectedOption.options[selectedOption.selectedIndex].value;

        autoplay = document.getElementById( 'player_autoplay' ).value;
        mute = document.getElementById( 'player_mute' );

        playerSize = document.getElementById( 'player_size' )
        size = playerSize.options[playerSize.selectedIndex].value;;

        var shortcode = '[istream_scheduling';
        if(playerType == 'live') {
            shortcode = '[istream_live';
        }

        shortcode += ' id="<?php echo $_REQUEST['id']; ?>"';

        if (size !== null && size !== '') {
            switch(size) {
                case '640':
                    var width = 640; var height=360;
                    break;
                case '560':
                    var width = 560; var height=315;
                    break;
                case '480':
                    var width = 480; var height=270;
                    break;
                case '320':
                    var width = 320; var height=180;
                    break;
            }
            shortcode += ' width="' + width + '" height="' + height + '"';
        }

        if (autoplay !== null && autoplay !== '') {
            shortcode += ' autostart="true"';
        }

        if (mute.checked) {
            shortcode += ' volume="0"';
        }


        cat = [];
        var x = document.getElementById("category");
        if(x) {
            for (var i=0; i<x.options.length; i++) {
                if( x.options[i].selected == true ) {
                    cat.push(x.options[i].value);
                }
            }

            if( cat.length ) {
                shortcode += ' category="';
                shortcode += cat.join() + '"';
            }
        }

        shortcode += ']';

        window.parent.tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, shortcode );
        top.tinymce.activeEditor.windowManager.close();
    };

    function changePlayerType(el) {
        selectedOption = el.options[el.selectedIndex].value;
        switch(selectedOption) {
            case 'live':
                src = 'live.php';
                break;
            default:
                src = 'palinsesto.php';
                break;
        }

        document.getElementById('thePlayer').innerHTML = '<iframe width="640" height="360" src="http://embed.itstream.tv/'+src+'?code=<?php echo $_REQUEST['id']; ?>&amp;autostart=false" frameborder="0" scrolling="no" id="fame"></iframe>';
    }
</script>
</body>
</html>