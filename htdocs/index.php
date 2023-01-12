<?php
require_once "../config/settings.php";
date_default_timezone_set("America/Chicago");

?>
<html>

<head>

    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" type="text/css" href="css/fb.css" />

</head>

<body>
    <div class="code">
        <?php
        $limit = 5;

        $group_id = '106061659471133';
        $url1 = 'https://graph.facebook.com/' . $group_id;
        $des = @json_decode(file_get_contents($url1));

        echo '<pre>';
        print_r($des);
        echo '</pre>';

        $url2 = "https://graph.facebook.com/106061659471133/feed?access_token=" . FACEBOOK_ACCESS_TOKEN;
        $data = json_decode(file_get_contents($url2));
        ?>
    </div>

    <div id="content" class="child-cnt" role="main">
        <div class="entry">
            <div style="width:412px; float:right;">
                <ul class="tabs">
                    <li class="fb-tab"><a href="#tab1"><span>Facebook</span></a></li>
                </ul>
                <div class="tab_container">
                    <div id="tab1" class="tab_content">
                        <div class="fb_group">
                            <a href='http://facebook.com/profile.php?id=<?php echo $group_id;  ?>&ap=1'><?php echo is_null($des) ? "": $des->name; ?></a>
                        </div>
                        <div style="width:100%; margin: 5px">
                        </div>

                        <?php
                        $counter = 0;
                        if (!is_null($data)) {
                            foreach ($data->data as $d) {
                                if ($counter == $limit)
                                    break;
                        ?>

                                <div>
                                    <a href="http://facebook.com/profile.php?id=<?php echo $d->from->id; ?>" class="fb_photo">
                                        <img border="0" alt="<?php $d->from->name; ?>" src="https://graph.facebook.com/<?php echo $d->from->id; ?>/picture" />
                                    </a>
                                    <div class="fb_text">
                                        <span style="font-weight:bold; margin-top:10px;"><a href="http://facebook.com/profile.php?id=<?php echo $d->from->id; ?>"><?php $d->from->name; ?></a>
                                        </span>
                                        <br />
                                        <span style="color: #333333;">on <?php echo date('F j, Y H:i', strtotime($d->created_time)); ?></span>
                                        <br />
                                        <?php echo $d->message; ?>
                                        <br clear="all" />
                                    </div>
                                </div>
                        <?php
                                $counter++;
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>