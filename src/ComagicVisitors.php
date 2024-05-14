<?php
namespace App;
use Exception;

require_once(__DIR__ . "/../vendor/autoload.php");


use PDO;

// $input = file_get_contents("php://input");
// $cv = new ComagicVisitors($input);

/**
 * Обработка уведомлений Comagic о звонках
 *
 * @property PDO   $pdo Клиент БД
 * @property array $json Тело запроса полученного от Comagic
 *
 * @method __construct        Конструктор класса
 *
 * @method databaseConnection Проверка доменного имени сайта
 *                            и подключение к соответствующей БД
 *
 * @method callDataProcessing
 */
class ComagicVisitors
{
    protected array $json;
    protected PDO $pdo;
    protected string $suffix;

    /**
     * Конструктор класса. Преобразует тело запроса в массив
     * и запускает основные методы
     *
     * @param string $json Тело запроса полученное от Сomagic
     */
    public function __construct(string $json)
    {
        $this->json = json_decode($json, true);
        $this->domainIdentification();
        $this->createTable();
        $this->callDataProcessing();
    }

    protected function domainIdentification(): void
    {
        switch ($this->json['site_domain_name']) {
            case "hy-lok.ru":
                $this->suffix = "HY";
                break;
            case "hylok.ru":
                $this->suffix = "HL";
                break;
            case "swagelok.su":
                $this->suffix = "SW";
                break;
            case "www.camozzi.ru.net":
                $this->suffix = "CZ";
                break;
            case "wika-manometry.ru":
                $this->suffix = "WM";
                break;
            default:
                new \Exception("Ошибка: Неверное доменное имя");
        }
        file_put_contents("php://output", "Work in process");
        $this->databaseConnection($this->suffix);
    }

    protected function databaseConnection(string $suffix): void
    {
        $this->pdo = new PDO(
            'mysql:host=' . $_ENV['DB_HOST_' . $suffix] . ';dbname=' . $_ENV['DB_DATABASE_' . $suffix],
            $_ENV['DB_USERNAME_' . $suffix],
            $_ENV['DB_PASSWORD_' . $suffix]
        );
    }

    protected function callDataProcessing(): void
    {
        if (
            array_key_exists("call_source", $this->json) ||
            array_key_exists("direction", $this->json) ||
            array_key_exists("talk_time_duration", $this->json) ||
            array_key_exists("total_time_duration", $this->json) ||
            array_key_exists("wait_time_duration", $this->json) ||
            array_key_exists("tag_names", $this->json) ||
            array_key_exists("is_lost", $this->json)
        ) {
            $this->droppedCallProcess();
        } else {
            $this->incomingCallProcess();
        }
    }

    protected function createTable(): void
    {
        $sql = file_get_contents(__DIR__ . "/../sql/createTable.sql");
        $this->pdo->query($sql);
    }

    protected function droppedCallProcess(): void
    {
        $sql = file_get_contents(__DIR__ . "/../sql/droppedCall.sql");
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("id", 0, PDO::PARAM_INT);
        $stmt->bindValue("client_id", $this->getClientId(), PDO::PARAM_INT);
        $stmt->bindValue("scenario", "dropped", PDO::PARAM_STR);
        $stmt->bindValue("notification_name", $this->json['notification_name'], PDO::PARAM_STR);
        $stmt->bindValue("virtual_phone_number", $this->json['virtual_phone_number'], PDO::PARAM_STR);
        $stmt->bindValue("notification_time", $this->json['notification_time'], PDO::PARAM_STR);
        $stmt->bindValue("advertising_campaign", $this->json['advertising_campaign'], PDO::PARAM_STR);
        $stmt->bindValue("contact_phone_number", $this->json['contact_phone_number'], PDO::PARAM_STR);
        $stmt->bindValue("visitor_id", $this->json['visitor_id'], PDO::PARAM_STR);
        $stmt->bindValue("search_query", $this->json['search_query'], PDO::PARAM_STR);
        $stmt->bindValue("communication_number", $this->json['communication_number'], PDO::PARAM_STR);
        $stmt->bindValue("visitor_is_new", $this->json['visitor_is_new'], PDO::PARAM_BOOL);
        $stmt->bindValue("search_engine", $this->json['search_engine'], PDO::PARAM_STR);
        $stmt->bindValue("call_session_id", $this->json['call_session_id'], PDO::PARAM_STR);
        $stmt->bindValue("call_source", $this->json['call_source'], PDO::PARAM_STR);
        $stmt->bindValue("direction", $this->json['direction'], PDO::PARAM_STR);
        $stmt->bindValue("talk_time_duration", $this->json['talk_time_duration'], PDO::PARAM_INT);
        $stmt->bindValue("total_time_duration", $this->json['total_time_duration'], PDO::PARAM_INT);
        $stmt->bindValue("wait_time_duration", $this->json['wait_time_duration'], PDO::PARAM_INT);
        $stmt->bindValue("is_lost", $this->json['is_lost'], PDO::PARAM_BOOL);
        $stmt->execute();
    }

    protected function incomingCallProcess(): void
    {
        $sql = file_get_contents(__DIR__ . "/../sql/incomingCall.sql");
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("id", 0, PDO::PARAM_INT);
        $stmt->bindValue("client_id", $this->getClientId(), PDO::PARAM_INT);
        $stmt->bindValue("scenario", "income", PDO::PARAM_STR);
        $stmt->bindValue("notification_name", $this->json['notification_name'], PDO::PARAM_STR);
        $stmt->bindValue("virtual_phone_number", $this->json['virtual_phone_number'], PDO::PARAM_STR);
        $stmt->bindValue("notification_time", $this->json['notification_time'], PDO::PARAM_STR);
        $stmt->bindValue("advertising_campaign", $this->json['advertising_campaign'], PDO::PARAM_STR);
        $stmt->bindValue("contact_phone_number", $this->json['contact_phone_number'], PDO::PARAM_STR);
        $stmt->bindValue("visitor_id", $this->json['visitor_id'], PDO::PARAM_STR);
        $stmt->bindValue("search_query", $this->json['search_query'], PDO::PARAM_STR);
        $stmt->bindValue("communication_number", $this->json['communication_number'], PDO::PARAM_STR);
        $stmt->bindValue("visitor_is_new", $this->json['visitor_is_new'], PDO::PARAM_BOOL);
        $stmt->bindValue("search_engine", $this->json['search_engine'], PDO::PARAM_STR);
        $stmt->bindValue("call_session_id", $this->json['call_session_id'], PDO::PARAM_STR);
        $stmt->bindValue("call_source", null, PDO::PARAM_NULL);
        $stmt->bindValue("direction", null, PDO::PARAM_NULL);
        $stmt->bindValue("talk_time_duration", null, PDO::PARAM_NULL);
        $stmt->bindValue("total_time_duration", null, PDO::PARAM_NULL);
        $stmt->bindValue("wait_time_duration", null, PDO::PARAM_NULL);
        $stmt->bindValue("is_lost", null, PDO::PARAM_NULL);
        $stmt->execute();
    }

    protected function getClientId(): int
    {
        $sql = "SELECT `id` FROM `visitors_info`
            WHERE `client_id` = :cid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue("cid", $this->json['client_id']);
        $stmt->execute();
        $response = $stmt->fetch();
        if ($response) {
            return (int) $response;
        }
        return 0;
    }
}
