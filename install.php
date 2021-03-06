<?php
// Load language
require_once __DIR__ . '/bb-content/lib/database.php';
require_once __DIR__ . '/bb-content/lib/language.php';

$lang = new Language('en_us');
function __($key, $value = null) {
    global $lang;
    return $lang->get($key, $value);
}

$errors = array();
$installed = false;
$env = (@$_GET['env'] == 'test') ? 'test' : 'prod';

if($_POST) {
    // get data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $language = $_POST['language'];
    $timezone = floatval($_POST['timezone']);

    if(strlen($username) < 4) {
        $errors[] = __('install.errors.username_too_short', array('min_chars' => 4));
    }

    if(strlen($password) < 4) {
        $errors[] = __('install.errors.password_too_short', array('min_chars' => 4));
    }

    // if data is valid, install
    if(count($errors) == 0) {
        $base_uri = explode('install.php', $_SERVER['REQUEST_URI']);
        $base_uri = $base_uri[0];

        $db = new Database($env);
        $data = array(
            'username' => $username,
            'password' => $password,
            'language' => $language,
            'timezone' => $timezone,
            'base_uri' => $base_uri,
            'admin_uri' => $base_uri . 'bb-admin/',
            'admin_theme' => 'default',
            'theme' => 'default',
        );
        $config = $db->table('config')->find(1);
        if(is_null($config)) {
            $db->table('config')->insert($data);
        } else {
            $db->table('config')->update(1, $data);
        }

        $installed = true;
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo __('install.title'); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.3.0/pure-min.css">
        <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" />

        <style type="text/css">
        body,
        .pure-g [class *= "pure-u"],
        .pure-g-r [class *= "pure-u"] {
            font-family: 'Lato', sans-serif;
        }
        
        div#header {
            background-color: #E4E7EC;
        }

        div#header div#header-menu {
            font-size: 13px;
            padding: 10px;
            text-align: right;
        }

        div#header div#header-menu ul {
            display: inline-block;
            padding: 0px;
            margin: 0px;
            list-style: none;
        }

        div#header div#header-menu ul li {
            display: inline-block;
            padding-left: 10px;
        }

        div#header div#header-menu ul li a {
            color: black;
        }

        div#header div#header-menu ul li a:hover {
            text-decoration: none;
        }

        div#header div#title h1 {
            font-weight: 300;
            padding-left: 25px;
        }

        div#container {
            width: 960px;
            margin: auto;
        }

        div#container div#content h1 {
            font-weight: 300;
        }

        div.alert {
            background: #1f8dd6;
            padding: 0.3em 1em;
            border-radius: 3px;
            color: #fff;
            margin-bottom: 10px;
        }

        div.alert h2 {
            font-weight: 300;
            font-size: 20px;
        }
        </style>
    </head>
    <body>

        <div class="pure-g" id="header">
            <div class="pure-u-1-2" id="title">
                <h1><?php echo __('install.title'); ?></h1>
            </div>

            <div class="pure-u-1-2">
                <div id="header-menu">
                    <span>Language:</span>
                    <ul>
                        <li><a href="?lang=en_us">English</a></li>
                        <li><a href="?lang=es_ar">Spanish</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="pure-g" id="container">
            <div class="pure-u-1">
                <div id="content">
                    <h1><?php echo __('install.settings'); ?></h1>

                    <?php if(count($errors) > 0): ?>
                    <div class="alert" id="error-message">
                    <h2><?php echo __('install.error_title'); ?></h2>
                        <ul>
                            <?php foreach($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <div class="alert">
                        <?php echo __('install.is_data_dir_writable'); ?>
                        <?php if(is_writable(__DIR__ . '/bb-content/data/')): ?>
                            <strong><?php echo __('install.yes'); ?></strong>
                        <?php else: ?>
                        <strong><?php echo __('install.no'); ?></strong> - <?php echo __('install.data_dir_error'); ?>
                        <?php endif; ?>
                    </div>

                    <div class="alert">
                        <?php echo __('install.is_uploads_dir_writable'); ?>
                        <?php if(is_writable(__DIR__ . '/bb-content/uploads/')): ?>
                            <strong><?php echo __('install.yes'); ?></strong>
                        <?php else: ?>
                        <strong><?php echo __('install.no'); ?></strong> - <?php echo __('install.uploads_dir_error'); ?>
                        <?php endif; ?>
                    </div>

                    <?php if($installed): ?>

                    <p id="success-message"><?php echo __('install.success', array('url' => 'bb-admin/')); ?></p>

                    <?php else: ?>

                    <form action="install.php?env=<?php echo $env; ?>" method="post" class="pure-form pure-form-aligned">
                        <fieldset>
                            <div class="pure-control-group">
                                <p><?php echo __('install.section_1_desc'); ?></p>
                            </div>

                            <div class="pure-control-group">
                                <label for="username"><?php echo __('install.username'); ?></label>
                                <input class="pure-input-1-2" id="username" name="username" type="text" placeholder="<?php echo __('install.username'); ?>">
                            </div>

                            <div class="pure-control-group">
                                <label for="password"><?php echo __('install.password'); ?></label>
                                <input class="pure-input-1-2" id="password" name="password" type="password" placeholder="<?php echo __('install.password'); ?>">
                            </div>

                            <div class="pure-control-group">
                                <p><?php echo __('install.section_2_desc'); ?></p>
                            </div>

                            <div class="pure-control-group">
                                <label for="language"><?php echo __('install.language'); ?></label>
                                <select id="language" name="language" class="pure-input-1-2">
                                    <option value="en_us">English</option>
                                    <option value="es_ar">Español</option>
                                </select>
                            </div>

                            <div class="pure-control-group">
                                <label for="timezone"><?php echo __('install.timezone'); ?></label>
                                <select name="timezone" id="timezone">
                                    <option value="-12.0">(GMT -12:00) Eniwetok, Kwajalein</option>
                                    <option value="-11.0">(GMT -11:00) Midway Island, Samoa</option>
                                    <option value="-10.0">(GMT -10:00) Hawaii</option>
                                    <option value="-9.0">(GMT -9:00) Alaska</option>
                                    <option value="-8.0">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
                                    <option value="-7.0">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
                                    <option value="-6.0">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
                                    <option value="-5.0">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
                                    <option value="-4.0">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
                                    <option value="-3.5">(GMT -3:30) Newfoundland</option>
                                    <option value="-3.0">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
                                    <option value="-2.0">(GMT -2:00) Mid-Atlantic</option>
                                    <option value="-1.0">(GMT -1:00 hour) Azores, Cape Verde Islands</option>
                                    <option value="0.0">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
                                    <option value="1.0">(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
                                    <option value="2.0">(GMT +2:00) Kaliningrad, South Africa</option>
                                    <option value="3.0">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
                                    <option value="3.5">(GMT +3:30) Tehran</option>
                                    <option value="4.0">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
                                    <option value="4.5">(GMT +4:30) Kabul</option>
                                    <option value="5.0">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                                    <option value="5.5">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
                                    <option value="5.75">(GMT +5:45) Kathmandu</option>
                                    <option value="6.0">(GMT +6:00) Almaty, Dhaka, Colombo</option>
                                    <option value="7.0">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
                                    <option value="8.0">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
                                    <option value="9.0">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                                    <option value="9.5">(GMT +9:30) Adelaide, Darwin</option>
                                    <option value="10.0">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
                                    <option value="11.0">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
                                    <option value="12.0">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
                                </select>
                            </div>

                            <div class="pure-controls">
                                <button id="btn-install" type="submit" class="pure-button pure-button-primary">
                                    <i class="fa fa-wrench"></i>
                                    <?php echo __('install.btn_install'); ?>
                                </button>
                            </div>
                        </fieldset>
                    </form>

                    <?php endif; ?>
                </div>
            </div>
        </div>
        </body>
        </html>
