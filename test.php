<?php
// 确保 Swoole 扩展已启用
if (!extension_loaded('swoole')) {
    die('Please install the Swoole extension.');
}

echo "http://127.0.1:9501";
// 创建一个 HTTP 服务器
$serv = new Swoole\Http\Server("127.0.0.1", 9501);

// 设置服务器参数
$serv->set([
    'worker_num' => 4, // 设置工作进程数量
    'enable_coroutine' => true, // 启用协程
    'task_worker_num' => 20, // 设置协程工作进程的数量
]);

// 监听 HTTP 请求
$serv->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
    go(function () use ($request, $response) { // 使用 go 函数启动一个协程
        $testI = 0;
        while ($testI < 10000) {
            go(function () use ($request, $response, &$testI) {
                $testI++;
                $testI++;
                $testI++;
            });
        }
        $testI = 0;
        while ($testI < 10000) {
            go(function () use ($request, $response, &$testI) {
                $testI++;
                $testI++;
                $testI++;
            });
        }

        $response->header("Content-Type", "text/html; charset=utf-8");
        $response->end("<h1>Hello Swoole Coroutine!</h1>");
    });
});

// 设置 onTask 事件处理函数
$serv->on('task', function (Swoole\Server $serv, $taskId, $fromWorkerId, $data) {
    // 处理任务数据
    echo "Task: $taskId\n";
    // 返回结果
    return $data;
});

// 设置 onFinish 事件处理函数
$serv->on('finish', function (Swoole\Server $serv, $taskId, $data) {
    echo "Finish: $taskId, data: $data\n";
});
// 启动服务器
$serv->start();

