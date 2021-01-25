<?php

namespace app\components;

use Yii;
use app\models\db\Admin;
use app\models\db\Report;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use yii\helpers\Json;

//use app\models\offer\Offer;

class WebSocketServer implements MessageComponentInterface
{

    /**
     * @var SplObjectStorage
     */
    protected $clients;

    protected $lockedReports = [];

    private function getAdminName($adminId)
    {
        if (!$adminId || $adminId === null) {
            return '';
        } else {
            $adminInfo = Admin::findOne($adminId);
            return $adminInfo->getFullName();
        }
    }

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        //$this->eventHandlers = new OfferEditorEventHandler($this);
        echo "Running\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";

        // initialize client
        $this->sendInit($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        $data = json_decode($msg, true);
        if ($data['event'] != 'heartbeat') {
            echo sprintf(
                'Connection %d sending message "%s" to %d other connection%s' . "\n",
                $from->resourceId,
                $msg,
                $numRecv,
                $numRecv <= 1 ? '' : 's'
            );
        }
        $report_id = $data['reportId'];
        $admin_id = $data['adminId'];
        $from->reportId = $report_id;
        $from->adminId = $admin_id;

        if (in_array($data['event'], ['lock', 'unlock'])) {
            $reportProccessingStart = microtime(true);
            if ($data['event'] == 'lock' && !empty($report_id) && !empty($admin_id)) {
                $this->lock($report_id, $admin_id, $from);
            } elseif ($data['event'] == 'unlock' && !empty($report_id) && !empty($admin_id)) {
                $this->unlock($report_id, $admin_id, $from);
            }
            echo 'Response sent after ' . (microtime(true) - $reportProccessingStart) . ' seconds.' . PHP_EOL;
        } else {
            $from->send(json_encode(['event' => 'error', 'msg' => 'Invalid event']));
        }
    }

    public function sendMessage($msg, $reportId, $adminId, ConnectionInterface $except = null)
    {
        foreach ($this->clients as $client) {
            if (!$except || $except !== $client) {
                echo sprintf(
                    'Connection %d sending message "%s" on %d' . PHP_EOL,
                    $client->resourceId,
                    $msg,
                    $reportId
                );
                $client->send(
                    Json::encode([
                        'event' => $msg,
                        'reportId' => $reportId,
                        'adminId' => $adminId,
                        'adminName' => $this->getAdminName($adminId),
                    ])
                );
            }
        }
    }

    public function sendInit(ConnectionInterface $to)
    {
        $lockedReports = [];
        foreach ($this->lockedReports as $reportId => $adminId) {
            $lockedReports[] = [
                'reportId' => $reportId,
                'adminId' => $adminId,
                'adminName' => $this->getAdminName($adminId),
            ];
        }

        $msg = Json::encode([
            'event' => 'init',
            'lockedReports' => $lockedReports,
        ]);

        echo sprintf(
            'Sending initial lock table to connection %d: "%s"' . PHP_EOL,
            $to->resourceId,
            $msg
        );
        $to->send($msg);
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";

        $this->unlock($conn->reportId, $conn->adminId);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    public function getClients()
    {
        return $this->clients;
    }

    protected function lock($report_id, $admin_id, ConnectionInterface $except = null)
    {
        $report_id_valid = Report::find()->where(['id' => $report_id])->exists();
        $admin_id_valid = Admin::find()->where(['id' => $admin_id])->exists();
        if (!array_key_exists($report_id, $this->lockedReports) && $report_id_valid && $admin_id_valid) {
            $this->lockedReports[$report_id] = $admin_id;
            $this->sendMessage('lock', $report_id, $admin_id, $except);
        }
    }

    protected function unlock($report_id, $admin_id, ConnectionInterface $except = null)
    {
        if (array_key_exists($report_id, $this->lockedReports) && $this->lockedReports[$report_id] == $admin_id) {
            unset($this->lockedReports[$report_id]);
            $this->sendMessage('unlock', $report_id, $admin_id, $except);
        }
    }

    protected function sendResult($from, $success, $message = '', $primaryMessateType = false)
    {
        $msg = [
            'event' => $success ? 'saveSuccess' : 'error',
            'params' => [
                'msg' => $success && empty($message) ? Yii::t('app/reportEditor', 'report_editor_saved') : $message,
            ],
        ];
        if ($primaryMessateType) {
            $msg['params']['msgType'] = 'primary';
        }
        $from->send(Json::htmlEncode($msg));
    }
}
