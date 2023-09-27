<?php

use App\Lib\GoogleAuthenticator;
use App\Lib\SendSms;
use App\Models\BvLog;
use App\Models\EmailTemplate;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\Transaction;
use App\Models\GeneralSetting;
use App\Models\SmsTemplate;
use App\Models\EmailLog;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserExtra;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

function sidebarVariation(){

    /// for sidebar
    $variation['sidebar'] = 'bg_img';

    //for selector
    $variation['selector'] = 'capsule--rounded';

    //for overlay
    $variation['overlay'] = 'overlay--indigo';

    //Opacity
    $variation['opacity'] = 'overlay--opacity-8'; // 1-10

    return $variation;

}

function systemDetails()
{
    $system['name'] = 'binaryecom';
    $system['version'] = '1.1';
    return $system;
}

function getLatestVersion()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/version/' . systemDetails()['name'];
    $result = curlPostContent($url, $param);
    if ($result) {
        return $result;
    } else {
        return null;
    }
}


function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}


function shortDescription($string, $length = 120)
{
    return Illuminate\Support\Str::limit($string, $length);
}


function shortCodeReplacer($shortCode, $replace_with, $template_string)
{
    return str_replace($shortCode, $replace_with, $template_string);
}


function verificationCode($length)
{
    if ($length == 0) return 0;
    $min = pow(10, $length - 1);
    $max = 0;
    while ($length > 0 && $length--) {
        $max = ($max * 10) + 9;
    }
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function uploadImage($file, $location, $size = null, $old = null, $thumb = null)
{
    $path = makeDirectory($location);
    if (!$path) throw new Exception('File could not been created.');

    if ($old) {
        removeFile($location . '/' . $old);
        removeFile($location . '/thumb_' . $old);
    }
    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $image = Image::make($file);
    if ($size) {
        $size = explode('x', strtolower($size));
        $image->resize($size[0], $size[1]);
    }
    $image->save($location . '/' . $filename);

    if ($thumb) {
        $thumb = explode('x', $thumb);
        Image::make($file)->resize($thumb[0], $thumb[1])->save($location . '/thumb_' . $filename);
    }

    return $filename;
}

function uploadFile($file, $location, $size = null, $old = null){
    $path = makeDirectory($location);
    if (!$path) throw new Exception('File could not been created.');

    if ($old) {
        removeFile($location . '/' . $old);
    }

    $filename = uniqid() . time() . '.' . $file->getClientOriginalExtension();
    $file->move($location,$filename);
    return $filename;
}

function makeDirectory($path)
{
    if (file_exists($path)) return true;
    return mkdir($path, 0755, true);
}


function removeFile($path)
{
    return file_exists($path) && is_file($path) ? @unlink($path) : false;
}


function activeTemplate($asset = false)
{
    $general = GeneralSetting::first(['active_template']);
    $template = $general->active_template;
    $sess = session()->get('template');
    if (trim($sess)) {
        $template = $sess;
    }
    if ($asset) return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $general = GeneralSetting::first(['active_template']);
    $template = $general->active_template;
    $sess = session()->get('template');
    if (trim($sess)) {
        $template = $sess;
    }
    return $template;
}


function loadReCaptcha()
{
    $reCaptcha = Extension::where('act', 'google-recaptcha2')->where('status', 1)->first();
    return $reCaptcha ? $reCaptcha->generateScript() : '';
}


function loadAnalytics()
{
    $analytics = Extension::where('act', 'google-analytics')->where('status', 1)->first();
    return $analytics ? $analytics->generateScript() : '';
}

function loadTawkto()
{
    $tawkto = Extension::where('act', 'tawk-chat')->where('status', 1)->first();
    return $tawkto ? $tawkto->generateScript() : '';
}


function loadFbComment()
{
    $comment = Extension::where('act', 'fb-comment')->where('status',1)->first();
    return  $comment ? $comment->generateScript() : '';
}

function loadCustomCaptcha($height = 46, $width = '300px', $bgcolor = '#003', $textcolor = '#abc')
{
    $textcolor = '#'.GeneralSetting::first()->base_color;
    $captcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
    if (!$captcha) {
        return 0;
    }
    $code = rand(100000, 999999);
    $char = str_split($code);
    $ret = '<link href="https://fonts.googleapis.com/css?family=Henny+Penny&display=swap" rel="stylesheet">';
    $ret .= '<div style="height: ' . $height . 'px; line-height: ' . $height . 'px; width:' . $width . '; text-align: center; background-color: ' . $bgcolor . '; color: ' . $textcolor . '; font-size: ' . ($height - 20) . 'px; font-weight: bold; letter-spacing: 20px; font-family: \'Henny Penny\', cursive;  -webkit-user-select: none; -moz-user-select: none;-ms-user-select: none;user-select: none;  display: flex; justify-content: center;">';
    foreach ($char as $value) {
        $ret .= '<span style="    float:left;     -webkit-transform: rotate(' . rand(-60, 60) . 'deg);">' . $value . '</span>';
    }
    $ret .= '</div>';
    $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
    $ret .= '<input type="hidden" name="captcha_secret" value="' . $captchaSecret . '">';
    return $ret;
}


function captchaVerify($code, $secret)
{
    $captcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
    $captchaSecret = hash_hmac('sha256', $code, $captcha->shortcode->random_key->value);
    if ($captchaSecret == $secret) {
        return true;
    }
    return false;
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function getAmount($amount, $length = 2)
{
    $amount = round($amount, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false){
    $separator = '';
    if($separate){
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if($exceptZeros){
    $exp = explode('.', $printAmount);
        if($exp[1]*1 == 0){
            $printAmount = $exp[0];
        }
    }
    return $printAmount;
}


function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{

    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}
function curlContent($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function curlPostContent($url, $arr = null)
{
    if ($arr) {
        $params = http_build_query($arr);
    } else {
        $params = '';
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}


function inputTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}


function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}


function str_limit($title = null, $length = 10)
{
    return \Illuminate\Support\Str::limit($title, $length);
}
function getIpInfo()
{
    $ip = $_SERVER["REMOTE_ADDR"];

    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)){
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }

    $xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);


    $country = @$xml->geoplugin_countryName;
    $city = @$xml->geoplugin_city;
    $area = @$xml->geoplugin_areaCode;
    $code = @$xml->geoplugin_countryCode;
    $long = @$xml->geoplugin_longitude;
    $lat = @$xml->geoplugin_latitude;

    $data['country'] = $country;
    $data['city'] = $city;
    $data['area'] = $area;
    $data['code'] = $code;
    $data['long'] = $long;
    $data['lat'] = $lat;
    $data['ip'] = $ip;
    $data['time'] = date('d-m-Y h:i:s A');


    return $data;
}
function osBrowser(){
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $osPlatform = "Unknown OS Platform";
    $osArray = array(
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );
    foreach ($osArray as $regex => $value) {
        if (preg_match($regex, $userAgent)) {
            $osPlatform = $value;
        }
    }
    $browser = "Unknown Browser";
    $browserArray = array(
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    );
    foreach ($browserArray as $regex => $value) {
        if (preg_match($regex, $userAgent)) {
            $browser = $value;
        }
    }

    $data['os_platform'] = $osPlatform;
    $data['browser'] = $browser;

    return $data;
}

function siteName()
{
    $general = GeneralSetting::first();
    $sitname = str_word_count($general->sitename);
    $sitnameArr = explode(' ', $general->sitename);
    if ($sitname > 1) {
        $title = "<span>$sitnameArr[0] </span> " . str_replace($sitnameArr[0], '', $general->sitename);
    } else {
        $title = "<span>$general->sitename</span>";
    }

    return $title;
}

function getTemplates()
{
    $param['purchasecode'] = env("PURCHASECODE");
    $param['website'] = @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . env("APP_URL");
    $url = 'https://license.viserlab.com/updates/templates/' . systemDetails()['name'];
    $result = curlPostContent($url, $param);
    if ($result) {
        return $result;
    } else {
        return null;
    }
}


function getPageSections($arr = false)
{

    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}


function getImage($image,$size = null)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image',$size);
    }
    return asset('assets/images/default.png');
}

function notify($user, $type, $shortCodes = null)
{

    sendEmail($user, $type, $shortCodes);
    sendSms($user, $type, $shortCodes);
}



function sendSms($user, $type, $shortCodes = [])
{
    $general = GeneralSetting::first();
    $smsTemplate = SmsTemplate::where('act', $type)->where('sms_status', 1)->first();
    $gateway = $general->sms_config->name;
    $sendSms = new SendSms;
    if ($general->sn == 1 && $smsTemplate) {
        $template = $smsTemplate->sms_body;
        foreach ($shortCodes as $code => $value) {
            $template = shortCodeReplacer('{{' . $code . '}}', $value, $template);
        }
        $message = shortCodeReplacer("{{message}}", $template, $general->sms_api);
        $message = shortCodeReplacer("{{name}}", $user->username, $message);
        $sendSms->$gateway($user->mobile,$general->sitename,$message,$general->sms_config);
    }
}

function sendEmail($user, $type = null, $shortCodes = [])
{
    $general = GeneralSetting::first();

    $emailTemplate = EmailTemplate::where('act', $type)->where('email_status', 1)->first();
    if ($general->en != 1 || !$emailTemplate) {
        return;
    }


    $message = shortCodeReplacer("{{fullname}}", $user->fullname, $general->email_template);
    $message = shortCodeReplacer("{{username}}", $user->username, $message);
    $message = shortCodeReplacer("{{message}}", $emailTemplate->email_body, $message);

    if (empty($message)) {
        $message = $emailTemplate->email_body;
    }

    foreach ($shortCodes as $code => $value) {
        $message = shortCodeReplacer('{{' . $code . '}}', $value, $message);
    }

    $config = $general->mail_config;

    $emailLog = new EmailLog();
    $emailLog->user_id = $user->id;
    $emailLog->mail_sender = $config->name;
    $emailLog->email_from = $general->sitename.' '.$general->email_from;
    $emailLog->email_to = $user->email;
    $emailLog->subject = $emailTemplate->subj;
    $emailLog->message = $message;
    $emailLog->save();


    if ($config->name == 'php') {
        sendPhpMail($user->email, $user->username,$emailTemplate->subj, $message, $general);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $user->email, $user->username, $emailTemplate->subj, $message,$general);
    }
}


function sendPhpMail($receiver_email, $receiver_name, $subject, $message,$general)
{
    $headers = "From: $general->sitename <$general->email_from> \r\n";
    $headers .= "Reply-To: $general->sitename <$general->email_from> \r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    @mail($receiver_email, $subject, $message, $headers);
}


function sendSmtpMail($config, $receiver_email, $receiver_name, $subject, $message,$general)
{
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = $config->host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $config->username;
        $mail->Password   = $config->password;
        if ($config->enc == 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }else{
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->Port       = $config->port;
        $mail->CharSet = 'UTF-8';
        //Recipients
        $mail->setFrom($general->email_from, $general->sitename);
        $mail->addAddress($receiver_email, $receiver_name);
        $mail->addReplyTo($general->email_from, $general->sitename);
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->send();
    } catch (Exception $e) {
        throw new Exception($e);
    }
}


function sendSendGridMail($config, $receiver_email, $receiver_name, $subject, $message,$general)
{
    $sendgridMail = new \SendGrid\Mail\Mail();
    $sendgridMail->setFrom($general->email_from, $general->sitename);
    $sendgridMail->setSubject($subject);
    $sendgridMail->addTo($receiver_email, $receiver_name);
    $sendgridMail->addContent("text/html", $message);
    $sendgrid = new \SendGrid($config->appkey);
    try {
        $response = $sendgrid->send($sendgridMail);
    } catch (Exception $e) {
        throw new Exception($e);
    }
}


function sendMailjetMail($config, $receiver_email, $receiver_name, $subject, $message,$general)
{
    $mj = new \Mailjet\Client($config->public_key, $config->secret_key, true, ['version' => 'v3.1']);
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => $general->email_from,
                    'Name' => $general->sitename,
                ],
                'To' => [
                    [
                        'Email' => $receiver_email,
                        'Name' => $receiver_name,
                    ]
                ],
                'Subject' => $subject,
                'TextPart' => "",
                'HTMLPart' => $message,
            ]
        ]
    ];
    $response = $mj->post(\Mailjet\Resources::$Email, ['body' => $body]);
}


function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data, $design = 'admin.partials.paginate'){
    return $data->appends(request()->all())->links($design);
}


function menuActive($routeName, $type = null)
{
    if ($type == 3) {
        $class = 'side-menu--open';
    } elseif ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
    }
    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routeName)) {
        return $class;
    }
}


function imagePath()
{
    $data['gateway'] = [
        'path' => 'assets/images/gateway',
        'size' => '800x800',
    ];
    $data['verify'] = [
        'withdraw'=>[
            'path'=>'assets/images/verify/withdraw'
        ],
        'deposit'=>[
            'path'=>'assets/images/verify/deposit'
        ]
    ];
    $data['image'] = [
        'default' => 'assets/images/default.png',
    ];
    $data['withdraw'] = [
        'method' => [
            'path' => 'assets/images/withdraw/method',
            'size' => '800x800',
        ]
    ];
    $data['ticket'] = [
        'path' => 'assets/support',
    ];
    $data['language'] = [
        'path' => 'assets/images/lang',
        'size' => '64x64'
    ];
    $data['logoIcon'] = [
        'path' => 'assets/images/logoIcon',
    ];
    $data['favicon'] = [
        'size' => '128x128',
    ];
    $data['extensions'] = [
        'path' => 'assets/images/extensions',
        'size' => '36x36',
    ];
    $data['seo'] = [
        'path' => 'assets/images/seo',
        'size' => '600x315'
    ];
    $data['profile'] = [
        'user'=> [
            'path'=>'assets/images/user/profile',
            'size'=>'350x300'
        ],
        'admin'=> [
            'path'=>'assets/admin/images/profile',
            'size'=>'400x400'
        ]
    ];
    $data['products'] = [
        'path' => 'assets/images/products',
        'size' => '450x500',
        'thumb' => '310x350'
    ];
    return $data;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}
function sendGeneralEmail($email, $subject, $message, $receiver_name = '')
{

    $general = GeneralSetting::first();


    if ($general->en != 1 || !$general->email_from) {
        return;
    }


    $message = shortCodeReplacer("{{message}}", $message, $general->email_template);
    $message = shortCodeReplacer("{{fullname}}", $receiver_name, $message);
    $message = shortCodeReplacer("{{username}}", $email, $message);

    $config = $general->mail_config;

    if ($config->name == 'php') {
        sendPhpMail($email, $receiver_name, $subject, $message, $general);
    } else if ($config->name == 'smtp') {
        sendSmtpMail($config, $email, $receiver_name, $subject, $message, $general);
    } else if ($config->name == 'sendgrid') {
        sendSendGridMail($config, $email, $receiver_name,$subject, $message,$general);
    } else if ($config->name == 'mailjet') {
        sendMailjetMail($config, $email, $receiver_name,$subject, $message, $general);
    }
}

function getContent($data_keys, $singleQuery = false, $limit = null,$orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $data_keys)->orderBy('id','desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if($orderById){
            $content = $article->where('data_keys', $data_keys)->orderBy('id')->get();
        }else{
            $content = $article->where('data_keys', $data_keys)->orderBy('id','desc')->get();
        }
    }
    return $content;
}


function gatewayRedirectUrl($type = false){
    if ($type) {
        return 'user.deposit.history';
    }else{
        return 'user.deposit';
    }
}

function verifyG2fa($user,$code,$secret = null)
{
    $ga = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $ga->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
}


function urlPath($routeName,$routeParam=null){
    if($routeParam == null){
        $url = route($routeName);
    } else {
        $url = route($routeName,$routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath,'',$url);
    return $path;
}



/* MLM FUNCTION  */

function getUserById($id)
{
    return User::find($id);
}
function getUserByIdD($id)
{
    $user = User::find($id);
    return $user->username;
}

function createBVLog($user_id, $lr, $amount, $details){
    $bvlog = new BvLog();
    $bvlog->user_id = $user_id;
    $bvlog->position = $lr;
    $bvlog->amount = $amount;
    $bvlog->trx_type = '-';
    $bvlog->details = $details;
    $bvlog->save();
}


function mlmWidth()
{
    return 2;
}

function mlmPositions()
{
    return array(
        '1' => 'Left',
        '2' => 'Right',
    );
}

function getPosition($parentid, $position)
{
    $childid = getTreeChildId($parentid, $position);

    if ($childid != "-1") {
        $id = $childid;
    } else {
        $id = $parentid;
    }
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $nextchildid = getTreeChildId($id, $position);
            if ($nextchildid == "-1") {
                break;
            } else {
                $id = $nextchildid;
            }
        } else break;
    }

    $res['pos_id'] = $id;
    $res['position'] = $position;
    return $res;
}

function getTreeChildId($parentid, $position)
{
    $cou = User::where('pos_id', $parentid)->where('position', $position)->count();
    $cid = User::where('pos_id', $parentid)->where('position', $position)->first();
    if ($cou == 1) {
        return $cid->id;
    } else {
        return -1;
    }
}

function isUserExists($id)
{
    $user = User::find($id);
    if ($user) {
        return true;
    } else {
        return false;
    }
}

function getPositionId($id)
{
    $user = User::find($id);
    if ($user) {
        return $user->pos_id;
    } else {
        return 0;
    }
}

function getPositionLocation($id)
{
    $user = User::find($id);
    if ($user) {
        return $user->position;
    } else {
        return 0;
    }
}

function updateFreeCount($id)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
            $position = getPositionLocation($id);

            $extra = UserExtra::where('user_id', $posid)->first();

            if ($position == 1) {
                $extra->free_left += 1;
            } else {
                $extra->free_right += 1;
            }
            $extra->save();

            $id = $posid;

        } else {
            break;
        }
    }

}

function updatePaidCount($id)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
            $position = getPositionLocation($id);

            $extra = UserExtra::where('user_id', $posid)->first();

            if ($position == 1) {
                $extra->free_left -= 1;
                $extra->paid_left += 1;
            } else {
                $extra->free_right -= 1;
                $extra->paid_right += 1;
            }
            $extra->save();
            $id = $posid;
        } else {
            break;
        }
    }

}
function updateDspPaidCount($id)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
            $position = getPositionLocation($id);

            $extra = UserExtra::where('user_id', $posid)->first();

            if ($position == 1) {
                // $extra->free_left -= 1;
                $extra->paid_left += 1;
            } else {
                // $extra->free_right -= 1;
                $extra->paid_right += 1;
            }
            $extra->save();
            $id = $posid;
        } else {
            break;
        }
    }

}


function updateBV($id, $bv, $details)
{
    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }
            $posUser = User::find($posid);
            if ($posUser->plan_id != 0) {
                $position = getPositionLocation($id);
                $extra = UserExtra::where('user_id', $posid)->first();
                $bvlog = new BvLog();
                $bvlog->user_id = $posid;

                if ($position == 1) {
                    $extra->bv_left += $bv;
                    $bvlog->position = '1';
                } else {
                    $extra->bv_right += $bv;
                    $bvlog->position = '2';
                }
                $extra->save();
                $bvlog->amount = $bv;
                $bvlog->trx_type = '+';
                $bvlog->details = $details;
                $bvlog->save();
            }
            $id = $posid;
        } else {
            break;
        }
    }

}


function treeComission($id, $amount, $details)
{
    $fromUser = User::find($id);

    while ($id != "" || $id != "0") {
        if (isUserExists($id)) {
            $posid = getPositionId($id);
            if ($posid == "0") {
                break;
            }

            $posUser = User::find($posid);
            if ($posUser->plan_id != 0) {

                $posUser->balance  += $amount;
                $posUser->total_binary_com += $amount;
                $posUser->save();

               $posUser->transactions()->create([
                    'amount' => $amount,
                    'charge' => 0,
                    'trx_type' => '+',
                    'details' => $details,
                    'remark' => 'binary_commission',
                    'trx' => getTrx(),
                    'post_balance' => getAmount($posUser->balance),
                ]);


            }
            $id = $posid;
        } else {
            break;
        }
    }

}

function referralComission($user_id, $details)
{

    $user = User::find($user_id);
    $refer = User::find($user->ref_id);
    if ($refer) {
        $plan = Plan::find($refer->plan_id);
        if ($plan) {
            $ref_com_74per = ($plan->ref_com * 74)/100;
            $ref_com_26per = ($plan->ref_com * 26)/100;

            $amount = $plan->ref_com;
            $refer->balance += $ref_com_74per;
            $refer->epin_credit += $ref_com_26per;
            $refer->total_ref_com += $amount;
            $refer->save();

            $trx = $refer->transactions()->create([
                'amount' => $amount,
                'charge' => 0,
                'trx_type' => '+',
                'details' => $details,
                'remark' => 'referral_commission',
                'trx' => getTrx(),
                'post_balance' => getAmount($refer->balance),

            ]);

            $gnl = GeneralSetting::first();

            notify($refer, 'referral_commission', [
                'trx' => $trx->trx,
                'amount' => getAmount($amount),
                'currency' => $gnl->cur_text,
                'username' => $user->username,
                'post_balance' => getAmount($refer->balance),
            ]);

        }

    }


}

/*
===============TREEE===============
*/

function getPositionUser($id, $position)
{
    return User::where('pos_id', $id)->where('position', $position)->first();
}

function showTreePage($id)
{
    $res = array_fill_keys(array('b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o'), null);
    $res['a'] = User::find($id);

    $res['b'] = getPositionUser($id, 1);
    if ($res['b']) {
        $res['d'] = getPositionUser($res['b']->id, 1);
        $res['e'] = getPositionUser($res['b']->id, 2);
    }
    if ($res['d']) {
        $res['h'] = getPositionUser($res['d']->id, 1);
        $res['i'] = getPositionUser($res['d']->id, 2);
    }
    if ($res['e']) {
        $res['j'] = getPositionUser($res['e']->id, 1);
        $res['k'] = getPositionUser($res['e']->id, 2);
    }
    $res['c'] = getPositionUser($id, 2);
    if ($res['c']) {
        $res['f'] = getPositionUser($res['c']->id, 1);
        $res['g'] = getPositionUser($res['c']->id, 2);
    }
    if ($res['f']) {
        $res['l'] = getPositionUser($res['f']->id, 1);
        $res['m'] = getPositionUser($res['f']->id, 2);
    }
    if ($res['g']) {
        $res['n'] = getPositionUser($res['g']->id, 1);
        $res['o'] = getPositionUser($res['g']->id, 2);
    }
    return $res;
}


function showSingleUserinTree($user)
{
    $res = '';
    if ($user) {
        if ($user->plan_id == 0) {
            $userType = "free-user";
            $stShow = "Free";
            $planName = '';
        } else {
            $userType = "paid-user";
            $stShow = "Paid";
            $planName = $user->plan->name;
        }

        $img = getImage('assets/images/user/profile/'. $user->image, '120x120');
        $refby = getUserById($user->ref_id)->fullname ?? '';
        $refbyUsername = getUserById($user->ref_id)->username ?? '';
        if (auth()->guard('admin')->user()) {
            $hisTree = route('admin.users.other.tree', $user->username);
        } else {
            $hisTree = route('user.other.tree', $user->username);
        }
        $dsp_username = $user->dsp_username;
        $username = $user->username;
        $cur_bal = $user->balance;
        $epin_credit = $user->epin_credit;

        $extraData = " data-name=\"$user->fullname\"";
        $extraData .= " data-username=\"$username\"";
        $extraData .= " data-cur_bal=\"$cur_bal\"";
        $extraData .= " data-epin_credit=\"$epin_credit\"";
        $extraData .= " data-dspun=\"$dsp_username\"";
        $extraData .= " data-treeurl=\"$hisTree\"";
        $extraData .= " data-status=\"$stShow\"";
        $extraData .= " data-plan=\"$planName\"";
        $extraData .= " data-image=\"$img\"";
        $extraData .= " data-refby=\"$refby\"";
        $extraData .= " data-refbyUsername=\"$refbyUsername\"";
        $extraData .= " data-lpaid=\"" . @$user->userExtra->paid_left . "\"";
        $extraData .= " data-rpaid=\"" . @$user->userExtra->paid_right . "\"";
        $extraData .= " data-lfree=\"" . @$user->userExtra->free_left . "\"";
        $extraData .= " data-rfree=\"" . @$user->userExtra->free_right . "\"";
        $extraData .= " data-lbv=\"" . getAmount(@$user->userExtra->bv_left) . "\"";
        $extraData .= " data-rbv=\"" . getAmount(@$user->userExtra->bv_right) . "\"";

        $res .= "<div class=\"user showDetails\" type=\"button\" $extraData>";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"$userType\">";
        $res .= "<p class=\"user-name\">$user->username</p>";

    } else {
        $img = getImage('assets/images/user/profile/', '120x120');

        $res .= "<div class=\"user showDetailsnew\" type=\"button\">";
        $res .= "<img src=\"$img\" alt=\"*\"  class=\"no-user\">";
        $res .= "<p class=\"user-name\">No user</p>";
    }

    $res .= " </div>";
    $res .= " <span class=\"line\"></span>";

    return $res;

}

/*
===============TREE AUTH==============
*/
function treeAuth($whichID, $whoID){

    if($whichID==$whoID){
        return true;
    }
    $formid = $whichID;
    while($whichID!=""||$whichID!="0"){
        if(isUserExists($whichID)){
            $posid = getPositionId($whichID);
            if($posid=="0"){
                break;
            }
            $position = getPositionLocation($whichID);
            if($posid==$whoID){
                return true;
            }
            $whichID = $posid;
        } else {
            break;
        }
    }
    return 0;
}
function snakeCase($string,$replace=null)
{
    $string=strtolower(trim(str_replace(' ','_',$string)));
    if($replace){
         $string= str_replace($replace,' ',$string);
         $string= str_replace("&",'and',$string);

    }
    return $string;
}

function displayRating($val)
{
    $result = '';
    for($i=0; $i<intval($val); $i++){
        $result .= '<i class="la la-star text--warning"></i>';
    }
    if(fmod($val, 1)==0.5){
        $i++;
        $result .='<i class="las la-star-half-alt text--warning"></i>';
    }
    for($k=0; $k<5-$i ; $k++){
        $result .= '<i class="lar la-star text--warning"></i>';
    }
    return $result;


}

function fbcomment()
{
    $comment = Extension::where('act', 'fb-comment')->where('status',1)->first();
    return  $comment ? $comment->generateScript() : '';
}


function getSeocontent($product){
    $data['description'] = $product->meta_description;
    $data['title'] = $product->meta_title;
    $data['keywords'] = is_array($product->meta_keyword) ? implode(',',$product->meta_keyword) : '';
    $data['image'] = getImage(imagePath()['products']['path'].'/'.$product->thumbnail,imagePath()['products']['size']);
    $data['social_image_size'] = imagePath()['products']['size'];
    return $data;
}

function level($user_id, $loop) {
    $parent_user = User::find($user_id);
    
    if($user_id >=  auth()->user()->id ){
        if(!empty($parent_user->pos_id)){
            $loop++;
            
           level($parent_user->pos_id, $loop);
        }
    } elseif($user_id < auth()->user()->id ){
        echo --$loop.' ';
         }
   
}
function join_the_entry($user_id) {
    // echo " join_the_entry -".$user_id."<br>";
    pay_matching_bonus($user_id, 0);
}

function is_matching_bonus_can_be_paid_to_team($pos_id) {
    // echo " team matching_bonus -".$pos_id."<br>";
    $check_parent = User::where('pos_id',$pos_id)->where('plan_id', 1)->count();
    if ($check_parent == 2){ return true; }else{ return false; }
}

function pay_matching_bonus($user_id, $loop) {
    // $parent_user = get_parent_user_data($user_id);
    $parent_user = User::find($user_id);
    // echo "$parent_user->id:-".$parent_user->id." $parent_user->pos_id:-".$parent_user->pos_id." pay_matching_bonus User id:-".$user_id." --, ".$loop." ppy ";
    // echo "check = ".is_upliner_eligible_for_payment($parent_user)." pp ";
    if(is_upliner_eligible_for_payment($parent_user)) {
    // echo "is_upliner_eligible_for_payment --, ";
        // add_matching_bonus_in_user_wallet($parent_user->id);
        add_matching_bonus_in_user_wallet_extra($parent_user->id);
    }

    $loop = $loop + 1;
    if($loop < 102){
        if(!empty($parent_user->pos_id)){pay_matching_bonus($parent_user->pos_id, $loop);}
    }else{
        return false;
    }

}

function is_upliner_eligible_for_payment($user_data) {
// return true if the user is not a free member
    return ($user_data->plan_id != 0);
}

function add_matching_bonus_in_user_wallet_extra($user_id) {
    $bonus200_74per = (200 * 74)/100;
    $bonus200_26per = (200 * 26)/100;
    $chk_min = 0;

    $extra = UserExtra::where('user_id', $user_id)->first();
    if (($extra->pair_bonus < 1025)) {
// echo " add_matching_bonus_1025 -".$user_id."<br>";
        if ($extra->paid_left < $extra->paid_right) {
            $chk_min = $extra->paid_left;
        }else{
            $chk_min = $extra->paid_right;
        }
// echo " before extra check -".$chk_min."<br>";
        if ($extra->pair_bonus >= $chk_min) {
            return;
        }
// echo " after extra check -".$chk_min."<br>";

        $extra->pair_bonus += 1;
        $extra->save();

        $PairBonus_parent = User::find($user_id);
        $PairBonus_username = $PairBonus_parent->username;
        // echo "PairBonus_username: ".$PairBonus_username."<br>";
        // return;
        if (str_contains($PairBonus_username, 'dsp')) { 
            // echo 'true'; die();
            $sponser = User::find($PairBonus_parent->ref_id);
            $sponser->balance += $bonus200_74per;
            $sponser->epin_credit += $bonus200_26per;
            $sponser->save();

            $trxn = new Transaction();
            $trxn->user_id = $sponser->id;
            $trxn->amount = 200;
            $trxn->trx_type = '+';
            $trxn->details = 'Pair Bonus Credit '.$PairBonus_parent->username;
            $trxn->remark = 'Pair bonus add on pair complete '.$PairBonus_parent->username;
            $trxn->trx = getTrx();
            $trxn->post_balance = getAmount($sponser->balance);
            $trxn->save();
        }else{
            // echo "else";die();
            $PairBonus_parent->balance += $bonus200_74per;
            $PairBonus_parent->epin_credit += $bonus200_26per;
            $PairBonus_parent->save();

            $trxn = new Transaction();
            $trxn->user_id = $PairBonus_parent->id;
            $trxn->amount = 200;
            $trxn->trx_type = '+';
            $trxn->details = 'Pair Bonus Credit '.$PairBonus_parent->username;
            $trxn->remark = 'Pair bonus add on pair complete '.$PairBonus_parent->username;
            $trxn->trx = getTrx();
            $trxn->post_balance = getAmount($PairBonus_parent->balance);
            $trxn->save();
        }
    }
}

// function add_matching_bonus_in_user_wallet($user_id) {
//     $bonus200_74per = (200 * 74)/100;
//     $bonus200_26per = (200 * 26)/100;

//     $PairBonus_parent = User::find($user_id);
//     $PairBonus_parent->balance += $bonus200_74per;
//     $PairBonus_parent->epin_credit += $bonus200_26per;
//     $PairBonus_parent->save();

//     $trxn = new Transaction();
//     $trxn->user_id = $PairBonus_parent->id;
//     $trxn->amount = 200;
//     $trxn->trx_type = '+';
//     $trxn->details = 'Pair Bonus Credit';
//     $trxn->remark = 'Pair bonus add on pair complete';
//     $trxn->trx = getTrx();
//     $trxn->post_balance = getAmount($PairBonus_parent->balance);
//     $trxn->save();
// }


// function check_user_extra_matching_bonus() {
//     // get the data of the parent
//     // $parent_user = get_parent_user_data($user_id);
//     $parent_user = User::find($user_id);
//     if(is_upliner_eligible_for_payment($parent_user)) {
//         add_matching_bonus_in_user_wallet($parent_user->id);
//     }

//     $loop += $loop + 1;
//     if($loop < 12){
//         if(!empty($parent_user->pos_id)){pay_matching_bonus($parent_user->pos_id, $loop);}
//     }else{
//         return false;
//     }

// }



function getRefUsername($ref_id){
    $user = User::where('id', $ref_id)->first();
    return $user->username;
}
function getRefFullname($ref_id){
    $user = User::where('id', $ref_id)->first();
    return $user->firstname.' '.$user->lastname;
}

function getUpperUsername($position){
    $join_under = User::where('id', $position)->first();
    return $join_under->username;
}
function getUpperFullname($position){
    $join_under = User::where('id', $position)->first();
    if($join_under->firstname == null){
        
    return 0;
    }
    return $join_under->firstname.' '.$join_under->lastname;
}
function getUpperId($position){
    $join_under = User::where('id', $position)->first();
    return $join_under->id;
}
function getDPSFullname($id){
    $main_user_id = User::where('id', $id)->first();
    $main_user = User::where('id', $main_user_id->ref_id)->first();
    return $main_user->firstname.' '.$main_user->lastname."'s dsp";
}

function admin_wallet_distribution(){
    $admin__wallet_pre = DB::table('admin_wallets')->where('id',1)->first();
        DB::table('admin_wallets')->where('id',1)->update([
            'dSPBalance'=>$admin__wallet_pre->dSPBalance += 200,
            'directReferenceBalance'=>$admin__wallet_pre->directReferenceBalance += 500,
            'rewardBalance'=>$admin__wallet_pre->rewardBalance += 700,
            'pairBalance'=>$admin__wallet_pre->pairBalance += 1100,
            'weeklyPromoBalance'=>$admin__wallet_pre->weeklyPromoBalance += 200,
            'e_PinStockistBalance'=>$admin__wallet_pre->e_PinStockistBalance += 300,
            'pintaPayBalance'=>$admin__wallet_pre->pintaPayBalance += 300,
            'productsBalance'=>$admin__wallet_pre->productsBalance += 1500,
            'shippingBalance'=>$admin__wallet_pre->shippingBalance += 500,
            'companyBalance'=>$admin__wallet_pre->companyBalance += 600,
            'eventBalance'=>$admin__wallet_pre->eventBalance += 100,
            'samiExpenseBalance'=>$admin__wallet_pre->samiExpenseBalance += 50 ,
            'officeBalance'=>$admin__wallet_pre->officeBalance += 300,
            'visitOutsideBalance'=>$admin__wallet_pre->visitOutsideBalance += 300,
            'iTBalance'=>$admin__wallet_pre->iTBalance += 25,
            'extraBalance'=>$admin__wallet_pre->extraBalance += 125,
            'company_wallet'=>$admin__wallet_pre->company_wallet += 600,
            'users_wallet'=>$admin__wallet_pre->users_wallet += 6200,
            'total_collection'=>$admin__wallet_pre->total_collection += 6800,
       
        ]);
}