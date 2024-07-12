<?php
//headser all cors
$swooleResponse->header('Access-Control-Allow-Origin', '*');
$swooleResponse->header('Access-Control-Allow-Headers', $swooleRequest->header['access-control-request-headers'] ?? 'Content-Type, authorization, Accept');
$swooleResponse->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
$swooleResponse->header('Content-Type', 'application/json');
$swooleResponse->header('Authorization', '*');
//Access-Control-Allow-Credentials
$swooleResponse->header('Access-Control-Allow-Credentials', 'true');
//set swoole name server
$swooleResponse->header('Server', 'VibeCriativa');
//populate the global state with the request info
// $_SERVER['REQUEST_URI'] = $swooleRequest->server['request_uri'];
// $_SERVER['REQUEST_METHOD'] = $swooleRequest->server['request_method'];
// $_SERVER['REMOTE_ADDR'] = $swooleRequest->server['remote_addr'];
// $_SERVER['Authorization'] = $swooleRequest->header['authorization'] ?? '';
// $_SERVER['refreshtoken'] = $swooleRequest->header['refreshtoken'] ?? '';
// $_SERVER['HTTP_USER_AGENT'] = $swooleRequest->header['user-agent'] ?? '';
// $_SERVER['HTTP_ACCEPT'] = $swooleRequest->header['accept'] ?? '';
// $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $swooleRequest->header['accept-language'] ?? '';
// $_SERVER['HTTP_ACCEPT_ENCODING'] = $swooleRequest->header['accept-encoding'] ?? '';
// $_SERVER['HTTP_CONNECTION'] = $swooleRequest->header['connection'] ?? '';
// $_SERVER['HTTP_HOST'] = $swooleRequest->header['host'] ?? '';
// $_SERVER['HTTP_REFERER'] = $swooleRequest->header['referer'] ?? '';
// $_SERVER['HTTP_ORIGIN'] = $swooleRequest->header['origin'] ?? '';
// $_SERVER['HTTP_SEC_FETCH_SITE'] = $swooleRequest->header['sec-fetch-site'] ?? '';
// $_SERVER['HTTP_SEC_FETCH_MODE'] = $swooleRequest->header['sec-fetch-mode'] ?? '';
// $_SERVER['HTTP_SEC_FETCH_DEST'] = $swooleRequest->header['sec-fetch-dest'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA'] = $swooleRequest->header['sec-ch-ua'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA_MOBILE'] = $swooleRequest->header['sec-ch-ua-mobile'] ?? '';
// $_SERVER['HTTP_SEC_FETCH_USER'] = $swooleRequest->header['sec-fetch-user'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] = $swooleRequest->header['sec-ch-ua-platform'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA_ARCH'] = $swooleRequest->header['sec-ch-ua-arch'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA_MODEL'] = $swooleRequest->header['sec-ch-ua-model'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA_PLATFORM_VERSION'] = $swooleRequest->header['sec-ch-ua-platform-version'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA_FULL_VERSION'] = $swooleRequest->header['sec-ch-ua-full-version'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA_PLATFORM_VERSION'] = $swooleRequest->header['sec-ch-ua-platform-version'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA_FULL_VERSION'] = $swooleRequest->header['sec-ch-ua-full-version'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA_ARCH'] = $swooleRequest->header['sec-ch-ua-arch'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA_MODEL'] = $swooleRequest->header['sec-ch-ua-model'] ?? '';
// $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] = $swooleRequest->header['sec-ch-ua-platform'] ?? '';
//cloudflare ip client
// $_SERVER['HTTP_CF_CONNECTING_IP'] = $swooleRequest->header['cf-connecting-ip'] ?? '';
// $_SERVER['HTTP_CF_IPCOUNTRY'] = $swooleRequest->header['cf-ipcountry'] ?? '';
// $_SERVER['HTTP_CF_RAY'] = $swooleRequest->header['cf-ray'] ?? '';
// $_SERVER['HTTP_CF_VISITOR'] = $swooleRequest->header['cf-visitor'] ?? '';
// $_SERVER['HTTP_CF_WARP_TAG'] = $swooleRequest->header['cf-warp-tag'] ?? '';
// $_SERVER['HTTP_CF_WARP_ZONE'] = $swooleRequest->header['cf-warp-zone'] ?? '';
// $_SERVER['HTTP_CLIENT_IP'] = $swooleRequest->header['client-ip'] ?? '';
// $_SERVER['HTTP_X_FORWARDED_FOR'] = $swooleRequest->header['x-forwarded-for'] ?? '';
// $_SERVER['PROXY_REMOTE_ADDR'] = $swooleRequest->header['proxy-remote-addr'] ?? '';
// $_SERVER['HTTP_X_REAL_IP'] = $swooleRequest->header['x-real-ip'] ?? '';

//global var
$_GET = $swooleRequest->get ?? [];
$_POST = $swooleRequest->post ?? $swooleRequest->rawContent();
$_FILES = $swooleRequest->files ?? $swooleRequest->rawContent();
$_COOKIE = $swooleRequest->cookie ?? [];