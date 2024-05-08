<?php
namespace App;

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
            case "camozzi.ru.net":
                $this->suffix = "CZ";
                break;
            case "wika-manometry.ru":
                $this->suffix = "WM";
                break;
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

    }

    protected function createTable(): void
    {
        $sql = file_get_contents(__DIR__ . "/../sql/createTable.sql");
        $this->pdo->query($sql);
    }
}
