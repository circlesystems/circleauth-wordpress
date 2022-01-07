<?php

function toArrayObj($result)
{
    $array = json_decode($result, true);

    return $array;
}

function generateDataSignature($data, $key)
{
    $s = hash_hmac('sha256', $data, $key, true);

    return base64_encode($s);
}

function validateSignature($data, $signature)
{
    $s = generateDataSignature($data, $GLOBALS['api_readKey']);
    if ($signature === $s) {
        return true;
    }

    error_log("Data '$data' has invalid signature '$signature'");

    return false;
}

function validateJSONSignature($jsonData)
{
    $data = $jsonData['data'];
    $signature = $jsonData['signature'];
    if ($data && $signature) {
        $data = json_encode($data, JSON_UNESCAPED_SLASHES);

        return validateSignature($data, $signature);
    }

    return false;
}

function validateReceivedQueryString()
{
    return true;
}

function get2FAReturnURL($path)
{
    $urlReturn = $_SERVER['HTTP_REFERER'];
    $urlReturn = substr($urlReturn, 0, strrpos($urlReturn, '/')); //remove file
    $urlReturn = substr($urlReturn, 0, strrpos($urlReturn, '/')); //remove dir
    $urlReturn = $urlReturn.$path;

    return $urlReturn;
}

function increaseCountChanges()
{
    $countChange = time();
    $_SESSION['count_changes'] = $countChange;

    return $countChange;
}

function getCountChange()
{
    $countChange = $_SESSION['count_changes'];
    if ($countChange) {
        return intval($countChange);
    }

    return time();
}

function sendApiCircleAuth($functionName, $method, $params, $appendFinalUrl = null)
{
    return sendApiCircleAuthForApp($GLOBALS['api_appKey'], $GLOBALS['api_writeKey'], $functionName, $method, $params, $appendFinalUrl);
}

function sendApiCircleAuthForApp($appKey, $writeKey, $functionName, $method, $params, $appendFinalUrl = null)
{
    $url = CIRCLEAUTH_DOMAIN.'api/'.$functionName;

    $context = [
        'http' => [
            'method' => $method,
            'header' => [
                'Accept-Encoding: gzip, deflate, br',
                'Content-Type: application/json',
                "x-ua-appKey: $appKey",
            ],
            'ignore_errors' => true,
        ],
    ];

    if ($method === 'GET') {
        $urlParams = '?'.urldecode(http_build_query($params));
        $signature = generateDataSignature($urlParams, $writeKey);

        $url = $url.$urlParams.'&signature='.$signature;

        if ($appendFinalUrl) {
            $url = $url.$appendFinalUrl;
        }
    } elseif ($method === 'POST') {
        $signature = generateDataSignature(json_encode($params, JSON_UNESCAPED_SLASHES), $writeKey);
        $signed = [
            'data' => $params,
            'signature' => $signature,
        ];
        $context['http']['content'] = json_encode($signed, JSON_UNESCAPED_SLASHES);
    } else {
        return '{"error": "Method not allowed."}';
    }
    $context = stream_context_create($context);
    $result = file_get_contents($url, false, $context);

    $obj = toArrayObj($result);

    if ($obj && (isset($obj['error']))) {
        logDev("Error at $url: ".$obj['error']);
    }

    return $obj;
}

function getUser($userID)
{
    $param = [
        'userID' => $userID,
    ];
    $result = sendApiCircleAuth('user/get', 'GET', $param);

    return $result;
}

function getUserSession($sessionID, $userID)
{
    $param = [
        'sessionID' => $sessionID,
        'userID' => $userID,
    ];

    $result = sendApiCircleAuth('user/session', 'GET', $param);

    return $result;
}

function validateUserSession($sessionID, $userID)
{
    if ($sessionID && $userID) {
        $userSession = getUserSession($sessionID, $userID);

        if ($userSession && $userSession['data'] && validateJSONSignature($userSession)) {
            $data = $userSession['data'];

            return $data['userID'] == $userID
                && $data['sessionID'] == $sessionID
                && ($data['status'] == 'usedOnce' || $data['status'] == 'active');
        }
    }

    return false;
}

function expireUserSession($sessionID, $userID)
{
    if (!$sessionID || !$userID) {
        return null;
    }
    $param = [
        'sessionID' => $sessionID,
        'userID' => $userID,
    ];
    $result = sendApiCircleAuth('user/session/expire', 'POST', $param);

    return $result;
}

function getSession($sessionID)
{
    $param = [
        's' => $sessionID,
    ];
    $result = sendApiCircleAuth('session/', 'GET', $param);

    return $result;
}

function createPermissionInvitation($fieldsToSave)
{
    $result = sendApiCircleAuth('app/invite/create', 'POST', $fieldsToSave);
    increaseCountChanges();

    return $result;
}

function createNew2FAForApp($appID, $writeKey, $userID, $returnURL, $question, $customData, $email = null, $webhookURL = null)
{
    increaseCountChanges();
    $fieldsToCreate = [
        'userID' => $userID,
        'email' => $email,
        'returnUrl' => $returnURL,
        'question' => $question,
        'customID' => $customData,
    ];
    if ($webhookURL) {
        $fieldsToCreate['webhookUrl'] = $webhookURL;
    }

    $result = sendApiCircleAuthForApp($appID, $writeKey, '', '2fa/create', 'POST', $fieldsToCreate);

    return $result;
}
